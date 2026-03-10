<?php

declare(strict_types=1);

require __DIR__ . '/cms/bootstrap.php';

$requestedLanguage = $_GET['lang'] ?? '';
$requestedSlug = $_GET['slug'] ?? '';
$page = cms_find_public_page((string) $requestedLanguage, (string) $requestedSlug);

if (!$page) {
    http_response_code(404);
    echo cms_render_view('pages/public', [
        'page' => [
            'language' => cms_resolve_language((string) $requestedLanguage),
            'languages' => cms_languages(),
            'navigation' => cms_public_navigation(cms_resolve_language((string) $requestedLanguage)['code']),
            'slug' => cms_normalize_slug((string) $requestedSlug),
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

echo cms_render_view('pages/public', ['page' => $page], 'site');
