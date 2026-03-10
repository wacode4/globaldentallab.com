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
    $language = cms_resolve_language($languageCode);
    $defaultLanguage = cms_default_language();

    $stmt = cms_db()->prepare(
        'SELECT p.slug, p.page_type,
                COALESCE(pt_lang.page_name, pt_default.page_name) AS page_name,
                COALESCE(pt_lang.nav_label, pt_default.nav_label) AS nav_label
         FROM pages p
         LEFT JOIN page_translations pt_lang ON pt_lang.page_id = p.id AND pt_lang.language_id = ?
         LEFT JOIN page_translations pt_default ON pt_default.page_id = p.id AND pt_default.language_id = ?
         WHERE p.status = "published" AND p.show_in_nav = 1
         ORDER BY p.sort_order ASC, p.id ASC'
    );
    $stmt->execute([(int) $language['id'], (int) $defaultLanguage['id']]);
    return $stmt->fetchAll();
}

function cms_find_public_page(string $languageCode, string $slug): ?array
{
    $language = cms_resolve_language($languageCode);
    $defaultLanguage = cms_default_language();
    $normalizedSlug = $slug === '' ? 'home' : cms_normalize_slug($slug);

    $stmt = cms_db()->prepare(
        'SELECT p.*,
                COALESCE(pt_lang.page_name, pt_default.page_name) AS page_name,
                COALESCE(pt_lang.nav_label, pt_default.nav_label) AS nav_label,
                COALESCE(pt_lang.seo_title, pt_default.seo_title) AS seo_title,
                COALESCE(pt_lang.seo_description, pt_default.seo_description) AS seo_description
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
                COALESCE(mt_lang.title, mt_default.title) AS title,
                COALESCE(mt_lang.kicker, mt_default.kicker) AS kicker,
                COALESCE(mt_lang.subtitle, mt_default.subtitle) AS subtitle,
                COALESCE(mt_lang.content_html, mt_default.content_html) AS content_html,
                COALESCE(mt_lang.content_json, mt_default.content_json) AS content_json
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
        $modules[] = $module;
    }

    $page['language'] = $language;
    $page['languages'] = cms_languages();
    $page['navigation'] = cms_public_navigation($language['code']);
    $page['modules'] = $modules;
    $page['slug'] = $normalizedSlug;

    return $page;
}
