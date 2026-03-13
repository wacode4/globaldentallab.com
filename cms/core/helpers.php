<?php

declare(strict_types=1);

function cms_escape(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function cms_flash(?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['cms_flash'] = $message;
        return null;
    }

    if (empty($_SESSION['cms_flash'])) {
        return null;
    }

    $flash = $_SESSION['cms_flash'];
    unset($_SESSION['cms_flash']);

    return $flash;
}

function cms_redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function cms_json_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function cms_read_json_input(): array
{
    $decoded = json_decode((string) file_get_contents('php://input'), true);
    return is_array($decoded) ? $decoded : [];
}

function cms_decode_json(?string $value, array $fallback = []): array
{
    if (!$value) {
        return $fallback;
    }

    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : $fallback;
}

function cms_encode_json(array $value): string
{
    return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function cms_normalize_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9-]+/', '-', $value) ?? '';
    return trim($value, '-');
}

function cms_trimmed(?string $value): string
{
    return trim((string) $value);
}

function cms_setting_definitions(): array
{
    return [
        'home_intro_title' => ['label' => 'Homepage Intro Title', 'type' => 'textarea', 'translatable' => false],
        'home_intro_text' => ['label' => 'Homepage Intro Paragraph', 'type' => 'textarea', 'translatable' => false],
        'home_scope_text' => ['label' => 'Homepage Product Scope Text', 'type' => 'textarea', 'translatable' => false],
        'contact_form_intro' => ['label' => 'Contact Form Intro', 'type' => 'textarea', 'translatable' => false],
        'contact_hub_intro' => ['label' => 'Contact Hub Intro', 'type' => 'textarea', 'translatable' => false],
        'site_name' => ['label' => 'Site Name', 'type' => 'text', 'translatable' => true],
        'site_tagline' => ['label' => 'Site Tagline', 'type' => 'textarea', 'translatable' => true],
        'site_footer_blurb' => ['label' => 'Footer Brand Blurb', 'type' => 'textarea', 'translatable' => true],
        'site_phone_display' => ['label' => 'Phone Display', 'type' => 'text', 'translatable' => false],
        'site_phone_href' => ['label' => 'Phone Link', 'type' => 'text', 'translatable' => false],
        'site_email_display' => ['label' => 'Email Display', 'type' => 'text', 'translatable' => false],
        'site_email_href' => ['label' => 'Email Link', 'type' => 'text', 'translatable' => false],
        'site_whatsapp_display' => ['label' => 'WhatsApp Display', 'type' => 'text', 'translatable' => false],
        'site_whatsapp_href' => ['label' => 'WhatsApp Link', 'type' => 'text', 'translatable' => false],
        'site_hk_address_html' => ['label' => 'Hong Kong Address HTML', 'type' => 'textarea', 'translatable' => true],
        'site_sz_address_html' => ['label' => 'Shenzhen Address HTML', 'type' => 'textarea', 'translatable' => true],
    ];
}

function cms_setting_defaults(): array
{
    return [
        'home_intro_title' => 'A Full-Service Outsourcing Lab With Digital Depth',
        'home_intro_text' => 'Global Dental Lab was built for clinics that want the flexibility of an outsourcing partner without losing control over fit, communication, esthetics, or turnaround.',
        'home_scope_text' => 'The homepage surfaces the full product range so doctors can identify the right workflow quickly and move to the next step with less back-and-forth.',
        'contact_form_intro' => 'Use this form for new account questions, case planning support, turnaround discussions, or anything that does not fit one of the direct submission routes below.',
        'contact_hub_intro' => 'Use this page as the intake hub for first-contact questions, submission support, shipping coordination, and onboarding help.',
        'site_name' => 'Global Dental Lab',
        'site_tagline' => 'Template-driven multilingual site architecture running on PHP + MySQL.',
        'site_footer_blurb' => 'Template-driven multilingual site architecture running on PHP + MySQL.',
        'site_phone_display' => '+852 9142 4923',
        'site_phone_href' => 'tel:+85291424923',
        'site_email_display' => 'info@globaldentallab.com',
        'site_email_href' => 'mailto:info@globaldentallab.com',
        'site_whatsapp_display' => '+852 9142 4923',
        'site_whatsapp_href' => 'https://wa.me/85291424923',
        'site_hk_address_html' => '1/F Tung Chung 41 Ma Wan New Village<br>Lantau Island, Hong Kong',
        'site_sz_address_html' => '4/F, Building 1 HeTai Industrial Area<br>Shenzhen, China',
    ];
}

