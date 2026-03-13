<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

function cms_module_editor_buttons(array $buttons, int $minimumRows = 2): array
{
    $rows = [];
    foreach ($buttons as $button) {
        if (!is_array($button)) {
            continue;
        }
        $rows[] = [
            'text' => cms_trimmed($button['text'] ?? ''),
            'href' => cms_trimmed($button['href'] ?? ''),
            'style' => cms_trimmed($button['style'] ?? 'primary') ?: 'primary',
        ];
    }

    while (count($rows) < $minimumRows) {
        $rows[] = ['text' => '', 'href' => '', 'style' => 'primary'];
    }

    return $rows;
}

function cms_module_editor_feature_items(array $items, int $minimumRows = 3): array
{
    $rows = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $rows[] = [
            'eyebrow' => cms_trimmed($item['eyebrow'] ?? ''),
            'title' => cms_trimmed($item['title'] ?? ''),
            'text' => cms_trimmed($item['text'] ?? ''),
            'bullets_text' => implode("\n", array_values(array_filter(array_map('cms_trimmed', $item['bullets'] ?? [])))),
            'meta' => cms_trimmed($item['meta'] ?? ''),
        ];
    }

    while (count($rows) < $minimumRows) {
        $rows[] = ['eyebrow' => '', 'title' => '', 'text' => '', 'bullets_text' => '', 'meta' => ''];
    }

    return $rows;
}

function cms_module_editor_stats_items(array $items, int $minimumRows = 3): array
{
    $rows = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $rows[] = [
            'value' => cms_trimmed($item['value'] ?? ''),
            'label' => cms_trimmed($item['label'] ?? ''),
            'description' => cms_trimmed($item['description'] ?? ''),
        ];
    }

    while (count($rows) < $minimumRows) {
        $rows[] = ['value' => '', 'label' => '', 'description' => ''];
    }

    return $rows;
}

function cms_module_editor_cards(array $cards, int $minimumRows = 4): array
{
    $rows = [];
    foreach ($cards as $card) {
        if (!is_array($card)) {
            continue;
        }
        $rows[] = [
            'title' => cms_trimmed($card['title'] ?? ''),
            'text' => cms_trimmed($card['text'] ?? ''),
            'image' => cms_trimmed($card['image'] ?? ''),
            'href' => cms_trimmed($card['href'] ?? ''),
            'cta' => cms_trimmed($card['cta'] ?? ''),
        ];
    }

    while (count($rows) < $minimumRows) {
        $rows[] = ['title' => '', 'text' => '', 'image' => '', 'href' => '', 'cta' => ''];
    }

    return $rows;
}

function cms_module_editor_settings_json(string $moduleType, array $settingsInput, string $fallbackJson): string
{
    $settings = cms_decode_json($fallbackJson, []);

    switch ($moduleType) {
        case 'media_split':
            $settings['image_position'] = ($settingsInput['image_position'] ?? 'right') === 'left' ? 'left' : 'right';
            $settings['section_class'] = cms_trimmed($settingsInput['section_class'] ?? ($settings['section_class'] ?? 'bg-white py-20'));
            break;

        case 'feature_list':
            $settings['columns'] = max(1, min(4, (int) ($settingsInput['columns'] ?? ($settings['columns'] ?? 3))));
            $settings['section_class'] = cms_trimmed($settingsInput['section_class'] ?? ($settings['section_class'] ?? 'bg-slate-50 py-20'));
            break;

        case 'rich_text':
            $settings['section_class'] = cms_trimmed($settingsInput['section_class'] ?? ($settings['section_class'] ?? 'py-20 bg-white'));
            break;

        default:
            break;
    }

    return cms_encode_json($settings);
}

