<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$modules = cms_admin_modules();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modules | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Content Editing</p>
                <h1 class="text-4xl font-bold">Modules</h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/dashboard.php">Dashboard</a>
                <a class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white" href="/cms/module-edit.php">New Module</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Key</th>
                        <th class="px-5 py-4">Type</th>
                        <th class="px-5 py-4">Template Type</th>
                        <th class="px-5 py-4">Variant</th>
                        <th class="px-5 py-4">Title</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $module): ?>
                        <?php $templateMeta = cms_module_editor_template_meta($module['module_type'], $module['module_key']); ?>
                        <tr class="border-t border-slate-100">
                            <td class="px-5 py-4 font-mono text-xs"><?= cms_escape($module['module_key']) ?></td>
                            <td class="px-5 py-4"><?= cms_escape($module['module_type']) ?></td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $templateMeta['mode'] === 'structured' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                                    <?= cms_escape($templateMeta['label']) ?>
                                </span>
                                <div class="mt-1 text-xs uppercase tracking-[0.18em] <?= $templateMeta['mode'] === 'structured' ? 'text-emerald-500' : 'text-slate-400' ?>">
                                    <?= $templateMeta['mode'] === 'structured' ? 'Structured' : 'Raw' ?>
                                </div>
                            </td>
                            <td class="px-5 py-4"><?= cms_escape($module['variant']) ?></td>
                            <td class="px-5 py-4"><?= cms_escape($module['title'] ?: '-') ?></td>
                            <td class="px-5 py-4"><?= cms_escape($module['status']) ?></td>
                            <td class="px-5 py-4 text-right"><a class="font-semibold text-sky-600" href="/cms/module-edit.php?id=<?= (int) $module['id'] ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
