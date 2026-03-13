<?php

declare(strict_types=1);

function cms_languages(): array
{
    return cms_db()->query('SELECT * FROM languages WHERE is_active = 1 ORDER BY sort_order ASC, id ASC')->fetchAll();
}

function cms_default_language(): array
{
    $language = cms_db()->query('SELECT * FROM languages WHERE is_default = 1 LIMIT 1')->fetch();

    if (!$language) {
        throw new RuntimeException('No default language configured.');
    }

    return $language;
}

function cms_resolve_language(?string $code): array
{
    $code = strtolower(trim((string) $code));

    if ($code !== '') {
        $stmt = cms_db()->prepare('SELECT * FROM languages WHERE code = ? AND is_active = 1 LIMIT 1');
        $stmt->execute([$code]);
        $language = $stmt->fetch();
        if ($language) {
            return $language;
        }
    }

    return cms_default_language();
}

function cms_admin_pages(): array
{
    $defaultLanguage = cms_default_language();
    $stmt = cms_db()->prepare(
        'SELECT p.*, pt.page_name, pt.nav_label
         FROM pages p
         LEFT JOIN page_translations pt ON pt.page_id = p.id AND pt.language_id = ?
         ORDER BY p.sort_order ASC, p.id ASC'
    );
    $stmt->execute([(int) $defaultLanguage['id']]);
    return $stmt->fetchAll();
}

function cms_admin_page(?int $pageId): ?array
{
    if (!$pageId) {
        return null;
    }

    $stmt = cms_db()->prepare('SELECT * FROM pages WHERE id = ? LIMIT 1');
    $stmt->execute([$pageId]);
    $page = $stmt->fetch();

    if (!$page) {
        return null;
    }

    $translationsStmt = cms_db()->prepare(
        'SELECT pt.*, l.code AS language_code
         FROM page_translations pt
         INNER JOIN languages l ON l.id = pt.language_id
         WHERE pt.page_id = ?'
    );
    $translationsStmt->execute([$pageId]);

    $translations = [];
    foreach ($translationsStmt->fetchAll() as $row) {
        $translations[$row['language_code']] = $row;
    }

    $page['translations'] = $translations;
    $page['modules'] = cms_admin_page_modules((int) $page['id']);

    return $page;
}

function cms_admin_page_modules(int $pageId): array
{
    $stmt = cms_db()->prepare(
        'SELECT pm.*, m.module_key, m.module_type
         FROM page_modules pm
         INNER JOIN modules m ON m.id = pm.module_id
         WHERE pm.page_id = ?
         ORDER BY pm.sort_order ASC, pm.id ASC'
    );
    $stmt->execute([$pageId]);
    return $stmt->fetchAll();
}

function cms_admin_modules(): array
{
    $defaultLanguage = cms_default_language();
    $stmt = cms_db()->prepare(
        'SELECT m.*, mt.title
         FROM modules m
         LEFT JOIN module_translations mt ON mt.module_id = m.id AND mt.language_id = ?
         ORDER BY m.id ASC'
    );
    $stmt->execute([(int) $defaultLanguage['id']]);
    return $stmt->fetchAll();
}

function cms_admin_module(?int $moduleId): ?array
{
    if (!$moduleId) {
        return null;
    }

    $stmt = cms_db()->prepare('SELECT * FROM modules WHERE id = ? LIMIT 1');
    $stmt->execute([$moduleId]);
    $module = $stmt->fetch();

    if (!$module) {
        return null;
    }

    $translationsStmt = cms_db()->prepare(
        'SELECT mt.*, l.code AS language_code
         FROM module_translations mt
         INNER JOIN languages l ON l.id = mt.language_id
         WHERE mt.module_id = ?'
    );
    $translationsStmt->execute([$moduleId]);

    $translations = [];
    foreach ($translationsStmt->fetchAll() as $row) {
        $translations[$row['language_code']] = $row;
    }

    $module['translations'] = $translations;
    return $module;
}

function cms_admin_menus(): array
{
    $stmt = cms_db()->query(
        'SELECT m.*, COUNT(mi.id) AS item_count
         FROM menus m
         LEFT JOIN menu_items mi ON mi.menu_id = m.id
         GROUP BY m.id
         ORDER BY m.id ASC'
    );

    return $stmt->fetchAll();
}

