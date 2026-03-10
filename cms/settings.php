<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$definitions = cms_setting_definitions();
$languages = cms_languages();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = cms_db();
    $baseStmt = $pdo->prepare(
        'INSERT INTO cms_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP'
    );
    $translationUpsertStmt = $pdo->prepare(
        'INSERT INTO site_setting_translations (setting_key, language_id, setting_value) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP'
    );
    $translationDeleteStmt = $pdo->prepare(
        'DELETE FROM site_setting_translations WHERE setting_key = ? AND language_id = ?'
    );

    foreach ($definitions as $key => $definition) {
        $value = trim((string) ($_POST[$key] ?? ''));
        $baseStmt->execute([$key, $value]);
    }

    foreach ($languages as $language) {
        $code = $language['code'];
        foreach ($definitions as $key => $definition) {
            if (empty($definition['translatable'])) {
                continue;
            }

            $value = trim((string) ($_POST['translation'][$code][$key] ?? ''));
            if ($value === '') {
                $translationDeleteStmt->execute([$key, (int) $language['id']]);
                continue;
            }

            $translationUpsertStmt->execute([$key, (int) $language['id'], $value]);
        }
    }

    cms_flash('Settings updated.');
    header('Location: /cms/settings.php');
    exit;
}

$settings = array_merge(cms_setting_defaults(), cms_setting_map());
$translations = cms_setting_translation_map();
$flash = cms_flash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Settings | Global Dental Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <div class="mx-auto max-w-6xl px-6 py-10">
        <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-sky-600">Server CMS</p>
                <h1 class="text-4xl font-bold">Content Settings</h1>
            </div>
            <div class="flex gap-3">
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/dashboard.php">Dashboard</a>
                <a class="rounded-xl bg-white px-5 py-3 font-semibold text-slate-700 shadow hover:bg-slate-50" href="/cms/inquiries.php">Inquiries</a>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="mb-6 rounded-2xl bg-emerald-50 px-5 py-4 text-emerald-700"><?= cms_escape($flash) ?></div>
        <?php endif; ?>

        <form method="post" class="space-y-8">
            <div class="rounded-3xl bg-white p-8 shadow">
                <h2 class="mb-6 text-2xl font-bold">Global Defaults</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <?php foreach ($definitions as $key => $definition): ?>
                        <?php $isWide = ($definition['type'] ?? 'text') === 'textarea'; ?>
                        <div class="<?= $isWide ? 'md:col-span-2' : '' ?>">
                            <label class="mb-2 block text-sm font-medium text-slate-700" for="<?= cms_escape($key) ?>"><?= cms_escape($definition['label']) ?></label>
                            <?php if (($definition['type'] ?? 'text') === 'textarea'): ?>
                                <textarea class="min-h-28 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500" id="<?= cms_escape($key) ?>" name="<?= cms_escape($key) ?>"><?= cms_escape($settings[$key] ?? '') ?></textarea>
                            <?php else: ?>
                                <input class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500" id="<?= cms_escape($key) ?>" name="<?= cms_escape($key) ?>" value="<?= cms_escape($settings[$key] ?? '') ?>">
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php foreach ($languages as $language): ?>
                <div class="rounded-3xl bg-white p-8 shadow">
                    <h2 class="mb-6 text-2xl font-bold"><?= strtoupper(cms_escape($language['code'])) ?> Overrides</h2>
                    <p class="mb-6 text-sm text-slate-500">Leave fields blank to fall back to the global default.</p>
                    <div class="grid gap-6 md:grid-cols-2">
                        <?php foreach ($definitions as $key => $definition): ?>
                            <?php if (empty($definition['translatable'])): ?>
                                <?php continue; ?>
                            <?php endif; ?>
                            <?php $value = $translations[$language['code']][$key] ?? ''; ?>
                            <div class="<?= ($definition['type'] ?? 'text') === 'textarea' ? 'md:col-span-2' : '' ?>">
                                <label class="mb-2 block text-sm font-medium text-slate-700" for="<?= cms_escape($language['code'] . '_' . $key) ?>"><?= cms_escape($definition['label']) ?></label>
                                <?php if (($definition['type'] ?? 'text') === 'textarea'): ?>
                                    <textarea class="min-h-28 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500" id="<?= cms_escape($language['code'] . '_' . $key) ?>" name="translation[<?= cms_escape($language['code']) ?>][<?= cms_escape($key) ?>]"><?= cms_escape($value) ?></textarea>
                                <?php else: ?>
                                    <input class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500" id="<?= cms_escape($language['code'] . '_' . $key) ?>" name="translation[<?= cms_escape($language['code']) ?>][<?= cms_escape($key) ?>]" value="<?= cms_escape($value) ?>">
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="grid gap-6 md:grid-cols-2">
                <button class="rounded-xl bg-slate-900 px-6 py-3 font-semibold text-white hover:bg-slate-800 md:col-span-2" type="submit">Save Settings</button>
            </div>
        </form>
    </div>
</body>
</html>
