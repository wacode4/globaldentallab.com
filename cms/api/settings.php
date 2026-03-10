<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

echo json_encode([
    'success' => true,
    'settings' => array_merge(cms_setting_defaults(), cms_setting_map()),
], JSON_UNESCAPED_SLASHES);
