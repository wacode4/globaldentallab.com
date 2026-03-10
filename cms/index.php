<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if (cms_is_logged_in()) {
    header('Location: /cms/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    $stmt = cms_db()->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['cms_user_id'] = (int) $user['id'];
        $_SESSION['cms_username'] = $user['username'];
        header('Location: /cms/dashboard.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Login | Global Dental Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-md rounded-3xl bg-white p-8 shadow-xl">
        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600 mb-3">Server CMS</p>
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Global Dental Lab</h1>
        <p class="text-slate-600 mb-8">Manage core content settings and incoming inquiries.</p>
        <?php if ($error): ?>
            <div class="mb-6 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= cms_escape($error) ?></div>
        <?php endif; ?>
        <form method="post" class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="username">Username</label>
                <input class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500" id="username" name="username" required>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="password">Password</label>
                <input class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500" id="password" name="password" type="password" required>
            </div>
            <button class="w-full rounded-xl bg-slate-900 px-4 py-3 font-semibold text-white transition hover:bg-slate-800" type="submit">Log In</button>
        </form>
    </div>
</body>
</html>