function cms_setting_map(?string $languageCode = null): array
{
    $settings = cms_setting_defaults();

    foreach (cms_db()->query('SELECT setting_key, setting_value FROM cms_settings')->fetchAll() as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    if ($languageCode === null || $languageCode === '') {
        return $settings;
    }

    $stmt = cms_db()->prepare(
        'SELECT st.setting_key, st.setting_value
         FROM site_setting_translations st
         INNER JOIN languages l ON l.id = st.language_id
         WHERE l.code = ?'
    );
    $stmt->execute([strtolower($languageCode)]);

    foreach ($stmt->fetchAll() as $row) {
        if ($row['setting_value'] !== '') {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }

    return $settings;
}

function cms_setting_translation_map(): array
{
    $rows = cms_db()->query(
        'SELECT st.setting_key, st.setting_value, l.code AS language_code
         FROM site_setting_translations st
         INNER JOIN languages l ON l.id = st.language_id'
    )->fetchAll();

    $map = [];
    foreach ($rows as $row) {
        $map[$row['language_code']][$row['setting_key']] = $row['setting_value'];
    }

    return $map;
}

function cms_localized_href(?string $href, string $languageCode): string
{
    $href = trim((string) $href);
    if ($href === '') {
        return '#';
    }

    if ($href[0] !== '/') {
        return $href;
    }

    if (
        preg_match('#^/(cms|images|css|js|uploads)(/|$)#', $href) === 1 ||
        preg_match('#\\.[a-z0-9]{2,5}$#i', $href) === 1 ||
        str_starts_with($href, '//')
    ) {
        return $href;
    }

    if (preg_match('#^/[a-z]{2}(?=/|$)#', $href) === 1) {
        return (string) preg_replace('#^/[a-z]{2}(?=/|$)#', '/' . $languageCode, $href, 1);
    }

    return '/' . $languageCode . $href;
}

function cms_legacy_page_map(): array
{
    return [
        'home' => 'index.html',
        'about' => 'about.html',
        'technology' => 'technology.html',
        'services' => 'services.html',
        'contact' => 'contact.html',
        'ceramics' => 'category-ceramics.html',
        'zirconia-ultra' => 'product-zirconia-ultra.html',
        'emax' => 'product-emax.html',
        'layered-zirconia' => 'product-layered.html',
        'monolithic-zirconia' => 'product-monolithic.html',
        'veneers' => 'product-veneers.html',
        'inlays-onlays' => 'product-inlays.html',
        'downloads' => 'downloads.html',
        'send-a-case' => 'send-a-case.html',
        'materials' => 'materials.html',
        'certificates' => 'certificates.html',
        'lab-tour' => 'lab-tour.html',
    ];
}

function cms_render_legacy_public_page(string $slug, string $languageCode): ?string
{
    if (strtolower($languageCode) !== 'en') {
        return null;
    }

    $map = cms_legacy_page_map();
    if (!isset($map[$slug])) {
        return null;
    }

    $path = dirname(CMS_BASE_PATH) . '/' . $map[$slug];
    if (!is_file($path)) {
        return null;
    }

    $html = (string) file_get_contents($path);
    if ($html === '') {
        return null;
    }

    return cms_transform_legacy_public_html($html, $languageCode);
}

function cms_transform_legacy_public_html(string $html, string $languageCode): string
{
    $routeMap = [
        'index.html' => '/' . $languageCode . '/',
        'about.html' => cms_localized_href('/about', $languageCode),
        'technology.html' => cms_localized_href('/technology', $languageCode),
        'services.html' => cms_localized_href('/services', $languageCode),
        'contact.html' => cms_localized_href('/contact', $languageCode),
        'category-ceramics.html' => cms_localized_href('/ceramics', $languageCode),
        'product-zirconia-ultra.html' => cms_localized_href('/zirconia-ultra', $languageCode),
        'product-emax.html' => cms_localized_href('/emax', $languageCode),
        'product-layered.html' => cms_localized_href('/layered-zirconia', $languageCode),
        'product-monolithic.html' => cms_localized_href('/monolithic-zirconia', $languageCode),
        'product-veneers.html' => cms_localized_href('/veneers', $languageCode),
        'product-inlays.html' => cms_localized_href('/inlays-onlays', $languageCode),
        'downloads.html' => cms_localized_href('/downloads', $languageCode),
        'send-a-case.html' => cms_localized_href('/send-a-case', $languageCode),
        'materials.html' => cms_localized_href('/materials', $languageCode),
        'certificates.html' => cms_localized_href('/certificates', $languageCode),
        'lab-tour.html' => cms_localized_href('/lab-tour', $languageCode),
    ];

    foreach ($routeMap as $source => $target) {
        $html = str_replace($source, $target, $html);
    }

    $html = (string) preg_replace(
        '#(?<=["\'(=])(?!(?:https?:)?//|/)(images|css|js|downloads)/#',
        '/$1/',
        $html
    );

    $html = (string) preg_replace(
        '#/js/header-hero\.js\?v=[0-9-]+#',
        '/js/header-hero.js?v=20260313-1',
        $html
    );

    $html = (string) preg_replace(
        '#<script(?![^>]*data-cfasync)(?![^>]*type="application/ld\+json")#',
        '<script data-cfasync="false"',
        $html
    );

    return $html;
}
