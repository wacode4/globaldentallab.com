<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$rows = cms_db()->query('SELECT id, full_name, email, phone, clinic, service, message, status, created_at FROM inquiries ORDER BY created_at DESC LIMIT 500')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries | Global Dental Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Server CMS</p>
                <h1 class="text-4xl font-bold">Inquiries</h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/dashboard.php">Dashboard</a>
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/settings.php">Content Settings</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-500">
                        <tr>
                            <th class="px-5 py-4">Date</th>
                            <th class="px-5 py-4">Name</th>
                            <th class="px-5 py-4">Email</th>
                            <th class="px-5 py-4">Phone</th>
                            <th class="px-5 py-4">Clinic</th>
                            <th class="px-5 py-4">Service</th>
                            <th class="px-5 py-4">Message</th>
                            <th class="px-5 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$rows): ?>
                            <tr><td class="px-5 py-8 text-slate-500" colspan="8">No inquiries recorded yet.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($rows as $row): ?>
                            <tr class="border-t border-slate-100 align-top">
                                <td class="px-5 py-4 text-slate-500 whitespace-nowrap"><?= cms_escape($row['created_at']) ?></td>
                                <td class="px-5 py-4 font-medium whitespace-nowrap"><?= cms_escape($row['full_name']) ?></td>
                                <td class="px-5 py-4 whitespace-nowrap"><a class="text-sky-600 hover:text-sky-700" href="mailto:<?= cms_escape($row['email']) ?>"><?= cms_escape($row['email']) ?></a></td>
                                <td class="px-5 py-4 whitespace-nowrap"><?= cms_escape($row['phone'] ?: '-') ?></td>
                                <td class="px-5 py-4 whitespace-nowrap"><?= cms_escape($row['clinic'] ?: '-') ?></td>
                                <td class="px-5 py-4 whitespace-nowrap"><?= cms_escape($row['service'] ?: '-') ?></td>
                                <td class="px-5 py-4 min-w-80 text-slate-600"><?= nl2br(cms_escape($row['message'])) ?></td>
                                <td class="px-5 py-4 whitespace-nowrap"><?= cms_escape($row['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
