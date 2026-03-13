<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$languages = cms_languages();
$categories = cms_admin_product_categories();
$productId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$product = cms_admin_product($productId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $translations = [];
    foreach ($languages as $language) {
        $code = $language['code'];
        $translations[$code] = [
            'name' => $_POST['translation'][$code]['name'] ?? '',
            'nav_label' => $_POST['translation'][$code]['nav_label'] ?? '',
            'short_description' => $_POST['translation'][$code]['short_description'] ?? '',
            'content_html' => $_POST['translation'][$code]['content_html'] ?? '',
            'seo_title' => $_POST['translation'][$code]['seo_title'] ?? '',
            'seo_description' => $_POST['translation'][$code]['seo_description'] ?? '',
        ];
    }

    $savedId = cms_upsert_product([
        'id' => $_POST['id'] ?? null,
        'category_id' => $_POST['category_id'] ?? null,
        'slug' => $_POST['slug'] ?? '',
        'page_slug' => $_POST['page_slug'] ?? '',
        'status' => $_POST['status'] ?? 'draft',
        'sort_order' => $_POST['sort_order'] ?? 100,
        'image_path' => $_POST['image_path'] ?? '',
        'badge' => $_POST['badge'] ?? '',
    ], $translations);

    cms_flash('Product saved.');
    cms_redirect('/cms/product-edit.php?id=' . $savedId);
}

$product = $product ?? [
    'id' => null,
    'category_id' => null,
    'slug' => '',
    'page_slug' => '',
    'status' => 'draft',
    'sort_order' => 100,
    'image_path' => '',
    'badge' => '',
    'translations' => [],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Editor | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Catalog Editing</p>
                <h1 class="text-4xl font-bold"><?= $product['id'] ? 'Edit Product' : 'New Product' ?></h1>
            </div>
            <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/products.php">Back</a>
        </div>

        <?php if ($flash = cms_flash()): ?>
            <div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-emerald-700"><?= cms_escape($flash) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-8">
            <input type="hidden" name="id" value="<?= (int) ($product['id'] ?? 0) ?>">
            <div class="rounded-3xl bg-white p-8 shadow">
                <h2 class="mb-6 text-2xl font-bold">Product Definition</h2>
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Category</label>
                        <select class="w-full rounded-xl border border-slate-300 px-4 py-3" name="category_id">
                            <option value="">No category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>>
                                    <?= cms_escape($category['name'] ?: $category['slug']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Slug</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="slug" value="<?= cms_escape($product['slug']) ?>" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Page Slug</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="page_slug" value="<?= cms_escape($product['page_slug']) ?>" placeholder="emax">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Status</label>
                        <select class="w-full rounded-xl border border-slate-300 px-4 py-3" name="status">
                            <?php foreach (['draft', 'published'] as $status): ?>
                                <option value="<?= $status ?>" <?= $product['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Sort Order</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" type="number" name="sort_order" value="<?= (int) $product['sort_order'] ?>">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Badge</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="badge" value="<?= cms_escape($product['badge'] ?? '') ?>" placeholder="High Esthetics">
                    </div>
                    <div class="md:col-span-2 xl:col-span-3">
                        <label class="mb-2 block text-sm font-medium">Image Path</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="image_path" value="<?= cms_escape($product['image_path'] ?? '') ?>" placeholder="/images/content/emax.jpg">
                    </div>
                </div>
            </div>

            <?php foreach ($languages as $language): ?>
                <?php $translation = $product['translations'][$language['code']] ?? []; ?>
                <div class="rounded-3xl bg-white p-8 shadow">
                    <h2 class="mb-6 text-2xl font-bold"><?= strtoupper(cms_escape($language['code'])) ?> Translation</h2>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Product Name</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][name]" value="<?= cms_escape($translation['name'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Navigation Label</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][nav_label]" value="<?= cms_escape($translation['nav_label'] ?? '') ?>">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Short Description</label>
                            <textarea class="min-h-28 w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][short_description]"><?= cms_escape($translation['short_description'] ?? '') ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Content HTML</label>
                            <textarea class="min-h-40 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="translation[<?= cms_escape($language['code']) ?>][content_html]"><?= cms_escape($translation['content_html'] ?? '') ?></textarea>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">SEO Title</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][seo_title]" value="<?= cms_escape($translation['seo_title'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">SEO Description</label>
                            <textarea class="min-h-28 w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][seo_description]"><?= cms_escape($translation['seo_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <button class="rounded-xl bg-primary px-6 py-3 font-semibold text-white" type="submit">Save Product</button>
        </form>
    </div>
</body>
</html>
