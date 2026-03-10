<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$pages = cms_admin_pages();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pages | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Content Editing</p>
                <h1 class="text-4xl font-bold">Pages</h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/dashboard.php">Dashboard</a>
                <a class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white" href="/cms/page-edit.php">New Page</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Slug</th>
                        <th class="px-5 py-4">Page</th>
                        <th class="px-5 py-4">Template</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4">Modules</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pages as $page): ?>
                        <tr class="border-t border-slate-100">
                            <td class="px-5 py-4 font-mono text-xs"><?= cms_escape($page['slug']) ?></td>
                            <td class="px-5 py-4"><?= cms_escape($page['page_name'] ?: $page['slug']) ?></td>
                            <td class="px-5 py-4"><?= cms_escape($page['template_key']) ?></td>
                            <td class="px-5 py-4"><?= cms_escape($page['status']) ?></td>
                            <td class="px-5 py-4"><?= count(cms_admin_page_modules((int) $page['id'])) ?></td>
                            <td class="px-5 py-4 text-right"><a class="font-semibold text-sky-600" href="/cms/page-edit.php?id=<?= (int) $page['id'] ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
