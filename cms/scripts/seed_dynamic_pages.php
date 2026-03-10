<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$pdo = cms_db();

$languageStmt = $pdo->prepare(
    'INSERT INTO languages (code, name, native_name, is_default, is_active, sort_order)
     VALUES (?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        native_name = VALUES(native_name),
        is_default = VALUES(is_default),
        is_active = VALUES(is_active),
        sort_order = VALUES(sort_order)'
);

$languages = [
    ['en', 'English', 'English', 1, 1, 10],
    ['fr', 'French', 'Français', 0, 1, 20],
    ['de', 'German', 'Deutsch', 0, 1, 30],
];

foreach ($languages as $language) {
    $languageStmt->execute($language);
}

$languageRows = cms_languages();
$languageIds = [];
foreach ($languageRows as $row) {
    $languageIds[$row['code']] = (int) $row['id'];
}

$pageStmt = $pdo->prepare(
    'INSERT INTO pages (slug, page_type, template_key, status, sort_order, show_in_nav)
     VALUES (?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        page_type = VALUES(page_type),
        template_key = VALUES(template_key),
        status = VALUES(status),
        sort_order = VALUES(sort_order),
        show_in_nav = VALUES(show_in_nav),
        updated_at = CURRENT_TIMESTAMP'
);

$pageTranslationStmt = $pdo->prepare(
    'INSERT INTO page_translations (page_id, language_id, page_name, nav_label, seo_title, seo_description)
     VALUES (?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        page_name = VALUES(page_name),
        nav_label = VALUES(nav_label),
        seo_title = VALUES(seo_title),
        seo_description = VALUES(seo_description)'
);

$moduleStmt = $pdo->prepare(
    'INSERT INTO modules (module_key, module_type, variant, status, settings_json)
     VALUES (?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        module_type = VALUES(module_type),
        variant = VALUES(variant),
        status = VALUES(status),
        settings_json = VALUES(settings_json),
        updated_at = CURRENT_TIMESTAMP'
);

$moduleTranslationStmt = $pdo->prepare(
    'INSERT INTO module_translations (module_id, language_id, title, kicker, subtitle, content_html, content_json)
     VALUES (?, ?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        kicker = VALUES(kicker),
        subtitle = VALUES(subtitle),
        content_html = VALUES(content_html),
        content_json = VALUES(content_json)'
);

$pages = [
    'home' => [
        'definition' => ['home', 'home', 'marketing', 'published', 10, 1],
        'translations' => [
            'en' => ['Home', 'Home', 'Global Dental Lab Dynamic Homepage', 'Dynamic multilingual homepage rendered from PHP modules.'],
            'fr' => ['Accueil', 'Accueil', 'Global Dental Lab Dynamic Homepage', 'Dynamic multilingual homepage rendered from PHP modules.'],
            'de' => ['Startseite', 'Start', 'Global Dental Lab Dynamic Homepage', 'Dynamic multilingual homepage rendered from PHP modules.'],
        ],
    ],
    'contact' => [
        'definition' => ['contact', 'contact', 'marketing', 'published', 20, 1],
        'translations' => [
            'en' => ['Contact', 'Contact', 'Contact Global Dental Lab', 'Contact page rendered from reusable CMS modules.'],
            'fr' => ['Contact', 'Contact', 'Contact Global Dental Lab', 'Contact page rendered from reusable CMS modules.'],
            'de' => ['Kontakt', 'Kontakt', 'Contact Global Dental Lab', 'Contact page rendered from reusable CMS modules.'],
        ],
    ],
];

$pageIds = [];
foreach ($pages as $slug => $page) {
    $pageStmt->execute($page['definition']);
    $pageId = (int) $pdo->query("SELECT id FROM pages WHERE slug = " . $pdo->quote($slug))->fetchColumn();
    $pageIds[$slug] = $pageId;

    foreach ($page['translations'] as $code => $translation) {
        $pageTranslationStmt->execute([
            $pageId,
            $languageIds[$code],
            $translation[0],
            $translation[1],
            $translation[2],
            $translation[3],
        ]);
    }
}

$modules = [
    'home-hero' => [
        'definition' => ['home-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Dynamic Homepage', 'Template-Driven Site Architecture', 'Reusable hero module with multilingual content and route-aware links.', '', json_encode([
                'label' => 'Multilingual Route Preview',
                'title_html' => 'A Modular PHP Site<br>Built For Ongoing Change',
                'subtitle_html' => 'The new architecture separates content, templates, and page composition so future language rollout does not require copying entire pages.',
                'buttons' => [
                    ['text' => 'Open Contact', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'CMS Dashboard', 'href' => '/cms/dashboard.php', 'style' => 'secondary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'home-intro' => [
        'definition' => ['home-intro', 'rich_text', 'default', 'published', json_encode(['section_class' => 'py-20 bg-white'])],
        'translations' => [
            'en' => ['Content And Presentation Are Now Split', 'Architecture Goal', 'Pages will become compositions of reusable modules rather than one-off HTML files.', '<p>This first phase introduces a language-aware page model, module assignments, a template renderer, and a dynamic route pattern based on <code>/en/slug</code>.</p><p>From here, product pages, category pages, landing pages, and support pages can all move into the same system without fragmenting the codebase again.</p>', '{}'],
        ],
    ],
    'home-stats' => [
        'definition' => ['home-stats', 'stats_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Why This Refactor Matters', 'Operational Benefits', 'The architecture is designed to scale content operations, not just render one more page.', '', json_encode([
                'items' => [
                    ['value' => '1', 'label' => 'Page Model', 'description' => 'Pages, translations, and modules now live in separate tables.'],
                    ['value' => '3', 'label' => 'Languages Seeded', 'description' => 'English, French, and German are ready for translation expansion.'],
                    ['value' => '6', 'label' => 'Reusable Module Types', 'description' => 'Hero, rich text, stats, cards, contact panel, and CTA are now renderable.'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'home-cards' => [
        'definition' => ['home-cards', 'card_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Reusable Building Blocks', 'Module Library', 'Repeated sections are now components you can reuse, reorder, and translate independently.', '', json_encode([
                'cards' => [
                    ['title' => 'Hero Modules', 'text' => 'Shared marketing heroes can now be edited without touching templates.', 'href' => '/cms/modules.php', 'cta' => 'Manage', 'image' => '/images/content/digital-workflow.jpg'],
                    ['title' => 'Page Composition', 'text' => 'Editors can assign modules to pages and control sort order from CMS pages.', 'href' => '/cms/pages.php', 'cta' => 'Edit Pages', 'image' => '/images/content/dental-lab-2.jpg'],
                    ['title' => 'Multilingual Structure', 'text' => 'Translations are tied to page and module records instead of duplicate files.', 'href' => '/en/contact', 'cta' => 'Preview Route', 'image' => '/images/content/orthodontics.jpg'],
                    ['title' => 'Inquiry Capture', 'text' => 'Contact submissions already write into MySQL and appear in the CMS.', 'href' => '/cms/inquiries.php', 'cta' => 'View Inquiries', 'image' => '/images/content/veneers.jpg'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'home-cta' => [
        'definition' => ['home-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Next Step', 'Dynamic Contact Route Ready', 'The contact page is now available on the new multilingual route and powered by the same module system.', '', json_encode([
                'buttons' => [
                    ['text' => 'Open /en/contact', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'Edit Modules', 'href' => '/cms/modules.php', 'style' => 'secondary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'contact-hero' => [
        'definition' => ['contact-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Contact Route', 'Dynamic Contact Page', 'A contact and intake page rendered from modules so the same layout can be reused across languages.', '', json_encode([
                'label' => 'Reusable Contact Template',
                'title_html' => 'Contact The Lab<br>On The New Route',
                'subtitle_html' => 'This page demonstrates how a single template can support language-aware URLs, editable content, and server-side inquiry handling.',
                'buttons' => [
                    ['text' => 'Send Inquiry', 'href' => '#contact-form', 'style' => 'primary'],
                    ['text' => 'Back Home', 'href' => '/en/', 'style' => 'secondary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'contact-panel' => [
        'definition' => ['contact-panel', 'contact_panel', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Inquiry And Intake', 'Server-Side Form', 'Messages submitted here write directly into the MySQL-backed CMS instead of depending on Cloudflare functions.', '', json_encode([
                'items' => [
                    ['label' => 'Phone', 'value' => '+852 9142 4923'],
                    ['label' => 'Email', 'value' => 'info@globaldentallab.com'],
                    ['label' => 'Hong Kong', 'value_html' => '1/F Tung Chung 41 Ma Wan New Village<br>Lantau Island, Hong Kong'],
                    ['label' => 'Shenzhen', 'value_html' => '4/F, Building 1 HeTai Industrial Area<br>Shenzhen, China'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'contact-cta' => [
        'definition' => ['contact-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Editing Workflow', 'Everything Here Is CMS-Managed', 'After sign-in, page records, module records, and route composition are all editable from the content backend.', '', json_encode([
                'buttons' => [
                    ['text' => 'Edit Contact Page', 'href' => '/cms/pages.php', 'style' => 'primary'],
                    ['text' => 'View Inquiries', 'href' => '/cms/inquiries.php', 'style' => 'secondary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
];

$moduleIds = [];
foreach ($modules as $key => $module) {
    $moduleStmt->execute($module['definition']);
    $moduleId = (int) $pdo->query("SELECT id FROM modules WHERE module_key = " . $pdo->quote($key))->fetchColumn();
    $moduleIds[$key] = $moduleId;

    foreach ($module['translations'] as $code => $translation) {
        $moduleTranslationStmt->execute([
            $moduleId,
            $languageIds[$code],
            $translation[0],
            $translation[1],
            $translation[2],
            $translation[3],
            $translation[4],
        ]);
    }
}

$pdo->exec('DELETE FROM page_modules');

$pageModules = [
    ['home', 'home-hero', 'main', 10, 1],
    ['home', 'home-intro', 'main', 20, 1],
    ['home', 'home-stats', 'main', 30, 1],
    ['home', 'home-cards', 'main', 40, 1],
    ['home', 'home-cta', 'main', 50, 1],
    ['contact', 'contact-hero', 'main', 10, 1],
    ['contact', 'contact-panel', 'main', 20, 1],
    ['contact', 'contact-cta', 'main', 30, 1],
];

$pageModuleStmt = $pdo->prepare(
    'INSERT INTO page_modules (page_id, module_id, region_name, sort_order, is_enabled) VALUES (?, ?, ?, ?, ?)'
);

foreach ($pageModules as $assignment) {
    $pageModuleStmt->execute([
        $pageIds[$assignment[0]],
        $moduleIds[$assignment[1]],
        $assignment[2],
        $assignment[3],
        $assignment[4],
    ]);
}

echo "Seed complete.\n";
