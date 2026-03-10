<?php

declare(strict_types=1);

function cms_is_logged_in(): bool
{
    return !empty($_SESSION['cms_user_id']);
}

function cms_require_login(): void
{
    if (!cms_is_logged_in()) {
        cms_redirect('/cms/index.php');
    }
}

function cms_attempt_login(string $username, string $password): bool
{
    $stmt = cms_db()->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['cms_user_id'] = (int) $user['id'];
    $_SESSION['cms_username'] = $user['username'];

    return true;
}

function cms_logout(): void
{
    $_SESSION = [];
    session_destroy();
}
