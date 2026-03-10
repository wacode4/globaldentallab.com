<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$menus = cms_admin_menus();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menus | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Content Editing</p>
                <h1 class="text-4xl font-bold">Menus</h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/dashboard.php">Dashboard</a>
                <a class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white" href="/cms/menu-edit.php">New Menu</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Key</th>
                        <th class="px-5 py-4">Name</th>
                        <th class="px-5 py-4">Items</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($menus as $menu): ?>
                        <tr class="border-t border-slate-100">
                            <td class="px-5 py-4 font-mono text-xs"><?= cms_escape($menu['menu_key']) ?></td>
                            <td class="px-5 py-4"><?= cms_escape($menu['name']) ?></td>
                            <td class="px-5 py-4"><?= (int) $menu['item_count'] ?></td>
                            <td class="px-5 py-4 text-right"><a class="font-semibold text-sky-600" href="/cms/menu-edit.php?id=<?= (int) $menu['id'] ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
