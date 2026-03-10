<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';
cms_require_login();

$definitions = [
    'home_intro_title' => 'Homepage Intro Title',
    'home_intro_text' => 'Homepage Intro Paragraph',
    'home_scope_text' => 'Homepage Product Scope Text',
    'contact_form_intro' => 'Contact Form Intro',
    'contact_hub_intro' => 'Contact Hub Intro',
    'site_phone_display' => 'Phone Display',
    'site_phone_href' => 'Phone Link',
    'site_email_display' => 'Email Display',
    'site_email_href' => 'Email Link',
    'site_whatsapp_display' => 'WhatsApp Display',
    'site_whatsapp_href' => 'WhatsApp Link',
    'site_hk_address_html' => 'Hong Kong Address HTML',
    'site_sz_address_html' => 'Shenzhen Address HTML',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo = cms_db();
    $stmt = $pdo->prepare(
        'INSERT INTO cms_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP'
    );

    foreach ($definitions as $key => $label) {
        $value = trim((string) ($_POST[$key] ?? ''));
        $stmt->execute([$key, $value]);
    }

    cms_flash('Settings updated.');
    header('Location: /cms/settings.php');
    exit;
}

$settings = array_merge(cms_setting_defaults(), cms_setting_map());
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

        <form method="post" class="space-y-8 rounded-3xl bg-white p-8 shadow">
            <div class="grid gap-6 md:grid-cols-2">
                <?php foreach ($definitions as $key => $label): ?>
                    <div class="<?= str_ends_with($key, '_html') || str_ends_with($key, '_text') ? 'md:col-span-2' : '' ?>">
                        <label class="mb-2 block text-sm font-medium text-slate-700" for="<?= cms_escape($key) ?>"><?= cms_escape($label) ?></label>
                        <?php if (str_ends_with($key, '_html') || str_ends_with($key, '_text')): ?>
                            <textarea class="min-h-28 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500" id="<?= cms_escape($key) ?>" name="<?= cms_escape($key) ?>"><?= cms_escape($settings[$key] ?? '') ?></textarea>
                        <?php else: ?>
                            <input class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500" id="<?= cms_escape($key) ?>" name="<?= cms_escape($key) ?>" value="<?= cms_escape($settings[$key] ?? '') ?>">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="rounded-xl bg-slate-900 px-6 py-3 font-semibold text-white hover:bg-slate-800" type="submit">Save Settings</button>
        </form>
    </div>
</body>
</html>
