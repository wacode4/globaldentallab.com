<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$availablePages = cms_admin_pages();
$languages = cms_languages();
$menuId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$menu = cms_admin_menu($menuId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = [];
    foreach (($_POST['items'] ?? []) as $item) {
        $translations = [];
        foreach ($languages as $language) {
            $code = $language['code'];
            $translations[$code] = [
                'custom_label' => $item['translations'][$code]['custom_label'] ?? '',
            ];
        }

        $items[] = [
            'page_id' => $item['page_id'] ?? '',
            'custom_label' => $item['custom_label'] ?? '',
            'custom_url' => $item['custom_url'] ?? '',
            'sort_order' => $item['sort_order'] ?? 100,
            'target' => $item['target'] ?? '_self',
            'is_enabled' => !empty($item['is_enabled']),
            'translations' => $translations,
        ];
    }

    $savedId = cms_upsert_menu([
        'id' => $_POST['id'] ?? null,
        'menu_key' => $_POST['menu_key'] ?? '',
        'name' => $_POST['name'] ?? '',
    ], $items);

    cms_flash('Menu saved.');
    cms_redirect('/cms/menu-edit.php?id=' . $savedId);
}

$menu = $menu ?? [
    'id' => null,
    'menu_key' => '',
    'name' => '',
    'items' => [],
];
$items = $menu['items'] ?: [[
    'page_id' => '',
    'custom_label' => '',
    'custom_url' => '',
    'sort_order' => 100,
    'target' => '_self',
    'is_enabled' => 1,
    'translations' => [],
]];
$translationGridClass = match (count($languages)) {
    2 => 'md:grid-cols-2',
    default => count($languages) >= 3 ? 'md:grid-cols-3' : 'md:grid-cols-1',
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Editor | Global Dental Lab CMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-7xl px-6 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Content Editing</p>
                <h1 class="text-4xl font-bold"><?= $menu['id'] ? 'Edit Menu' : 'New Menu' ?></h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow" href="/cms/menus.php">Back</a>
            </div>
        </div>

        <?php if ($flash = cms_flash()): ?>
            <div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-emerald-700"><?= cms_escape($flash) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-8">
            <input type="hidden" name="id" value="<?= (int) ($menu['id'] ?? 0) ?>">
            <div class="rounded-3xl bg-white p-8 shadow">
                <h2 class="mb-6 text-2xl font-bold">Menu Definition</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-medium">Menu Key</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="menu_key" value="<?= cms_escape($menu['menu_key']) ?>" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium">Name</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="name" value="<?= cms_escape($menu['name']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-8 shadow">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-2xl font-bold">Menu Items</h2>
                    <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white" id="add-menu-item" type="button">Add Item</button>
                </div>
                <div class="space-y-4" id="menu-item-rows">
                    <?php foreach ($items as $index => $item): ?>
                        <div class="space-y-4 rounded-2xl border border-slate-200 p-4">
                            <div class="grid gap-4 md:grid-cols-6">
                                <select class="rounded-xl border border-slate-300 px-4 py-3" name="items[<?= $index ?>][page_id]">
                                    <option value="">Custom URL</option>
                                    <?php foreach ($availablePages as $page): ?>
                                        <option value="<?= (int) $page['id'] ?>" <?= (int) ($item['page_id'] ?? 0) === (int) $page['id'] ? 'selected' : '' ?>>
                                            <?= cms_escape($page['page_name'] ?: $page['slug']) ?> (<?= cms_escape($page['slug']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="items[<?= $index ?>][custom_label]" value="<?= cms_escape($item['custom_label'] ?? '') ?>" placeholder="Fallback label">
                                <input class="rounded-xl border border-slate-300 px-4 py-3 md:col-span-2" name="items[<?= $index ?>][custom_url]" value="<?= cms_escape($item['custom_url'] ?? '') ?>" placeholder="/path or https://...">
                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="items[<?= $index ?>][sort_order]" type="number" value="<?= (int) ($item['sort_order'] ?? 100) ?>" placeholder="Sort">
                                <label class="inline-flex items-center gap-3 rounded-xl border border-slate-300 px-4 py-3">
                                    <input type="checkbox" name="items[<?= $index ?>][is_enabled]" value="1" <?= !empty($item['is_enabled']) ? 'checked' : '' ?>>
                                    <span>Enabled</span>
                                </label>
                            </div>
                            <div class="grid gap-4 <?= $translationGridClass ?>">
                                <?php foreach ($languages as $language): ?>
                                    <?php $translation = $item['translations'][$language['code']] ?? []; ?>
                                    <div>
                                        <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500"><?= cms_escape(strtoupper($language['code'])) ?> Label</label>
                                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="items[<?= $index ?>][translations][<?= cms_escape($language['code']) ?>][custom_label]" value="<?= cms_escape($translation['custom_label'] ?? '') ?>" placeholder="Optional override">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="items[<?= $index ?>][target]" value="<?= cms_escape($item['target'] ?? '_self') ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button class="rounded-xl bg-primary px-6 py-3 font-semibold text-white" type="submit">Save Menu</button>
        </form>
    </div>
    <script>
        const availablePages = <?= json_encode(array_map(static fn ($page) => ['id' => (int) $page['id'], 'label' => ($page['page_name'] ?: $page['slug']) . ' (' . $page['slug'] . ')'], $availablePages), JSON_UNESCAPED_SLASHES) ?>;
        const languages = <?= json_encode(array_map(static fn ($language) => ['code' => $language['code'], 'label' => strtoupper($language['code'])], $languages), JSON_UNESCAPED_SLASHES) ?>;
        let itemIndex = <?= count($items) ?>;
        document.getElementById('add-menu-item').addEventListener('click', () => {
            const wrapper = document.createElement('div');
            wrapper.className = 'space-y-4 rounded-2xl border border-slate-200 p-4';
            const options = ['<option value="">Custom URL</option>'].concat(availablePages.map(page => `<option value="${page.id}">${page.label}</option>`)).join('');
            const translationInputs = languages.map(language => `
                <div>
                    <label class="mb-2 block text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">${language.label} Label</label>
                    <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="items[${itemIndex}][translations][${language.code}][custom_label]" placeholder="Optional override">
                </div>
            `).join('');
            wrapper.innerHTML = `
                <div class="grid gap-4 md:grid-cols-6">
                    <select class="rounded-xl border border-slate-300 px-4 py-3" name="items[${itemIndex}][page_id]">${options}</select>
                    <input class="rounded-xl border border-slate-300 px-4 py-3" name="items[${itemIndex}][custom_label]" placeholder="Fallback label">
                    <input class="rounded-xl border border-slate-300 px-4 py-3 md:col-span-2" name="items[${itemIndex}][custom_url]" placeholder="/path or https://...">
                    <input class="rounded-xl border border-slate-300 px-4 py-3" name="items[${itemIndex}][sort_order]" type="number" value="100" placeholder="Sort">
                    <label class="inline-flex items-center gap-3 rounded-xl border border-slate-300 px-4 py-3"><input type="checkbox" name="items[${itemIndex}][is_enabled]" value="1" checked><span>Enabled</span></label>
                </div>
                <div class="grid gap-4 <?= $translationGridClass ?>">
                    ${translationInputs}
                </div>
                <input type="hidden" name="items[${itemIndex}][target]" value="_self">
            `;
            document.getElementById('menu-item-rows').appendChild(wrapper);
            itemIndex += 1;
        });
    </script>
</body>
</html>
