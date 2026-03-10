<?php

declare(strict_types=1);

if (!defined('CMS_CONFIG_FILE')) {
    define('CMS_CONFIG_FILE', CMS_BASE_PATH . '/config.local.php');
}

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
