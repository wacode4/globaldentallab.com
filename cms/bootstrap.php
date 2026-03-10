<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!defined('CMS_BASE_PATH')) {
    define('CMS_BASE_PATH', __DIR__);
}

require_once CMS_BASE_PATH . '/core/config.php';
require_once CMS_BASE_PATH . '/core/database.php';
require_once CMS_BASE_PATH . '/core/helpers.php';
require_once CMS_BASE_PATH . '/core/auth.php';
require_once CMS_BASE_PATH . '/core/view.php';
require_once CMS_BASE_PATH . '/core/content.php';