function cms_admin_menu(?int $menuId): ?array
{
    if (!$menuId) {
        return null;
    }

    $stmt = cms_db()->prepare('SELECT * FROM menus WHERE id = ? LIMIT 1');
    $stmt->execute([$menuId]);
    $menu = $stmt->fetch();
    if (!$menu) {
        return null;
    }

    $itemsStmt = cms_db()->prepare(
        'SELECT mi.*, p.slug
         FROM menu_items mi
         LEFT JOIN pages p ON p.id = mi.page_id
         WHERE mi.menu_id = ?
         ORDER BY mi.sort_order ASC, mi.id ASC'
    );
    $itemsStmt->execute([$menuId]);
    $menu['items'] = $itemsStmt->fetchAll();

    $translationStmt = cms_db()->prepare(
        'SELECT mit.menu_item_id, mit.custom_label, l.code AS language_code
         FROM menu_item_translations mit
         INNER JOIN languages l ON l.id = mit.language_id
         WHERE mit.menu_item_id = ?'
    );

    foreach ($menu['items'] as &$item) {
        $item['translations'] = [];
        $translationStmt->execute([(int) $item['id']]);
        foreach ($translationStmt->fetchAll() as $row) {
            $item['translations'][$row['language_code']] = [
                'custom_label' => $row['custom_label'] ?? '',
            ];
        }
    }
    unset($item);

    return $menu;
}