function cms_module_editor_content_json(string $moduleType, array $translationInput, string $fallbackJson): string
{
    $fallback = cms_decode_json($fallbackJson, []);

    $buttons = [];
    foreach (($translationInput['buttons'] ?? []) as $button) {
        if (!is_array($button)) {
            continue;
        }
        $text = cms_trimmed($button['text'] ?? '');
        $href = cms_trimmed($button['href'] ?? '');
        if ($text === '' && $href === '') {
            continue;
        }

        $buttons[] = [
            'text' => $text,
            'href' => $href,
            'style' => cms_trimmed($button['style'] ?? 'primary') ?: 'primary',
        ];
    }

    switch ($moduleType) {
        case 'hero':
            return cms_encode_json([
                'label' => cms_trimmed($translationInput['label'] ?? ''),
                'title_html' => (string) ($translationInput['title_html'] ?? ''),
                'subtitle_html' => (string) ($translationInput['subtitle_html'] ?? ''),
                'buttons' => $buttons,
            ]);

        case 'media_split':
            return cms_encode_json([
                'image' => cms_trimmed($translationInput['image'] ?? ''),
                'image_alt' => cms_trimmed($translationInput['image_alt'] ?? ''),
                'buttons' => $buttons,
            ]);

        case 'feature_list':
            $items = [];
            foreach (($translationInput['items'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $bullets = array_values(array_filter(array_map(
                    'cms_trimmed',
                    preg_split('/\r\n|\r|\n/', (string) ($item['bullets_text'] ?? ''))
                )));

                if (
                    cms_trimmed($item['eyebrow'] ?? '') === '' &&
                    cms_trimmed($item['title'] ?? '') === '' &&
                    cms_trimmed($item['text'] ?? '') === '' &&
                    $bullets === [] &&
                    cms_trimmed($item['meta'] ?? '') === ''
                ) {
                    continue;
                }

                $items[] = [
                    'eyebrow' => cms_trimmed($item['eyebrow'] ?? ''),
                    'title' => cms_trimmed($item['title'] ?? ''),
                    'text' => cms_trimmed($item['text'] ?? ''),
                    'bullets' => $bullets,
                    'meta' => cms_trimmed($item['meta'] ?? ''),
                ];
            }

            return cms_encode_json(['items' => $items]);

        case 'stats_grid':
            $items = [];
            foreach (($translationInput['items'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }
                if (
                    cms_trimmed($item['value'] ?? '') === '' &&
                    cms_trimmed($item['label'] ?? '') === '' &&
                    cms_trimmed($item['description'] ?? '') === ''
                ) {
                    continue;
                }
                $items[] = [
                    'value' => cms_trimmed($item['value'] ?? ''),
                    'label' => cms_trimmed($item['label'] ?? ''),
                    'description' => cms_trimmed($item['description'] ?? ''),
                ];
            }

            return cms_encode_json(['items' => $items]);

        case 'card_grid':
            $cards = [];
            foreach (($translationInput['cards'] ?? []) as $card) {
                if (!is_array($card)) {
                    continue;
                }
                if (
                    cms_trimmed($card['title'] ?? '') === '' &&
                    cms_trimmed($card['text'] ?? '') === '' &&
                    cms_trimmed($card['image'] ?? '') === '' &&
                    cms_trimmed($card['href'] ?? '') === '' &&
                    cms_trimmed($card['cta'] ?? '') === ''
                ) {
                    continue;
                }
                $cards[] = [
                    'title' => cms_trimmed($card['title'] ?? ''),
                    'text' => cms_trimmed($card['text'] ?? ''),
                    'image' => cms_trimmed($card['image'] ?? ''),
                    'href' => cms_trimmed($card['href'] ?? ''),
                    'cta' => cms_trimmed($card['cta'] ?? ''),
                ];
            }

            return cms_encode_json(['cards' => $cards]);

        case 'cta_banner':
            return cms_encode_json(['buttons' => $buttons]);

        default:
            return cms_trimmed($translationInput['content_json'] ?? $fallbackJson) ?: cms_encode_json($fallback);
    }
}

$languages = cms_languages();
$moduleId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$module = cms_admin_module($moduleId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleType = $_POST['module_type'] ?? 'rich_text';
    $translations = [];
    foreach ($languages as $language) {
        $code = $language['code'];
        $translationInput = $_POST['translation'][$code] ?? [];
        $translations[$code] = [
            'title' => $translationInput['title'] ?? '',
            'kicker' => $translationInput['kicker'] ?? '',
            'subtitle' => $translationInput['subtitle'] ?? '',
            'content_html' => $translationInput['content_html'] ?? '',
            'content_json' => cms_module_editor_content_json($moduleType, $translationInput, $translationInput['content_json'] ?? '{}'),
        ];
    }

    $savedId = cms_upsert_module([
        'id' => $_POST['id'] ?? null,
        'module_key' => $_POST['module_key'] ?? '',
        'module_type' => $moduleType,
        'variant' => $_POST['variant'] ?? 'default',
        'status' => $_POST['status'] ?? 'published',
        'settings_json' => cms_module_editor_settings_json($moduleType, $_POST['settings'] ?? [], $_POST['settings_json'] ?? '{}'),
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

$moduleType = $module['module_type'];
$settings = cms_decode_json($module['settings_json'], []);
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
                                <option value="<?= $type ?>" <?= $moduleType === $type ? 'selected' : '' ?>><?= $type ?></option>
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

                <?php if ($moduleType === 'media_split'): ?>
                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Image Position</label>
                            <select class="w-full rounded-xl border border-slate-300 px-4 py-3" name="settings[image_position]">
                                <option value="right" <?= ($settings['image_position'] ?? 'right') === 'right' ? 'selected' : '' ?>>Right</option>
                                <option value="left" <?= ($settings['image_position'] ?? 'right') === 'left' ? 'selected' : '' ?>>Left</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Section Class</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="settings[section_class]" value="<?= cms_escape($settings['section_class'] ?? 'bg-white py-20') ?>">
                        </div>
                    </div>
                <?php elseif ($moduleType === 'feature_list'): ?>
                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Columns</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" type="number" min="1" max="4" name="settings[columns]" value="<?= (int) ($settings['columns'] ?? 3) ?>">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium">Section Class</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="settings[section_class]" value="<?= cms_escape($settings['section_class'] ?? 'bg-slate-50 py-20') ?>">
                        </div>
                    </div>
                <?php elseif ($moduleType === 'rich_text'): ?>
                    <div class="mt-6">
                        <label class="mb-2 block text-sm font-medium">Section Class</label>
                        <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="settings[section_class]" value="<?= cms_escape($settings['section_class'] ?? 'py-20 bg-white') ?>">
                    </div>
                <?php else: ?>
                    <div class="mt-6">
                        <label class="mb-2 block text-sm font-medium">Advanced Settings JSON</label>
                        <textarea class="min-h-32 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="settings_json"><?= cms_escape($module['settings_json']) ?></textarea>
                    </div>
                <?php endif; ?>
            </div>

            <?php foreach ($languages as $language): ?>
                <?php
                $translation = $module['translations'][$language['code']] ?? [];
                $content = cms_decode_json($translation['content_json'] ?? '{}', []);
                $buttons = cms_module_editor_buttons($content['buttons'] ?? []);
                $featureItems = cms_module_editor_feature_items($content['items'] ?? []);
                $statsItems = cms_module_editor_stats_items($content['items'] ?? []);
                $cards = cms_module_editor_cards($content['cards'] ?? []);
                ?>
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

                        <?php if ($moduleType === 'hero'): ?>
                            <div>
                                <label class="mb-2 block text-sm font-medium">Hero Label</label>
                                <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][label]" value="<?= cms_escape($content['label'] ?? '') ?>">
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Hero Title HTML</label>
                                <textarea class="min-h-24 w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][title_html]"><?= cms_escape($content['title_html'] ?? '') ?></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Hero Subtitle HTML</label>
                                <textarea class="min-h-24 w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][subtitle_html]"><?= cms_escape($content['subtitle_html'] ?? '') ?></textarea>
                            </div>
                        <?php endif; ?>

                        <?php if ($moduleType === 'media_split'): ?>
                            <div>
                                <label class="mb-2 block text-sm font-medium">Image Path</label>
                                <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][image]" value="<?= cms_escape($content['image'] ?? '') ?>">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium">Image Alt</label>
                                <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][image_alt]" value="<?= cms_escape($content['image_alt'] ?? '') ?>">
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Body HTML</label>
                                <textarea class="min-h-40 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="translation[<?= cms_escape($language['code']) ?>][content_html]"><?= cms_escape($translation['content_html'] ?? '') ?></textarea>
                            </div>
                        <?php elseif ($moduleType === 'rich_text'): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Body HTML</label>
                                <textarea class="min-h-48 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="translation[<?= cms_escape($language['code']) ?>][content_html]"><?= cms_escape($translation['content_html'] ?? '') ?></textarea>
                            </div>
                        <?php endif; ?>

                        <?php if (in_array($moduleType, ['hero', 'media_split', 'cta_banner'], true)): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Buttons</label>
                                <div class="grid gap-4">
                                    <?php foreach ($buttons as $index => $button): ?>
                                        <div class="grid gap-4 rounded-2xl border border-slate-200 p-4 md:grid-cols-3">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][buttons][<?= $index ?>][text]" placeholder="Button text" value="<?= cms_escape($button['text']) ?>">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][buttons][<?= $index ?>][href]" placeholder="/en/contact" value="<?= cms_escape($button['href']) ?>">
                                            <select class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][buttons][<?= $index ?>][style]">
                                                <?php foreach (['primary', 'secondary', 'white'] as $style): ?>
                                                    <option value="<?= $style ?>" <?= $button['style'] === $style ? 'selected' : '' ?>><?= ucfirst($style) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($moduleType === 'feature_list'): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Feature Cards</label>
                                <div class="grid gap-4">
                                    <?php foreach ($featureItems as $index => $item): ?>
                                        <div class="rounded-2xl border border-slate-200 p-5">
                                            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Item <?= $index + 1 ?></p>
                                            <div class="grid gap-4 md:grid-cols-2">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][items][<?= $index ?>][eyebrow]" placeholder="Eyebrow" value="<?= cms_escape($item['eyebrow']) ?>">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][items][<?= $index ?>][title]" placeholder="Title" value="<?= cms_escape($item['title']) ?>">
                                                <textarea class="min-h-24 rounded-xl border border-slate-300 px-4 py-3 md:col-span-2" name="translation[<?= cms_escape($language['code']) ?>][items][<?= $index ?>][text]" placeholder="Main text"><?= cms_escape($item['text']) ?></textarea>
                                                <textarea class="min-h-24 rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][items][<?= $index ?>][bullets_text]" placeholder="One bullet per line"><?= cms_escape($item['bullets_text']) ?></textarea>
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][items][<?= $index ?>][meta]" placeholder="Meta label" value="<?= cms_escape($item['meta']) ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif ($moduleType === 'stats_grid'): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Stats</label>
                                <div class="grid gap-4">
                                    <?php foreach ($statsItems as $index => $item): ?>
                                        <div class="grid gap-4 rounded-2xl border border-slate-200 p-4 md:grid-cols-3">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][items][<?= $index ?>][value]" placeholder="Value" value="<?= cms_escape($item['value']) ?>">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][items][<?= $index ?>][label]" placeholder="Label" value="<?= cms_escape($item['label']) ?>">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][items][<?= $index ?>][description]" placeholder="Description" value="<?= cms_escape($item['description']) ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif ($moduleType === 'card_grid'): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Cards</label>
                                <div class="grid gap-4">
                                    <?php foreach ($cards as $index => $card): ?>
                                        <div class="rounded-2xl border border-slate-200 p-5">
                                            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Card <?= $index + 1 ?></p>
                                            <div class="grid gap-4 md:grid-cols-2">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][cards][<?= $index ?>][title]" placeholder="Title" value="<?= cms_escape($card['title']) ?>">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][cards][<?= $index ?>][image]" placeholder="/images/..." value="<?= cms_escape($card['image']) ?>">
                                                <textarea class="min-h-24 rounded-xl border border-slate-300 px-4 py-3 md:col-span-2" name="translation[<?= cms_escape($language['code']) ?>][cards][<?= $index ?>][text]" placeholder="Card text"><?= cms_escape($card['text']) ?></textarea>
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][cards][<?= $index ?>][href]" placeholder="/en/contact" value="<?= cms_escape($card['href']) ?>">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][cards][<?= $index ?>][cta]" placeholder="CTA text" value="<?= cms_escape($card['cta']) ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!in_array($moduleType, ['hero', 'media_split', 'feature_list', 'stats_grid', 'card_grid', 'cta_banner', 'rich_text'], true)): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Content HTML</label>
                                <textarea class="min-h-40 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="translation[<?= cms_escape($language['code']) ?>][content_html]"><?= cms_escape($translation['content_html'] ?? '') ?></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Advanced Content JSON</label>
                                <textarea class="min-h-48 w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm" name="translation[<?= cms_escape($language['code']) ?>][content_json]"><?= cms_escape($translation['content_json'] ?? '{}') ?></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <button class="rounded-xl bg-primary px-6 py-3 font-semibold text-white" type="submit">Save Module</button>
        </form>
    </div>
</body>
</html>
