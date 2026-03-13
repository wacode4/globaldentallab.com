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

function cms_module_editor_rich_text_template(string $moduleKey): ?string
{
    return match ($moduleKey) {
        'downloads-files' => 'resource_groups',
        'downloads-links' => 'link_grid',
        'materials-brands' => 'logo_grid',
        'materials-charts' => 'figure_grid',
        default => null,
    };
}

function cms_module_editor_decode_text(string $value): string
{
    return trim(html_entity_decode(strip_tags($value), ENT_QUOTES, 'UTF-8'));
}

function cms_module_editor_parse_resource_groups(string $html): array
{
    $groups = [];
    if (preg_match_all('#<div class="rounded-3xl[^"]*">\s*<p[^>]*>(.*?)</p>\s*<h3[^>]*>(.*?)</h3>\s*<div class="mt-6 space-y-4">(.*?)</div>\s*</div>#s', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $items = [];
            if (preg_match_all('#<a href="([^"]+)"[^>]*>\s*<span>(.*?)</span>\s*<span>(.*?)</span>\s*</a>#s', $match[3], $itemMatches, PREG_SET_ORDER)) {
                foreach ($itemMatches as $itemMatch) {
                    $items[] = [
                        'label' => cms_module_editor_decode_text($itemMatch[2]),
                        'href' => cms_trimmed($itemMatch[1]),
                        'cta' => cms_module_editor_decode_text($itemMatch[3]),
                    ];
                }
            }

            $groups[] = [
                'eyebrow' => cms_module_editor_decode_text($match[1]),
                'title' => cms_module_editor_decode_text($match[2]),
                'items' => $items,
            ];
        }
    }

    while (count($groups) < 2) {
        $groups[] = ['eyebrow' => '', 'title' => '', 'items' => []];
    }

    foreach ($groups as &$group) {
        while (count($group['items']) < 4) {
            $group['items'][] = ['label' => '', 'href' => '', 'cta' => 'Download'];
        }
    }
    unset($group);

    return $groups;
}

function cms_module_editor_parse_link_grid(string $html): array
{
    $items = [];
    if (preg_match_all('#<a href="([^"]+)"[^>]*>(.*?)</a>#s', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $items[] = [
                'label' => cms_module_editor_decode_text($match[2]),
                'href' => cms_trimmed($match[1]),
            ];
        }
    }

    while (count($items) < 8) {
        $items[] = ['label' => '', 'href' => ''];
    }

    return $items;
}

function cms_module_editor_parse_logo_grid(string $html): array
{
    $items = [];
    if (preg_match_all('#<div class="rounded-3xl[^"]*">\s*<img src="([^"]+)" alt="([^"]*)"[^>]*>\s*<h3[^>]*>(.*?)</h3>\s*<p[^>]*>(.*?)</p>\s*</div>#s', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $items[] = [
                'image' => cms_trimmed($match[1]),
                'alt' => cms_trimmed($match[2]),
                'title' => cms_module_editor_decode_text($match[3]),
                'text' => cms_module_editor_decode_text($match[4]),
            ];
        }
    }

    while (count($items) < 10) {
        $items[] = ['image' => '', 'alt' => '', 'title' => '', 'text' => ''];
    }

    return $items;
}

function cms_module_editor_parse_figure_grid(string $html): array
{
    $items = [];
    if (preg_match_all('#<figure[^>]*>\s*<img src="([^"]+)" alt="([^"]*)"[^>]*>\s*<figcaption[^>]*>(.*?)</figcaption>\s*</figure>#s', $html, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $items[] = [
                'image' => cms_trimmed($match[1]),
                'alt' => cms_trimmed($match[2]),
                'caption' => cms_module_editor_decode_text($match[3]),
            ];
        }
    }

    while (count($items) < 3) {
        $items[] = ['image' => '', 'alt' => '', 'caption' => ''];
    }

    return $items;
}

