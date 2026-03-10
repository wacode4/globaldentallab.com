<!DOCTYPE html>
<html lang="<?= cms_escape($page['language']['code']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= cms_escape($page['seo_title'] ?: $page['page_name']) ?> | Global Dental Lab</title>
    <meta name="description" content="<?= cms_escape($page['seo_description']) ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0083C9',
                        'primary-dark': '#006BA6',
                        accent: '#E6B00F',
                        navy: '#001B39',
                        'navy-light': '#0D2C52',
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="/css/shared-styles.css?v=20260309-2">
</head>
<?php $siteSettings = cms_setting_map($page['language']['code']); ?>
<body class="bg-white text-navy" style="font-family: 'Montserrat', sans-serif; line-height: 1.8;">
    <header class="border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a class="text-xl font-bold text-navy" href="/<?= cms_escape($page['language']['code']) ?>/"><?= cms_escape($siteSettings['site_name']) ?></a>
            <nav class="hidden gap-6 md:flex">
                <?php foreach ($page['navigation'] as $item): ?>
                    <a class="text-sm font-semibold text-slate-600 hover:text-primary" href="<?= cms_escape($item['href'] ?? '#') ?>" target="<?= cms_escape($item['target'] ?? '_self') ?>">
                        <?= cms_escape($item['nav_label'] ?: $item['page_name']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="flex items-center gap-3">
                <?php foreach ($page['languages'] as $language): ?>
                    <?php $href = $page['slug'] === 'home' ? '/' . $language['code'] . '/' : '/' . $language['code'] . '/' . $page['slug']; ?>
                    <a class="rounded-full px-3 py-1 text-xs font-bold <?= $language['code'] === $page['language']['code'] ? 'bg-navy text-white' : 'bg-slate-100 text-slate-600' ?>" href="<?= cms_escape($href) ?>">
                        <?= strtoupper(cms_escape($language['code'])) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </header>

    <?= $content ?>

    <footer class="mt-16 bg-navy py-14 text-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-6 md:grid-cols-4">
            <div>
                <h3 class="mb-3 text-xl font-bold"><?= cms_escape($siteSettings['site_name']) ?></h3>
                <p class="text-sm text-slate-300"><?= cms_escape($siteSettings['site_footer_blurb']) ?></p>
            </div>
            <div>
                <h4 class="mb-3 font-semibold">Dynamic Pages</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <?php foreach (($page['footer_navigation'] ?: $page['navigation']) as $item): ?>
                        <li><a href="<?= cms_escape($item['href'] ?? '#') ?>" class="hover:text-white" target="<?= cms_escape($item['target'] ?? '_self') ?>"><?= cms_escape($item['nav_label'] ?: $item['page_name']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h4 class="mb-3 font-semibold">CMS</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><a href="/cms/dashboard.php" class="hover:text-white">Dashboard</a></li>
                    <li><a href="/cms/pages.php" class="hover:text-white">Pages</a></li>
                    <li><a href="/cms/modules.php" class="hover:text-white">Modules</a></li>
                </ul>
            </div>
            <div>
                <h4 class="mb-3 font-semibold">Contact</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><a class="hover:text-white" href="<?= cms_escape($siteSettings['site_phone_href']) ?>"><?= cms_escape($siteSettings['site_phone_display']) ?></a></li>
                    <li><a class="hover:text-white" href="<?= cms_escape($siteSettings['site_email_href']) ?>"><?= cms_escape($siteSettings['site_email_display']) ?></a></li>
                    <li><?= $siteSettings['site_hk_address_html'] ?></li>
                    <li><?= $siteSettings['site_sz_address_html'] ?></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
