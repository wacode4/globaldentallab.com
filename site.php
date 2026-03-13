<?php

declare(strict_types=1);

require __DIR__ . '/cms/bootstrap.php';

$requestedLanguage = $_GET['lang'] ?? '';
$requestedSlug = $_GET['slug'] ?? '';
$resolvedLanguage = cms_resolve_language((string) $requestedLanguage);
$normalizedSlug = cms_normalize_slug((string) $requestedSlug);

if ($normalizedSlug === 'services') {
    echo cms_render_view('pages/catalog_services', ['page' => cms_public_catalog_services_page($resolvedLanguage['code'])], 'catalog');
    exit;
}

if ($catalogPage = cms_public_catalog_category_page($resolvedLanguage['code'], $normalizedSlug)) {
    echo cms_render_view('pages/catalog_category', ['page' => $catalogPage], 'catalog');
    exit;
}

if ($catalogPage = cms_public_catalog_product_page($resolvedLanguage['code'], $normalizedSlug)) {
    echo cms_render_view('pages/catalog_product', ['page' => $catalogPage], 'catalog');
    exit;
}

$page = cms_find_public_page((string) $requestedLanguage, (string) $requestedSlug);

if (!$page) {
    http_response_code(404);
    echo cms_render_view('pages/public', [
        'page' => [
            'language' => $resolvedLanguage,
            'languages' => cms_languages(),
            'navigation' => cms_public_navigation($resolvedLanguage['code']),
            'slug' => $normalizedSlug,
            'page_name' => 'Page Not Found',
            'seo_title' => 'Page Not Found',
            'seo_description' => 'Requested page was not found.',
            'modules' => [[
                'module_type' => 'rich_text',
                'kicker' => '404',
                'title' => 'Page Not Found',
                'subtitle' => 'The requested content is not available yet.',
                'content_html' => '<p>Use the CMS to create the page or return to the current language homepage.</p>',
                'settings' => ['section_class' => 'py-24 bg-white'],
            ]],
        ],
    ], 'site');
    exit;
}

if ($legacyHtml = cms_render_legacy_public_page((string) ($page['slug'] ?? ''), (string) ($page['language']['code'] ?? ''))) {
    echo $legacyHtml;
    exit;
}

echo cms_render_view('pages/public', ['page' => $page], 'site');
