<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$languages = cms_languages();
$moduleId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$module = cms_admin_module($moduleId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $translations = [];
    foreach ($languages as $language) {
        $code = $language['code'];
        $translations[$code] = [
            'title' => $_POST['translation'][$code]['title'] ?? '',
            'kicker' => $_POST['translation'][$code]['kicker'] ?? '',
            'subtitle' => $_POST['translation'][$code]['subtitle'] ?? '',
            'content_html' => $_POST['translation'][$code]['content_html'] ?? '',
            'content_json' => $_POST['translation'][$code]['content_json'] ?? '{}',
        ];
    }

    $savedId = cms_upsert_module([
        'id' => $_POST['id'] ?? null,
        'module_key' => $_POST['module_key'] ?? '',
        'module_type' => $_POST['module_type'] ?? 'rich_text',
        'variant' => $_POST['variant'] ?? 'default',
        'status' => $_POST['status'] ?? 'published',
        'settings_json' => $_POST['settings_json'] ?? '{}',
    ], $translations);

    cms_flash('Module saved.');
    cms_redirect('/cms/module-edit.php?id=' . $savedId);
}

$module = $module ?? [
    'id' => null,
    'module_key' => '',
    'module_type' => 'rich_text',
    'variant' => 'default',
    'status' => 'published',
    'settings_json' => '{}',
    'translations' => [],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Editor | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Content Editing</p>
                <h1 class="text-4xl font-bold"><?= $module['id'] ? 'Edit Module' : 'New Module' ?></h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/modules.php">Back</a>
            </div>
        </div>

        <?php if ($flash = cms_flash()): ?>
            <div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-emerald-700"><?= cms_escape($flash) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-8">
            <input type="hidden" name="id" value="<?= (int) ($module['id'] ?? 0) ?>">
            <div class="rounded-3xl bg-white p-8 shadow">
                <h2 class="mb-6 text-2xl font-bold">Module Definition</h2>
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Module Key</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="module_key" value="<?= cms_escape($module['module_key']) ?>" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Module Type</label>
                        <select class="w-full rounded-xl border border-slate-300 px-4 py-3" name="module_type">
                            <?php foreach (['hero', 'rich_text', 'stats_grid', 'card_grid', 'feature_list', 'media_split', 'contact_panel', 'cta_banner'] as $type): ?>
                                <option value="<?= $type ?>" <?= $module['module_type'] === $type ? 'selected' : '' ?>><?= $type ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Variant</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="variant" value="<?= cms_escape($module['variant']) ?>">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Status</label>
                        <select class="w-full rounded-xl border border-slate-300 px-4 py-3" name="status">
                            <?php foreach (['draft', 'published'] as $status): ?>
                                <option value="<?= $status ?>" <?= $module['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-6">
                    <label class="mb-2 block text-sm font-medium">Settings JSON</label>
                    <textarea class="min-h-40 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="settings_json"><?= cms_escape($module['settings_json']) ?></textarea>
                </div>
            </div>

            <?php foreach ($languages as $language): ?>
                <?php $translation = $module['translations'][$language['code']] ?? []; ?>
                <div class="rounded-3xl bg-white p-8 shadow">
                    <h2 class="mb-6 text-2xl font-bold"><?= strtoupper(cms_escape($language['code'])) ?> Content</h2>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Title</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][title]" value="<?= cms_escape($translation['title'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Kicker</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][kicker]" value="<?= cms_escape($translation['kicker'] ?? '') ?>">
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Subtitle</label>
                            <textarea class="min-h-24 w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][subtitle]"><?= cms_escape($translation['subtitle'] ?? '') ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Content HTML</label>
                            <textarea class="min-h-40 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="translation[<?= cms_escape($language['code']) ?>][content_html]"><?= cms_escape($translation['content_html'] ?? '') ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium">Content JSON</label>
                            <textarea class="min-h-48 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="translation[<?= cms_escape($language['code']) ?>][content_json]"><?= cms_escape($translation['content_json'] ?? '{}') ?></textarea>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <button class="rounded-xl bg-primary px-6 py-3 font-semibold text-white" type="submit">Save Module</button>
        </form>
    </div>
</body>
</html>
