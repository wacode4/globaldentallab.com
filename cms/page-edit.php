<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$languages = cms_languages();
$availableModules = cms_admin_modules();
$pageId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$page = cms_admin_page($pageId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $translations = [];
    foreach ($languages as $language) {
        $code = $language['code'];
        $translations[$code] = [
            'page_name' => $_POST['translation'][$code]['page_name'] ?? '',
            'nav_label' => $_POST['translation'][$code]['nav_label'] ?? '',
            'seo_title' => $_POST['translation'][$code]['seo_title'] ?? '',
            'seo_description' => $_POST['translation'][$code]['seo_description'] ?? '',
        ];
    }

    $assignments = [];
    foreach (($_POST['assignments'] ?? []) as $assignment) {
        $assignments[] = [
            'module_id' => $assignment['module_id'] ?? '',
            'region_name' => $assignment['region_name'] ?? 'main',
            'sort_order' => $assignment['sort_order'] ?? 100,
            'is_enabled' => !empty($assignment['is_enabled']),
        ];
    }

    $savedId = cms_upsert_page([
        'id' => $_POST['id'] ?? null,
        'slug' => $_POST['slug'] ?? '',
        'page_type' => $_POST['page_type'] ?? 'page',
        'template_key' => $_POST['template_key'] ?? 'default',
        'status' => $_POST['status'] ?? 'draft',
        'sort_order' => $_POST['sort_order'] ?? 100,
        'show_in_nav' => !empty($_POST['show_in_nav']),
    ], $translations, $assignments);

    cms_flash('Page saved.');
    cms_redirect('/cms/page-edit.php?id=' . $savedId);
}

$page = $page ?? [
    'id' => null,
    'slug' => '',
    'page_type' => 'page',
    'template_key' => 'default',
    'status' => 'draft',
    'sort_order' => 100,
    'show_in_nav' => 1,
    'translations' => [],
    'modules' => [],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Editor | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Content Editing</p>
                <h1 class="text-4xl font-bold"><?= $page['id'] ? 'Edit Page' : 'New Page' ?></h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/pages.php">Back</a>
            </div>
        </div>

        <?php if ($flash = cms_flash()): ?>
            <div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-emerald-700"><?= cms_escape($flash) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-8">
            <input type="hidden" name="id" value="<?= (int) ($page['id'] ?? 0) ?>">
            <div class="rounded-3xl bg-white p-8 shadow">
                <h2 class="mb-6 text-2xl font-bold">Page Definition</h2>
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Slug</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="slug" value="<?= cms_escape($page['slug']) ?>" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Page Type</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="page_type" value="<?= cms_escape($page['page_type']) ?>">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Template Key</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="template_key" value="<?= cms_escape($page['template_key']) ?>">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Status</label>
                        <select class="w-full rounded-xl border border-slate-300 px-4 py-3" name="status">
                            <?php foreach (['draft', 'published'] as $status): ?>
                                <option value="<?= $status ?>" <?= $page['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Sort Order</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="sort_order" type="number" value="<?= (int) $page['sort_order'] ?>">
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-3 rounded-xl border border-slate-300 px-4 py-3">
                            <input type="checkbox" name="show_in_nav" value="1" <?= !empty($page['show_in_nav']) ? 'checked' : '' ?>>
                            <span>Show in navigation</span>
                        </label>
                    </div>
                </div>
            </div>

            <?php foreach ($languages as $language): ?>
                <?php $translation = $page['translations'][$language['code']] ?? []; ?>
                <div class="rounded-3xl bg-white p-8 shadow">
                    <h2 class="mb-6 text-2xl font-bold"><?= strtoupper(cms_escape($language['code'])) ?> Translation</h2>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Page Name</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][page_name]" value="<?= cms_escape($translation['page_name'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Nav Label</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][nav_label]" value="<?= cms_escape($translation['nav_label'] ?? '') ?>">
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

            <div class="rounded-3xl bg-white p-8 shadow">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold">Page Modules</h2>
                    <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white" id="add-module-row" type="button">Add Module</button>
                </div>
                <div class="space-y-4" id="module-rows">
                    <?php $assignments = $page['modules'] ?: [['module_id' => '', 'region_name' => 'main', 'sort_order' => 100, 'is_enabled' => 1]]; ?>
                    <?php foreach ($assignments as $index => $assignment): ?>
                        <div class="grid gap-4 rounded-2xl border border-slate-200 p-4 md:grid-cols-4">
                            <select class="rounded-xl border border-slate-300 px-4 py-3" name="assignments[<?= $index ?>][module_id]">
                                <option value="">Select module</option>
                                <?php foreach ($availableModules as $module): ?>
                                    <option value="<?= (int) $module['id'] ?>" <?= (int) $assignment['module_id'] === (int) $module['id'] ? 'selected' : '' ?>>
                                        <?= cms_escape($module['module_key']) ?> (<?= cms_escape($module['module_type']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="assignments[<?= $index ?>][region_name]" value="<?= cms_escape($assignment['region_name'] ?? 'main') ?>" placeholder="Region">
                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="assignments[<?= $index ?>][sort_order]" type="number" value="<?= (int) ($assignment['sort_order'] ?? 100) ?>" placeholder="Sort">
                            <label class="inline-flex items-center gap-3 rounded-xl border border-slate-300 px-4 py-3">
                                <input type="checkbox" name="assignments[<?= $index ?>][is_enabled]" value="1" <?= !empty($assignment['is_enabled']) ? 'checked' : '' ?>>
                                <span>Enabled</span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="rounded-xl bg-primary px-6 py-3 font-semibold text-white" type="submit">Save Page</button>
        </form>
    </div>
    <script>
        const availableModules = <?= json_encode(array_map(static fn ($module) => ['id' => (int) $module['id'], 'label' => $module['module_key'] . ' (' . $module['module_type'] . ')'], $availableModules), JSON_UNESCAPED_SLASHES) ?>;
        let moduleIndex = <?= count($assignments) ?>;
        document.getElementById('add-module-row').addEventListener('click', () => {
            const wrapper = document.createElement('div');
            wrapper.className = 'grid gap-4 rounded-2xl border border-slate-200 p-4 md:grid-cols-4';
            const options = ['<option value="">Select module</option>'].concat(availableModules.map(module => `<option value="${module.id}">${module.label}</option>`)).join('');
            wrapper.innerHTML = `
                <select class="rounded-xl border border-slate-300 px-4 py-3" name="assignments[${moduleIndex}][module_id]">${options}</select>
                <input class="rounded-xl border border-slate-300 px-4 py-3" name="assignments[${moduleIndex}][region_name]" value="main" placeholder="Region">
                <input class="rounded-xl border border-slate-300 px-4 py-3" name="assignments[${moduleIndex}][sort_order]" type="number" value="100" placeholder="Sort">
                <label class="inline-flex items-center gap-3 rounded-xl border border-slate-300 px-4 py-3"><input type="checkbox" name="assignments[${moduleIndex}][is_enabled]" value="1" checked><span>Enabled</span></label>
            `;
            document.getElementById('module-rows').appendChild(wrapper);
            moduleIndex += 1;
        });
    </script>
</body>
</html>
