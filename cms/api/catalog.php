<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

cms_json_response([
    'success' => true,
    'language' => cms_resolve_language($_GET['lang'] ?? '')['code'],
    'catalog' => cms_public_catalog((string) ($_GET['lang'] ?? '')),
]);
