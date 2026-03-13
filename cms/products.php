<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$products = cms_admin_products();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Catalog Editing</p>
                <h1 class="text-4xl font-bold">Products</h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/dashboard.php">Dashboard</a>
                <a class="rounded-xl bg-slate-900 px-5 py-3 font-semibold text-white" href="/cms/product-edit.php">New Product</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-500">
                    <tr>
                        <th class="px-5 py-4">Slug</th>
                        <th class="px-5 py-4">Product</th>
                        <th class="px-5 py-4">Category</th>
                        <th class="px-5 py-4">Page Slug</th>
                        <th class="px-5 py-4">Status</th>
                        <th class="px-5 py-4"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr class="border-t border-slate-100">
                            <td class="px-5 py-4 font-mono text-xs"><?= cms_escape($product['slug']) ?></td>
                            <td class="px-5 py-4"><?= cms_escape($product['name'] ?: $product['slug']) ?></td>
                            <td class="px-5 py-4"><?= cms_escape($product['category_name'] ?: '-') ?></td>
                            <td class="px-5 py-4"><?= cms_escape($product['page_slug'] ?: '-') ?></td>
                            <td class="px-5 py-4"><?= cms_escape($product['status']) ?></td>
                            <td class="px-5 py-4 text-right"><a class="font-semibold text-sky-600" href="/cms/product-edit.php?id=<?= (int) $product['id'] ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