function cms_upsert_menu(array $menuData, array $items): int
{
    $pdo = cms_db();
    $pdo->beginTransaction();

    try {
        $menuId = !empty($menuData['id']) ? (int) $menuData['id'] : null;
        $menuKey = cms_normalize_slug($menuData['menu_key'] ?? '');
        $name = cms_trimmed($menuData['name'] ?? '');

        if ($menuId) {
            $stmt = $pdo->prepare('UPDATE menus SET menu_key = ?, name = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            $stmt->execute([$menuKey, $name, $menuId]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO menus (menu_key, name) VALUES (?, ?)');
            $stmt->execute([$menuKey, $name]);
            $menuId = (int) $pdo->lastInsertId();
        }

        $languages = cms_languages();

        $pdo->prepare('DELETE FROM menu_items WHERE menu_id = ?')->execute([$menuId]);
        $itemStmt = $pdo->prepare(
            'INSERT INTO menu_items (menu_id, page_id, custom_label, custom_url, sort_order, target, is_enabled)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $translationStmt = $pdo->prepare(
            'INSERT INTO menu_item_translations (menu_item_id, language_id, custom_label)
             VALUES (?, ?, ?)'
        );

        foreach ($items as $item) {
            $pageId = (int) ($item['page_id'] ?? 0);
            $customLabel = cms_trimmed($item['custom_label'] ?? '');
            $customUrl = cms_trimmed($item['custom_url'] ?? '');
            if ($pageId <= 0 && $customUrl === '') {
                continue;
            }

            $itemStmt->execute([
                $menuId,
                $pageId > 0 ? $pageId : null,
                $customLabel,
                $customUrl,
                (int) ($item['sort_order'] ?? 100),
                cms_trimmed($item['target'] ?? '_self'),
                !empty($item['is_enabled']) ? 1 : 0,
            ]);

            $menuItemId = (int) $pdo->lastInsertId();
            $translationRows = is_array($item['translations'] ?? null) ? $item['translations'] : [];
            foreach ($languages as $language) {
                $languageCode = $language['code'];
                $translatedLabel = cms_trimmed($translationRows[$languageCode]['custom_label'] ?? '');
                if ($translatedLabel === '') {
                    continue;
                }

                $translationStmt->execute([
                    $menuItemId,
                    (int) $language['id'],
                    $translatedLabel,
                ]);
            }
        }

        $pdo->commit();
        return $menuId;
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

function cms_upsert_page(array $pageData, array $translations, array $assignments): int
{
    $pdo = cms_db();
    $pdo->beginTransaction();

    try {
        $pageId = !empty($pageData['id']) ? (int) $pageData['id'] : null;
        $slug = cms_normalize_slug($pageData['slug'] ?? '');
        $pageType = cms_trimmed($pageData['page_type'] ?? 'page');
        $templateKey = cms_trimmed($pageData['template_key'] ?? 'default');
        $status = cms_trimmed($pageData['status'] ?? 'draft');
        $sortOrder = (int) ($pageData['sort_order'] ?? 100);
        $showInNav = !empty($pageData['show_in_nav']) ? 1 : 0;

        if ($pageId) {
            $stmt = $pdo->prepare(
                'UPDATE pages SET slug = ?, page_type = ?, template_key = ?, status = ?, sort_order = ?, show_in_nav = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
            );
            $stmt->execute([$slug, $pageType, $templateKey, $status, $sortOrder, $showInNav, $pageId]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO pages (slug, page_type, template_key, status, sort_order, show_in_nav) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$slug, $pageType, $templateKey, $status, $sortOrder, $showInNav]);
            $pageId = (int) $pdo->lastInsertId();
        }

        $translationStmt = $pdo->prepare(
            'INSERT INTO page_translations
                (page_id, language_id, page_name, nav_label, seo_title, seo_description)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                page_name = VALUES(page_name),
                nav_label = VALUES(nav_label),
                seo_title = VALUES(seo_title),
                seo_description = VALUES(seo_description)'
        );

        foreach (cms_languages() as $language) {
            $code = $language['code'];
            $row = $translations[$code] ?? [];
            $translationStmt->execute([
                $pageId,
                (int) $language['id'],
                cms_trimmed($row['page_name'] ?? ''),
                cms_trimmed($row['nav_label'] ?? ''),
                cms_trimmed($row['seo_title'] ?? ''),
                cms_trimmed($row['seo_description'] ?? ''),
            ]);
        }

        $pdo->prepare('DELETE FROM page_modules WHERE page_id = ?')->execute([$pageId]);
        $assignmentStmt = $pdo->prepare(
            'INSERT INTO page_modules (page_id, module_id, region_name, sort_order, is_enabled) VALUES (?, ?, ?, ?, ?)'
        );

        foreach ($assignments as $assignment) {
            $moduleId = (int) ($assignment['module_id'] ?? 0);
            if ($moduleId <= 0) {
                continue;
            }

            $assignmentStmt->execute([
                $pageId,
                $moduleId,
                cms_trimmed($assignment['region_name'] ?? 'main'),
                (int) ($assignment['sort_order'] ?? 100),
                !empty($assignment['is_enabled']) ? 1 : 0,
            ]);
        }

        $pdo->commit();
        return $pageId;
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

function cms_upsert_module(array $moduleData, array $translations): int
{
    $pdo = cms_db();
    $pdo->beginTransaction();

    try {
        $moduleId = !empty($moduleData['id']) ? (int) $moduleData['id'] : null;
        $moduleKey = cms_normalize_slug($moduleData['module_key'] ?? '');
        $moduleType = cms_trimmed($moduleData['module_type'] ?? 'rich_text');
        $variant = cms_trimmed($moduleData['variant'] ?? 'default');
        $status = cms_trimmed($moduleData['status'] ?? 'published');
        $settingsJson = cms_trimmed($moduleData['settings_json'] ?? '{}');

        if ($moduleId) {
            $stmt = $pdo->prepare(
                'UPDATE modules SET module_key = ?, module_type = ?, variant = ?, status = ?, settings_json = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
            );
            $stmt->execute([$moduleKey, $moduleType, $variant, $status, $settingsJson, $moduleId]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO modules (module_key, module_type, variant, status, settings_json) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$moduleKey, $moduleType, $variant, $status, $settingsJson]);
            $moduleId = (int) $pdo->lastInsertId();
        }

        $translationStmt = $pdo->prepare(
            'INSERT INTO module_translations
                (module_id, language_id, title, kicker, subtitle, content_html, content_json)
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                kicker = VALUES(kicker),
                subtitle = VALUES(subtitle),
                content_html = VALUES(content_html),
                content_json = VALUES(content_json)'
        );

        foreach (cms_languages() as $language) {
            $code = $language['code'];
            $row = $translations[$code] ?? [];
            $translationStmt->execute([
                $moduleId,
                (int) $language['id'],
                cms_trimmed($row['title'] ?? ''),
                cms_trimmed($row['kicker'] ?? ''),
                cms_trimmed($row['subtitle'] ?? ''),
                (string) ($row['content_html'] ?? ''),
                cms_trimmed($row['content_json'] ?? '{}'),
            ]);
        }

        $pdo->commit();
        return $moduleId;
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

function cms_public_navigation(string $languageCode): array
{
    $menuItems = cms_public_menu('primary', $languageCode);
    if ($menuItems !== []) {
        return $menuItems;
    }

    $language = cms_resolve_language($languageCode);
    $defaultLanguage = cms_default_language();

    $stmt = cms_db()->prepare(
        'SELECT p.slug, p.page_type,
                COALESCE(NULLIF(pt_lang.page_name, ""), pt_default.page_name) AS page_name,
                COALESCE(NULLIF(pt_lang.nav_label, ""), NULLIF(pt_lang.page_name, ""), pt_default.nav_label, pt_default.page_name) AS nav_label
         FROM pages p
         LEFT JOIN page_translations pt_lang ON pt_lang.page_id = p.id AND pt_lang.language_id = ?
         LEFT JOIN page_translations pt_default ON pt_default.page_id = p.id AND pt_default.language_id = ?
         WHERE p.status = "published" AND p.show_in_nav = 1
         ORDER BY p.sort_order ASC, p.id ASC'
    );
    $stmt->execute([(int) $language['id'], (int) $defaultLanguage['id']]);
    return $stmt->fetchAll();
}

function cms_public_menu(string $menuKey, string $languageCode): array
{
    $language = cms_resolve_language($languageCode);
    $defaultLanguage = cms_default_language();

    $stmt = cms_db()->prepare(
        'SELECT mi.*, p.slug, p.page_type,
                COALESCE(NULLIF(mit_lang.custom_label, ""), NULLIF(mit_default.custom_label, ""), mi.custom_label, NULLIF(pt_lang.page_name, ""), pt_default.page_name) AS page_name,
                COALESCE(NULLIF(mit_lang.custom_label, ""), NULLIF(mit_default.custom_label, ""), mi.custom_label, NULLIF(pt_lang.nav_label, ""), NULLIF(pt_lang.page_name, ""), pt_default.nav_label, pt_default.page_name) AS nav_label
         FROM menus m
         INNER JOIN menu_items mi ON mi.menu_id = m.id
         LEFT JOIN pages p ON p.id = mi.page_id
         LEFT JOIN page_translations pt_lang ON pt_lang.page_id = p.id AND pt_lang.language_id = ?
         LEFT JOIN page_translations pt_default ON pt_default.page_id = p.id AND pt_default.language_id = ?
         LEFT JOIN menu_item_translations mit_lang ON mit_lang.menu_item_id = mi.id AND mit_lang.language_id = ?
         LEFT JOIN menu_item_translations mit_default ON mit_default.menu_item_id = mi.id AND mit_default.language_id = ?
         WHERE m.menu_key = ? AND mi.is_enabled = 1
         ORDER BY mi.sort_order ASC, mi.id ASC'
    );
    $stmt->execute([(int) $language['id'], (int) $defaultLanguage['id'], (int) $language['id'], (int) $defaultLanguage['id'], $menuKey]);

    $items = [];
    foreach ($stmt->fetchAll() as $row) {
        if (!empty($row['page_id']) && !empty($row['slug'])) {
            $href = $row['slug'] === 'home' ? '/' . $language['code'] . '/' : '/' . $language['code'] . '/' . $row['slug'];
        } else {
            $href = cms_localized_href($row['custom_url'] ?? '#', $language['code']);
        }

        $items[] = [
            'page_id' => $row['page_id'],
            'slug' => $row['slug'] ?? '',
            'page_type' => $row['page_type'] ?? '',
            'page_name' => $row['page_name'] ?? '',
            'nav_label' => $row['nav_label'] ?? '',
            'href' => $href,
            'target' => $row['target'] ?? '_self',
        ];
    }

    return $items;
}

function cms_find_public_page(string $languageCode, string $slug): ?array
{
    $language = cms_resolve_language($languageCode);
    $defaultLanguage = cms_default_language();
    $normalizedSlug = $slug === '' ? 'home' : cms_normalize_slug($slug);

    $stmt = cms_db()->prepare(
        'SELECT p.*,
                COALESCE(NULLIF(pt_lang.page_name, ""), pt_default.page_name) AS page_name,
                COALESCE(NULLIF(pt_lang.nav_label, ""), NULLIF(pt_lang.page_name, ""), pt_default.nav_label, pt_default.page_name) AS nav_label,
                COALESCE(NULLIF(pt_lang.seo_title, ""), pt_default.seo_title) AS seo_title,
                COALESCE(NULLIF(pt_lang.seo_description, ""), pt_default.seo_description) AS seo_description
         FROM pages p
         LEFT JOIN page_translations pt_lang ON pt_lang.page_id = p.id AND pt_lang.language_id = ?
         LEFT JOIN page_translations pt_default ON pt_default.page_id = p.id AND pt_default.language_id = ?
         WHERE p.slug = ? AND p.status = "published"
         LIMIT 1'
    );
    $stmt->execute([(int) $language['id'], (int) $defaultLanguage['id'], $normalizedSlug]);
    $page = $stmt->fetch();

    if (!$page) {
        return null;
    }

    $modulesStmt = cms_db()->prepare(
        'SELECT pm.region_name, pm.sort_order, pm.is_enabled,
                m.module_key, m.module_type, m.variant, m.settings_json,
                COALESCE(NULLIF(mt_lang.title, ""), mt_default.title) AS title,
                COALESCE(NULLIF(mt_lang.kicker, ""), mt_default.kicker) AS kicker,
                COALESCE(NULLIF(mt_lang.subtitle, ""), mt_default.subtitle) AS subtitle,
                COALESCE(NULLIF(mt_lang.content_html, ""), mt_default.content_html) AS content_html,
                COALESCE(NULLIF(mt_lang.content_json, ""), mt_default.content_json) AS content_json
         FROM page_modules pm
         INNER JOIN modules m ON m.id = pm.module_id
         LEFT JOIN module_translations mt_lang ON mt_lang.module_id = m.id AND mt_lang.language_id = ?
         LEFT JOIN module_translations mt_default ON mt_default.module_id = m.id AND mt_default.language_id = ?
         WHERE pm.page_id = ? AND pm.is_enabled = 1 AND m.status = "published"
         ORDER BY pm.sort_order ASC, pm.id ASC'
    );
    $modulesStmt->execute([(int) $language['id'], (int) $defaultLanguage['id'], (int) $page['id']]);

    $modules = [];
    foreach ($modulesStmt->fetchAll() as $module) {
        $module['settings'] = cms_decode_json($module['settings_json'], []);
        $module['content'] = cms_decode_json($module['content_json'], []);
        $module['language_code'] = $language['code'];
        $modules[] = $module;
    }

    $page['language'] = $language;
    $page['languages'] = cms_languages();
    $page['navigation'] = cms_public_navigation($language['code']);
    $page['footer_navigation'] = cms_public_menu('footer', $language['code']);
    $page['modules'] = $modules;
    $page['slug'] = $normalizedSlug;

    return $page;
}

function cms_admin_product_categories(): array
{
    $defaultLanguage = cms_default_language();
    $stmt = cms_db()->prepare(
        'SELECT pc.*, pct.name, pct.nav_label,
                (SELECT COUNT(*) FROM products p WHERE p.category_id = pc.id) AS product_count
         FROM product_categories pc
         LEFT JOIN product_category_translations pct ON pct.category_id = pc.id AND pct.language_id = ?
         ORDER BY pc.sort_order ASC, pc.id ASC'
    );
    $stmt->execute([(int) $defaultLanguage['id']]);
    return $stmt->fetchAll();
}

function cms_admin_product_category(?int $categoryId): ?array
{
    if (!$categoryId) {
        return null;
    }

    $stmt = cms_db()->prepare('SELECT * FROM product_categories WHERE id = ? LIMIT 1');
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch();
    if (!$category) {
        return null;
    }

    $translationsStmt = cms_db()->prepare(
        'SELECT pct.*, l.code AS language_code
         FROM product_category_translations pct
         INNER JOIN languages l ON l.id = pct.language_id
         WHERE pct.category_id = ?'
    );
    $translationsStmt->execute([$categoryId]);

    $translations = [];
    foreach ($translationsStmt->fetchAll() as $row) {
        $translations[$row['language_code']] = $row;
    }

    $category['translations'] = $translations;
    return $category;
}

function cms_upsert_product_category(array $categoryData, array $translations): int
{
    $pdo = cms_db();
    $pdo->beginTransaction();

    try {
        $categoryId = !empty($categoryData['id']) ? (int) $categoryData['id'] : null;
        $slug = cms_normalize_slug($categoryData['slug'] ?? '');
        $pageSlug = cms_normalize_slug($categoryData['page_slug'] ?? '');
        $status = cms_trimmed($categoryData['status'] ?? 'draft');
        $sortOrder = (int) ($categoryData['sort_order'] ?? 100);
        $imagePath = cms_trimmed($categoryData['image_path'] ?? '');

        if ($categoryId) {
            $stmt = $pdo->prepare(
                'UPDATE product_categories
                 SET slug = ?, page_slug = ?, status = ?, sort_order = ?, image_path = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE id = ?'
            );
            $stmt->execute([$slug, $pageSlug ?: null, $status, $sortOrder, $imagePath ?: null, $categoryId]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO product_categories (slug, page_slug, status, sort_order, image_path)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$slug, $pageSlug ?: null, $status, $sortOrder, $imagePath ?: null]);
            $categoryId = (int) $pdo->lastInsertId();
        }

        $translationStmt = $pdo->prepare(
            'INSERT INTO product_category_translations
                (category_id, language_id, name, nav_label, summary, content_html, seo_title, seo_description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                nav_label = VALUES(nav_label),
                summary = VALUES(summary),
                content_html = VALUES(content_html),
                seo_title = VALUES(seo_title),
                seo_description = VALUES(seo_description)'
        );

        foreach (cms_languages() as $language) {
            $code = $language['code'];
            $row = $translations[$code] ?? [];
            $translationStmt->execute([
                $categoryId,
                (int) $language['id'],
                cms_trimmed($row['name'] ?? ''),
                cms_trimmed($row['nav_label'] ?? ''),
                cms_trimmed($row['summary'] ?? ''),
                (string) ($row['content_html'] ?? ''),
                cms_trimmed($row['seo_title'] ?? ''),
                cms_trimmed($row['seo_description'] ?? ''),
            ]);
        }

        $pdo->commit();
        return $categoryId;
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

function cms_admin_products(): array
{
    $defaultLanguage = cms_default_language();
    $stmt = cms_db()->prepare(
        'SELECT p.*, pt.name, pt.nav_label, pct.name AS category_name
         FROM products p
         LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.language_id = ?
         LEFT JOIN product_category_translations pct ON pct.category_id = p.category_id AND pct.language_id = ?
         ORDER BY p.sort_order ASC, p.id ASC'
    );
    $stmt->execute([(int) $defaultLanguage['id'], (int) $defaultLanguage['id']]);
    return $stmt->fetchAll();
}

function cms_admin_product(?int $productId): ?array
{
    if (!$productId) {
        return null;
    }

    $stmt = cms_db()->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) {
        return null;
    }

    $translationsStmt = cms_db()->prepare(
        'SELECT pt.*, l.code AS language_code
         FROM product_translations pt
         INNER JOIN languages l ON l.id = pt.language_id
         WHERE pt.product_id = ?'
    );
    $translationsStmt->execute([$productId]);

    $translations = [];
    foreach ($translationsStmt->fetchAll() as $row) {
        $translations[$row['language_code']] = $row;
    }

    $product['translations'] = $translations;
    return $product;
}

function cms_upsert_product(array $productData, array $translations): int
{
    $pdo = cms_db();
    $pdo->beginTransaction();

    try {
        $productId = !empty($productData['id']) ? (int) $productData['id'] : null;
        $categoryId = !empty($productData['category_id']) ? (int) $productData['category_id'] : null;
        $slug = cms_normalize_slug($productData['slug'] ?? '');
        $pageSlug = cms_normalize_slug($productData['page_slug'] ?? '');
        $status = cms_trimmed($productData['status'] ?? 'draft');
        $sortOrder = (int) ($productData['sort_order'] ?? 100);
        $imagePath = cms_trimmed($productData['image_path'] ?? '');
        $badge = cms_trimmed($productData['badge'] ?? '');

        if ($productId) {
            $stmt = $pdo->prepare(
                'UPDATE products
                 SET category_id = ?, slug = ?, page_slug = ?, status = ?, sort_order = ?, image_path = ?, badge = ?, updated_at = CURRENT_TIMESTAMP
                 WHERE id = ?'
            );
            $stmt->execute([$categoryId, $slug, $pageSlug ?: null, $status, $sortOrder, $imagePath ?: null, $badge ?: null, $productId]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO products (category_id, slug, page_slug, status, sort_order, image_path, badge)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$categoryId, $slug, $pageSlug ?: null, $status, $sortOrder, $imagePath ?: null, $badge ?: null]);
            $productId = (int) $pdo->lastInsertId();
        }

        $translationStmt = $pdo->prepare(
            'INSERT INTO product_translations
                (product_id, language_id, name, nav_label, short_description, content_html, seo_title, seo_description)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                nav_label = VALUES(nav_label),
                short_description = VALUES(short_description),
                content_html = VALUES(content_html),
                seo_title = VALUES(seo_title),
                seo_description = VALUES(seo_description)'
        );

        foreach (cms_languages() as $language) {
            $code = $language['code'];
            $row = $translations[$code] ?? [];
            $translationStmt->execute([
                $productId,
                (int) $language['id'],
                cms_trimmed($row['name'] ?? ''),
                cms_trimmed($row['nav_label'] ?? ''),
                cms_trimmed($row['short_description'] ?? ''),
                (string) ($row['content_html'] ?? ''),
                cms_trimmed($row['seo_title'] ?? ''),
                cms_trimmed($row['seo_description'] ?? ''),
            ]);
        }

        $pdo->commit();
        return $productId;
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

function cms_public_catalog(string $languageCode): array
{
    $language = cms_resolve_language($languageCode);
    $defaultLanguage = cms_default_language();

    $categoryStmt = cms_db()->prepare(
        'SELECT pc.*,
                COALESCE(NULLIF(pct_lang.name, ""), pct_default.name) AS name,
                COALESCE(NULLIF(pct_lang.nav_label, ""), NULLIF(pct_lang.name, ""), pct_default.nav_label, pct_default.name) AS nav_label,
                COALESCE(NULLIF(pct_lang.summary, ""), pct_default.summary) AS summary,
                COALESCE(NULLIF(pct_lang.content_html, ""), pct_default.content_html) AS content_html,
                COALESCE(NULLIF(pct_lang.seo_title, ""), pct_default.seo_title) AS seo_title,
                COALESCE(NULLIF(pct_lang.seo_description, ""), pct_default.seo_description) AS seo_description
         FROM product_categories pc
         LEFT JOIN product_category_translations pct_lang ON pct_lang.category_id = pc.id AND pct_lang.language_id = ?
         LEFT JOIN product_category_translations pct_default ON pct_default.category_id = pc.id AND pct_default.language_id = ?
         WHERE pc.status = "published"
         ORDER BY pc.sort_order ASC, pc.id ASC'
    );
    $categoryStmt->execute([(int) $language['id'], (int) $defaultLanguage['id']]);
    $categories = $categoryStmt->fetchAll();

    $productStmt = cms_db()->prepare(
        'SELECT p.*,
                COALESCE(NULLIF(pt_lang.name, ""), pt_default.name) AS name,
                COALESCE(NULLIF(pt_lang.nav_label, ""), NULLIF(pt_lang.name, ""), pt_default.nav_label, pt_default.name) AS nav_label,
                COALESCE(NULLIF(pt_lang.short_description, ""), pt_default.short_description) AS short_description,
                COALESCE(NULLIF(pt_lang.content_html, ""), pt_default.content_html) AS content_html,
                COALESCE(NULLIF(pt_lang.seo_title, ""), pt_default.seo_title) AS seo_title,
                COALESCE(NULLIF(pt_lang.seo_description, ""), pt_default.seo_description) AS seo_description
         FROM products p
         LEFT JOIN product_translations pt_lang ON pt_lang.product_id = p.id AND pt_lang.language_id = ?
         LEFT JOIN product_translations pt_default ON pt_default.product_id = p.id AND pt_default.language_id = ?
         WHERE p.status = "published"
         ORDER BY p.sort_order ASC, p.id ASC'
    );
    $productStmt->execute([(int) $language['id'], (int) $defaultLanguage['id']]);

    $productsByCategory = [];
    foreach ($productStmt->fetchAll() as $product) {
        $product['href'] = $product['page_slug']
            ? cms_localized_href('/' . $product['page_slug'], $language['code'])
            : '#';
        $productsByCategory[(int) ($product['category_id'] ?? 0)][] = $product;
    }

    $catalog = [];
    foreach ($categories as $category) {
        $category['href'] = $category['page_slug']
            ? cms_localized_href('/' . $category['page_slug'], $language['code'])
            : '#';
        $category['products'] = $productsByCategory[(int) $category['id']] ?? [];
        $catalog[] = $category;
    }

    return $catalog;
}

function cms_catalog_page_context_base(string $languageCode, string $slug): array
{
    $language = cms_resolve_language($languageCode);
    $catalog = cms_public_catalog($language['code']);

    return [
        'language' => $language,
        'languages' => cms_languages(),
        'navigation' => cms_public_navigation($language['code']),
        'site_settings' => cms_setting_map($language['code']),
        'slug' => cms_normalize_slug($slug),
        'catalog' => $catalog,
        'footer_categories' => array_slice($catalog, 0, 4),
    ];
}

function cms_public_catalog_services_page(string $languageCode): array
{
    $page = cms_catalog_page_context_base($languageCode, 'services');

    $page['meta'] = [
        'title' => 'Dental Lab Products - Ceramics, Implants, Aligners & More | Global Dental Lab',
        'description' => 'Explore Global Dental Lab products including CAD veneers, all-ceramics, implant restorations, surgical guides, PFM, clear aligners, removables, and orthodontics.',
        'type' => 'website',
        'canonical' => cms_localized_href('/services', $page['language']['code']),
        'image' => '/images/og-image.jpg',
    ];
    $page['hero'] = [
        'heroType' => 'static',
        'heroImage' => '/images/hero/services-hero.jpg',
        'heroTitle' => 'A Broader Product Mix<br>For Outsourcing Clinics',
        'heroSubtitle' => 'CAD veneers, all-ceramics, implants, surgical guides, PFM, clear aligners, removables, and orthodontic appliances.',
        'heroLabel' => 'Products',
        'heroCTAs' => [
            ['text' => 'SEND A CASE', 'href' => cms_localized_href('/send-a-case', $page['language']['code']), 'style' => 'white'],
            ['text' => 'DOWNLOAD FORMS', 'href' => cms_localized_href('/downloads', $page['language']['code']), 'style' => 'primary'],
        ],
        'showTrustBadges' => true,
        'activePage' => 'services',
    ];

    return $page;
}

function cms_public_catalog_category_page(string $languageCode, string $slug): ?array
{
    $page = cms_catalog_page_context_base($languageCode, $slug);
    $normalizedSlug = cms_normalize_slug($slug);

    foreach ($page['catalog'] as $category) {
        $pageSlug = cms_normalize_slug((string) ($category['page_slug'] ?? ''));
        if ($normalizedSlug !== $pageSlug && $normalizedSlug !== cms_normalize_slug((string) ($category['slug'] ?? ''))) {
            continue;
        }

        $page['category'] = $category;
        $page['meta'] = [
            'title' => ($category['seo_title'] ?: $category['name']) . ' - Global Dental Lab',
            'description' => $category['seo_description'] ?: $category['summary'],
            'type' => 'website',
            'canonical' => $category['href'] ?: cms_localized_href('/' . $normalizedSlug, $page['language']['code']),
            'image' => $category['image_path'] ?: '/images/og-image.jpg',
        ];
        $page['hero'] = [
            'heroType' => 'static',
            'heroImage' => '/images/hero/services-hero.jpg',
            'heroTitle' => (string) ($category['name'] ?: 'Product Category'),
            'heroSubtitle' => (string) ($category['summary'] ?: 'Browse category-level workflows and products.'),
            'heroLabel' => 'Product Category',
            'heroCTAs' => [
                ['text' => 'SEND A CASE', 'href' => cms_localized_href('/send-a-case', $page['language']['code']), 'style' => 'white'],
                ['text' => 'CONTACT THE LAB', 'href' => cms_localized_href('/contact', $page['language']['code']), 'style' => 'primary'],
            ],
            'showTrustBadges' => false,
            'activePage' => 'services',
        ];

        return $page;
    }

    return null;
}

function cms_public_catalog_product_page(string $languageCode, string $slug): ?array
{
    $page = cms_catalog_page_context_base($languageCode, $slug);
    $normalizedSlug = cms_normalize_slug($slug);

    foreach ($page['catalog'] as $category) {
        foreach ($category['products'] as $product) {
            $pageSlug = cms_normalize_slug((string) ($product['page_slug'] ?? ''));
            if ($normalizedSlug !== $pageSlug && $normalizedSlug !== cms_normalize_slug((string) ($product['slug'] ?? ''))) {
                continue;
            }

            $page['category'] = $category;
            $page['product'] = $product;
            $page['related_products'] = array_values(array_filter(
                $category['products'],
                static fn (array $candidate): bool => (int) $candidate['id'] !== (int) $product['id']
            ));
            $page['meta'] = [
                'title' => ($product['seo_title'] ?: $product['name']) . ' - Global Dental Lab',
                'description' => $product['seo_description'] ?: $product['short_description'],
                'type' => 'product',
                'canonical' => $product['href'] ?: cms_localized_href('/' . $normalizedSlug, $page['language']['code']),
                'image' => $product['image_path'] ?: ($category['image_path'] ?: '/images/og-image.jpg'),
            ];
            $page['hero'] = [
                'heroType' => 'static',
                'heroImage' => '/images/hero/services-hero.jpg',
                'heroTitle' => (string) ($product['name'] ?: 'Product Detail'),
                'heroSubtitle' => (string) ($product['short_description'] ?: 'A product workflow page managed from the catalog.'),
                'heroLabel' => 'Product Detail',
                'heroCTAs' => [
                    ['text' => 'SEND A CASE', 'href' => cms_localized_href('/send-a-case', $page['language']['code']), 'style' => 'white'],
                    ['text' => 'PREPARATION GUIDE', 'href' => cms_localized_href('/downloads', $page['language']['code']), 'style' => 'primary'],
                ],
                'showTrustBadges' => false,
                'activePage' => 'services',
            ];

            return $page;
        }
    }

    return null;
}
