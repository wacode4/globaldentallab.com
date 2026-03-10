<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$inquiryCount = (int) cms_db()->query('SELECT COUNT(*) FROM inquiries')->fetchColumn();
$newCount = (int) cms_db()->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
$settingsCount = (int) cms_db()->query('SELECT COUNT(*) FROM cms_settings')->fetchColumn();
$pageCount = (int) cms_db()->query('SELECT COUNT(*) FROM pages')->fetchColumn();
$moduleCount = (int) cms_db()->query('SELECT COUNT(*) FROM modules')->fetchColumn();
$menuCount = (int) cms_db()->query('SELECT COUNT(*) FROM menus')->fetchColumn();
$recentInquiries = cms_db()->query('SELECT id, full_name, email, service, created_at FROM inquiries ORDER BY created_at DESC LIMIT 8')->fetchAll();
$flash = cms_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Dashboard | Global Dental Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Server CMS</p>
                <h1 class="text-4xl font-bold">Dashboard</h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/pages.php">Pages</a>
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/modules.php">Modules</a>
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/menus.php">Menus</a>
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/settings.php">Content Settings</a>
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/inquiries.php">Inquiries</a>
                <a class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white hover:bg-slate-800" href="/cms/logout.php">Log Out</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-emerald-700"><?= cms_escape($flash) ?></div>
        <?php endif; ?>

        <div class="mb-10 grid gap-6 md:grid-cols-6">
            <div class="rounded-3xl bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Total Inquiries</p>
                <p class="mt-3 text-4xl font-bold"><?= $inquiryCount ?></p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <p class="text-sm text-slate-500">New Inquiries</p>
                <p class="mt-3 text-4xl font-bold text-sky-600"><?= $newCount ?></p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Managed Settings</p>
                <p class="mt-3 text-4xl font-bold"><?= $settingsCount ?></p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Pages</p>
                <p class="mt-3 text-4xl font-bold"><?= $pageCount ?></p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Modules</p>
                <p class="mt-3 text-4xl font-bold"><?= $moduleCount ?></p>
            </div>
            <div class="rounded-3xl bg-white p-6 shadow">
                <p class="text-sm text-slate-500">Menus</p>
                <p class="mt-3 text-4xl font-bold"><?= $menuCount ?></p>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-2xl font-bold">Recent Inquiries</h2>
                <a class="text-sm font-semibold text-sky-600 hover:text-sky-700" href="/cms/inquiries.php">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-slate-500">
                            <th class="py-3 pr-4">Name</th>
                            <th class="py-3 pr-4">Email</th>
                            <th class="py-3 pr-4">Service</th>
                            <th class="py-3 pr-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$recentInquiries): ?>
                            <tr><td class="py-6 text-slate-500" colspan="4">No inquiries yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($recentInquiries as $row): ?>
                            <tr class="border-b border-slate-100">
                                <td class="py-3 pr-4 font-medium"><?= cms_escape($row['full_name']) ?></td>
                                <td class="py-3 pr-4"><?= cms_escape($row['email']) ?></td>
                                <td class="py-3 pr-4"><?= cms_escape($row['service'] ?: '-') ?></td>
                                <td class="py-3 pr-4 text-slate-500"><?= cms_escape($row['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
