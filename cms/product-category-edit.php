<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$languages = cms_languages();
$categoryId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$category = cms_admin_product_category($categoryId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $translations = [];
    foreach ($languages as $language) {
        $code = $language['code'];
        $translations[$code] = [
            'name' => $_POST['translation'][$code]['name'] ?? '',
            'nav_label' => $_POST['translation'][$code]['nav_label'] ?? '',
            'summary' => $_POST['translation'][$code]['summary'] ?? '',
            'content_html' => $_POST['translation'][$code]['content_html'] ?? '',
            'seo_title' => $_POST['translation'][$code]['seo_title'] ?? '',
            'seo_description' => $_POST['translation'][$code]['seo_description'] ?? '',
        ];
    }

    $savedId = cms_upsert_product_category([
        'id' => $_POST['id'] ?? null,
        'slug' => $_POST['slug'] ?? '',
        'page_slug' => $_POST['page_slug'] ?? '',
        'status' => $_POST['status'] ?? 'draft',
        'sort_order' => $_POST['sort_order'] ?? 100,
        'image_path' => $_POST['image_path'] ?? '',
    ], $translations);

    cms_flash('Product category saved.');
    cms_redirect('/cms/product-category-edit.php?id=' . $savedId);
}

$category = $category ?? [
    'id' => null,
    'slug' => '',
    'page_slug' => '',
    'status' => 'draft',
    'sort_order' => 100,
    'image_path' => '',
    'translations' => [],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Category Editor | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Catalog Editing</p>
                <h1 class="text-4xl font-bold"><?= $category['id'] ? 'Edit Product Category' : 'New Product Category' ?></h1>
            </div>
            <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/product-categories.php">Back</a>
        </div>

        <?php if ($flash = cms_flash()): ?>
            <div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-emerald-700"><?= cms_escape($flash) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-8">
            <input type="hidden" name="id" value="<?= (int) ($category['id'] ?? 0) ?>">
            <div class="rounded-3xl bg-white p-8 shadow">
                <h2 class="mb-6 text-2xl font-bold">Category Definition</h2>
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Slug</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="slug" value="<?= cms_escape($category['slug']) ?>" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Page Slug</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="page_slug" value="<?= cms_escape($category['page_slug']) ?>" placeholder="ceramics">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Status</label>
                        <select class="w-full rounded-xl border border-slate-300 px-4 py-3" name="status">
                            <?php foreach (['draft', 'published'] as $status): ?>
                                <option value="<?= $status ?>" <?= $category['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Sort Order</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" type="number" name="sort_order" value="<?= (int) $category['sort_order'] ?>">
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-2 block text-sm font-medium">Image Path</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="image_path" value="<?= cms_escape($category['image_path'] ?? '') ?>" placeholder="/images/content/zirconia.jpg">
                    </div>
                </div>
            </div>

            <?php foreach ($languages as $language): ?>
                <?php $translation = $category['translations'][$language['code']] ?? []; ?>
                <div class="rounded-3xl bg-white p-8 shadow">
                    <h2 class="mb-6 text-2xl font-bold"><?= strtoupper(cms_escape($language['code'])) ?> Translation</h2>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Category Name</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][name]" value="<?= cms_escape($translation['name'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Navigation Label</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][nav_label]" value="<?= cms_escape($translation['nav_label'] ?? '') ?>">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Summary</label>
                            <textarea class="min-h-28 w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][summary]"><?= cms_escape($translation['summary'] ?? '') ?></textarea>
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

            <button class="rounded-xl bg-primary px-6 py-3 font-semibold text-white" type="submit">Save Category</button>
        </form>
    </div>
</body>
</html>