function cms_module_editor_rich_text_state(string $template, array $content, string $contentHtml): array
{
    if ($template === 'resource_groups') {
        return ['groups' => is_array($content['groups'] ?? null) ? $content['groups'] : cms_module_editor_parse_resource_groups($contentHtml)];
    }

    if ($template === 'link_grid') {
        return ['links' => is_array($content['links'] ?? null) ? $content['links'] : cms_module_editor_parse_link_grid($contentHtml)];
    }

    if ($template === 'logo_grid') {
        return ['logos' => is_array($content['logos'] ?? null) ? $content['logos'] : cms_module_editor_parse_logo_grid($contentHtml)];
    }

    if ($template === 'figure_grid') {
        return ['figures' => is_array($content['figures'] ?? null) ? $content['figures'] : cms_module_editor_parse_figure_grid($contentHtml)];
    }

    return [];
}

function cms_module_editor_generate_rich_text(array $translationInput, string $template): array
{
    if ($template === 'resource_groups') {
        $groups = [];
        foreach (($translationInput['groups'] ?? []) as $groupInput) {
            if (!is_array($groupInput)) {
                continue;
            }
            $items = [];
            foreach (($groupInput['items'] ?? []) as $itemInput) {
                if (!is_array($itemInput)) {
                    continue;
                }
                $label = cms_trimmed($itemInput['label'] ?? '');
                $href = cms_trimmed($itemInput['href'] ?? '');
                $cta = cms_trimmed($itemInput['cta'] ?? 'Download') ?: 'Download';
                if ($label === '' && $href === '') {
                    continue;
                }
                $items[] = ['label' => $label, 'href' => $href, 'cta' => $cta];
            }

            $eyebrow = cms_trimmed($groupInput['eyebrow'] ?? '');
            $title = cms_trimmed($groupInput['title'] ?? '');
            if ($eyebrow === '' && $title === '' && $items === []) {
                continue;
            }

            $groups[] = ['eyebrow' => $eyebrow, 'title' => $title, 'items' => $items];
        }

        $html = '<div class="grid gap-8 lg:grid-cols-2 not-prose">';
        foreach ($groups as $group) {
            $html .= '<div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">';
            if ($group['eyebrow'] !== '') {
                $html .= '<p class="text-xs font-bold uppercase tracking-[0.24em] text-primary">' . cms_escape($group['eyebrow']) . '</p>';
            }
            if ($group['title'] !== '') {
                $html .= '<h3 class="mt-3 text-2xl font-bold text-navy">' . cms_escape($group['title']) . '</h3>';
            }
            $html .= '<div class="mt-6 space-y-4">';
            foreach ($group['items'] as $item) {
                $html .= '<a href="' . cms_escape($item['href']) . '" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">';
                $html .= '<span>' . cms_escape($item['label']) . '</span><span>' . cms_escape($item['cta']) . '</span></a>';
            }
            $html .= '</div></div>';
        }
        $html .= '</div>';

        return ['content_json' => cms_encode_json(['groups' => $groups]), 'content_html' => $html];
    }

    if ($template === 'link_grid') {
        $links = [];
        foreach (($translationInput['links'] ?? []) as $linkInput) {
            if (!is_array($linkInput)) {
                continue;
            }
            $label = cms_trimmed($linkInput['label'] ?? '');
            $href = cms_trimmed($linkInput['href'] ?? '');
            if ($label === '' && $href === '') {
                continue;
            }
            $links[] = ['label' => $label, 'href' => $href];
        }

        $html = '<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 not-prose">';
        foreach ($links as $link) {
            $html .= '<a href="' . cms_escape($link['href']) . '" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">' . cms_escape($link['label']) . '</a>';
        }
        $html .= '</div>';

        return ['content_json' => cms_encode_json(['links' => $links]), 'content_html' => $html];
    }

    if ($template === 'logo_grid') {
        $logos = [];
        foreach (($translationInput['logos'] ?? []) as $logoInput) {
            if (!is_array($logoInput)) {
                continue;
            }
            $image = cms_trimmed($logoInput['image'] ?? '');
            $alt = cms_trimmed($logoInput['alt'] ?? '');
            $title = cms_trimmed($logoInput['title'] ?? '');
            $text = cms_trimmed($logoInput['text'] ?? '');
            if ($image === '' && $title === '' && $text === '') {
                continue;
            }
            $logos[] = ['image' => $image, 'alt' => $alt, 'title' => $title, 'text' => $text];
        }

        $html = '<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-5 not-prose">';
        foreach ($logos as $logo) {
            $html .= '<div class="rounded-3xl border border-slate-200 bg-white p-6 text-center">';
            if ($logo['image'] !== '') {
                $html .= '<img src="' . cms_escape($logo['image']) . '" alt="' . cms_escape($logo['alt']) . '" class="mx-auto h-12 object-contain">';
            }
            $html .= '<h3 class="mt-4 text-lg font-bold text-navy">' . cms_escape($logo['title']) . '</h3>';
            $html .= '<p class="mt-2 text-sm text-slate-600">' . cms_escape($logo['text']) . '</p></div>';
        }
        $html .= '</div>';

        return ['content_json' => cms_encode_json(['logos' => $logos]), 'content_html' => $html];
    }

    if ($template === 'figure_grid') {
        $figures = [];
        foreach (($translationInput['figures'] ?? []) as $figureInput) {
            if (!is_array($figureInput)) {
                continue;
            }
            $image = cms_trimmed($figureInput['image'] ?? '');
            $alt = cms_trimmed($figureInput['alt'] ?? '');
            $caption = cms_trimmed($figureInput['caption'] ?? '');
            if ($image === '' && $caption === '') {
                continue;
            }
            $figures[] = ['image' => $image, 'alt' => $alt, 'caption' => $caption];
        }

        $html = '<div class="grid gap-6 md:grid-cols-3 not-prose">';
        foreach ($figures as $figure) {
            $html .= '<figure class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">';
            if ($figure['image'] !== '') {
                $html .= '<img src="' . cms_escape($figure['image']) . '" alt="' . cms_escape($figure['alt']) . '" class="h-80 w-full object-cover object-top">';
            }
            $html .= '<figcaption class="p-4 text-sm text-slate-600">' . cms_escape($figure['caption']) . '</figcaption></figure>';
        }
        $html .= '</div>';

        return ['content_json' => cms_encode_json(['figures' => $figures]), 'content_html' => $html];
    }

    return [
        'content_json' => cms_trimmed($translationInput['content_json'] ?? '{}'),
        'content_html' => (string) ($translationInput['content_html'] ?? ''),
    ];
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

function cms_module_editor_content_json(string $moduleType, string $moduleKey, array $translationInput, string $fallbackJson): array
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
            return [
                'content_json' => cms_encode_json([
                    'label' => cms_trimmed($translationInput['label'] ?? ''),
                    'title_html' => (string) ($translationInput['title_html'] ?? ''),
                    'subtitle_html' => (string) ($translationInput['subtitle_html'] ?? ''),
                    'buttons' => $buttons,
                ]),
                'content_html' => (string) ($translationInput['content_html'] ?? ''),
            ];
        case 'media_split':
            return [
                'content_json' => cms_encode_json([
                    'image' => cms_trimmed($translationInput['image'] ?? ''),
                    'image_alt' => cms_trimmed($translationInput['image_alt'] ?? ''),
                    'buttons' => $buttons,
                ]),
                'content_html' => (string) ($translationInput['content_html'] ?? ''),
            ];
        case 'feature_list':
            $items = [];
            foreach (($translationInput['items'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $bullets = array_values(array_filter(array_map('cms_trimmed', preg_split('/\r\n|\r|\n/', (string) ($item['bullets_text'] ?? '')))));
                if (cms_trimmed($item['eyebrow'] ?? '') === '' && cms_trimmed($item['title'] ?? '') === '' && cms_trimmed($item['text'] ?? '') === '' && $bullets === [] && cms_trimmed($item['meta'] ?? '') === '') {
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
            return ['content_json' => cms_encode_json(['items' => $items]), 'content_html' => (string) ($translationInput['content_html'] ?? '')];
        case 'stats_grid':
            $items = [];
            foreach (($translationInput['items'] ?? []) as $item) {
                if (!is_array($item)) {
                    continue;
                }
                if (cms_trimmed($item['value'] ?? '') === '' && cms_trimmed($item['label'] ?? '') === '' && cms_trimmed($item['description'] ?? '') === '') {
                    continue;
                }
                $items[] = [
                    'value' => cms_trimmed($item['value'] ?? ''),
                    'label' => cms_trimmed($item['label'] ?? ''),
                    'description' => cms_trimmed($item['description'] ?? ''),
                ];
            }
            return ['content_json' => cms_encode_json(['items' => $items]), 'content_html' => (string) ($translationInput['content_html'] ?? '')];
        case 'card_grid':
            $cards = [];
            foreach (($translationInput['cards'] ?? []) as $card) {
                if (!is_array($card)) {
                    continue;
                }
                if (cms_trimmed($card['title'] ?? '') === '' && cms_trimmed($card['text'] ?? '') === '' && cms_trimmed($card['image'] ?? '') === '' && cms_trimmed($card['href'] ?? '') === '' && cms_trimmed($card['cta'] ?? '') === '') {
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
            return ['content_json' => cms_encode_json(['cards' => $cards]), 'content_html' => (string) ($translationInput['content_html'] ?? '')];
        case 'cta_banner':
            return ['content_json' => cms_encode_json(['buttons' => $buttons]), 'content_html' => (string) ($translationInput['content_html'] ?? '')];
        case 'rich_text':
            $template = cms_module_editor_rich_text_template($moduleKey);
            if ($template !== null) {
                return cms_module_editor_generate_rich_text($translationInput, $template);
            }
            return [
                'content_json' => cms_trimmed($translationInput['content_json'] ?? $fallbackJson) ?: cms_encode_json($fallback),
                'content_html' => (string) ($translationInput['content_html'] ?? ''),
            ];
        default:
            return [
                'content_json' => cms_trimmed($translationInput['content_json'] ?? $fallbackJson) ?: cms_encode_json($fallback),
                'content_html' => (string) ($translationInput['content_html'] ?? ''),
            ];
    }
}

$languages = cms_languages();
$moduleId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$module = cms_admin_module($moduleId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $moduleType = $_POST['module_type'] ?? 'rich_text';
    $moduleKey = $_POST['module_key'] ?? '';
    $translations = [];
    foreach ($languages as $language) {
        $code = $language['code'];
        $translationInput = $_POST['translation'][$code] ?? [];
        $contentPayload = cms_module_editor_content_json($moduleType, $moduleKey, $translationInput, $translationInput['content_json'] ?? '{}');
        $translations[$code] = [
            'title' => $translationInput['title'] ?? '',
            'kicker' => $translationInput['kicker'] ?? '',
            'subtitle' => $translationInput['subtitle'] ?? '',
            'content_html' => $contentPayload['content_html'],
            'content_json' => $contentPayload['content_json'],
        ];
    }

    $savedId = cms_upsert_module([
        'id' => $_POST['id'] ?? null,
        'module_key' => $moduleKey,
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
$richTextTemplate = $moduleType === 'rich_text' ? cms_module_editor_rich_text_template($module['module_key']) : null;
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
                    <div class="mt-6 grid gap-6 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium">Section Class</label>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3" name="settings[section_class]" value="<?= cms_escape($settings['section_class'] ?? 'py-20 bg-white') ?>">
                        </div>
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                            <span class="font-semibold text-slate-800">Editor Template:</span>
                            <?= cms_escape($richTextTemplate ?: 'advanced-html') ?>
                        </div>
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
                $richState = $richTextTemplate ? cms_module_editor_rich_text_state($richTextTemplate, $content, (string) ($translation['content_html'] ?? '')) : [];
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
                        <?php elseif ($moduleType === 'rich_text' && $richTextTemplate === null): ?>
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
                        <?php elseif ($moduleType === 'rich_text' && $richTextTemplate === 'resource_groups'): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Download Groups</label>
                                <div class="grid gap-4">
                                    <?php foreach (($richState['groups'] ?? []) as $groupIndex => $group): ?>
                                        <div class="rounded-2xl border border-slate-200 p-5">
                                            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Group <?= $groupIndex + 1 ?></p>
                                            <div class="grid gap-4 md:grid-cols-2">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][groups][<?= $groupIndex ?>][eyebrow]" placeholder="Eyebrow" value="<?= cms_escape($group['eyebrow'] ?? '') ?>">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][groups][<?= $groupIndex ?>][title]" placeholder="Group title" value="<?= cms_escape($group['title'] ?? '') ?>">
                                            </div>
                                            <div class="mt-4 grid gap-3">
                                                <?php foreach (($group['items'] ?? []) as $itemIndex => $item): ?>
                                                    <div class="grid gap-3 md:grid-cols-3">
                                                        <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][groups][<?= $groupIndex ?>][items][<?= $itemIndex ?>][label]" placeholder="File label" value="<?= cms_escape($item['label'] ?? '') ?>">
                                                        <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][groups][<?= $groupIndex ?>][items][<?= $itemIndex ?>][href]" placeholder="/downloads/file.pdf" value="<?= cms_escape($item['href'] ?? '') ?>">
                                                        <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][groups][<?= $groupIndex ?>][items][<?= $itemIndex ?>][cta]" placeholder="Download" value="<?= cms_escape($item['cta'] ?? 'Download') ?>">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif ($moduleType === 'rich_text' && $richTextTemplate === 'link_grid'): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Reference Links</label>
                                <div class="grid gap-3">
                                    <?php foreach (($richState['links'] ?? []) as $index => $link): ?>
                                        <div class="grid gap-3 md:grid-cols-2">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][links][<?= $index ?>][label]" placeholder="Link label" value="<?= cms_escape($link['label'] ?? '') ?>">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][links][<?= $index ?>][href]" placeholder="https://example.com" value="<?= cms_escape($link['href'] ?? '') ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif ($moduleType === 'rich_text' && $richTextTemplate === 'logo_grid'): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Logo Cards</label>
                                <div class="grid gap-4">
                                    <?php foreach (($richState['logos'] ?? []) as $index => $logo): ?>
                                        <div class="rounded-2xl border border-slate-200 p-5">
                                            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.18em] text-slate-400">Logo <?= $index + 1 ?></p>
                                            <div class="grid gap-4 md:grid-cols-2">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][logos][<?= $index ?>][image]" placeholder="/images/logo.png" value="<?= cms_escape($logo['image'] ?? '') ?>">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][logos][<?= $index ?>][alt]" placeholder="Image alt" value="<?= cms_escape($logo['alt'] ?? '') ?>">
                                                <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][logos][<?= $index ?>][title]" placeholder="Brand name" value="<?= cms_escape($logo['title'] ?? '') ?>">
                                                <textarea class="min-h-24 rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][logos][<?= $index ?>][text]" placeholder="Description"><?= cms_escape($logo['text'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php elseif ($moduleType === 'rich_text' && $richTextTemplate === 'figure_grid'): ?>
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium">Image Figures</label>
                                <div class="grid gap-4">
                                    <?php foreach (($richState['figures'] ?? []) as $index => $figure): ?>
                                        <div class="grid gap-4 rounded-2xl border border-slate-200 p-5 md:grid-cols-3">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][figures][<?= $index ?>][image]" placeholder="/images/chart.png" value="<?= cms_escape($figure['image'] ?? '') ?>">
                                            <input class="rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][figures][<?= $index ?>][alt]" placeholder="Image alt" value="<?= cms_escape($figure['alt'] ?? '') ?>">
                                            <textarea class="min-h-24 rounded-xl border border-slate-300 px-4 py-3" name="translation[<?= cms_escape($language['code']) ?>][figures][<?= $index ?>][caption]" placeholder="Caption"><?= cms_escape($figure['caption'] ?? '') ?></textarea>
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
