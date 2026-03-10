<?php

declare(strict_types=1);

session_start();

const CMS_CONFIG_FILE = __DIR__ . '/config.local.php';

function cms_config(): array
{
    static $config;

    if ($config !== null) {
        return $config;
    }

    if (!file_exists(CMS_CONFIG_FILE)) {
        http_response_code(500);
        echo 'CMS configuration missing. Create cms/config.local.php on the server.';
        exit;
    }

    $config = require CMS_CONFIG_FILE;

    return $config;
}

function cms_db(): PDO
{
    static $pdo;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = cms_config();
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['db_host'],
        $config['db_port'],
        $config['db_name']
    );

    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function cms_is_logged_in(): bool
{
    return !empty($_SESSION['cms_user_id']);
}

function cms_require_login(): void
{
    if (!cms_is_logged_in()) {
        header('Location: /cms/index.php');
        exit;
    }
}

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

function cms_setting_map(): array
{
    $rows = cms_db()->query('SELECT setting_key, setting_value FROM cms_settings ORDER BY id ASC')->fetchAll();
    $settings = [];

    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }

    return $settings;
}

function cms_setting_defaults(): array
{
    return [
        'home_intro_title' => 'A Full-Service Outsourcing Lab With Digital Depth',
        'home_intro_text' => 'Global Dental Lab was built for clinics that want the flexibility of an outsourcing partner without losing control over fit, communication, esthetics, or turnaround.',
        'home_scope_text' => 'The homepage surfaces the full product range so doctors can identify the right workflow quickly and move to the next step with less back-and-forth.',
        'contact_form_intro' => 'Use this form for new account questions, case planning support, turnaround discussions, or anything that does not fit one of the direct submission routes below.',
        'contact_hub_intro' => 'Use this page as the intake hub for first-contact questions, submission support, shipping coordination, and onboarding help.',
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
