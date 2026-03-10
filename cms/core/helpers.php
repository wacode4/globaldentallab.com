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
