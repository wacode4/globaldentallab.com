<!DOCTYPE html>
<html lang="<?= cms_escape($page['language']['code']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= cms_escape($page['meta']['title']) ?></title>
    <meta name="description" content="<?= cms_escape($page['meta']['description']) ?>">
    <meta property="og:type" content="<?= cms_escape($page['meta']['type']) ?>">
    <meta property="og:url" content="<?= cms_escape($page['meta']['canonical']) ?>">
    <meta property="og:title" content="<?= cms_escape($page['meta']['title']) ?>">
    <meta property="og:description" content="<?= cms_escape($page['meta']['description']) ?>">
    <meta property="og:image" content="<?= cms_escape($page['meta']['image']) ?>">
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= cms_escape($page['meta']['canonical']) ?>">
    <meta property="twitter:title" content="<?= cms_escape($page['meta']['title']) ?>">
    <meta property="twitter:description" content="<?= cms_escape($page['meta']['description']) ?>">
    <meta property="twitter:image" content="<?= cms_escape($page['meta']['image']) ?>">
    <link rel="canonical" href="<?= cms_escape($page['meta']['canonical']) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Albert+Sans:wght@700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script data-cfasync="false" src="https://cdn.tailwindcss.com"></script>
    <script data-cfasync="false">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0083C9',
                        'primary-dark': '#006BA6',
                        accent: '#E6B00F',
                        navy: '#001B39',
                        'navy-light': '#0D2C52'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['"Albert Sans"', 'sans-serif'],
                        body: ['Inter', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="/css/shared-styles.css?v=20260309-2">
    <style>
        .catalog-rich h3 {
            color: #001B39;
            font-size: 1.125rem;
            font-weight: 700;
            margin: 0 0 0.75rem;
        }

        .catalog-rich p {
            color: #4B5563;
            margin: 0 0 1rem;
        }

        .catalog-rich ul,
        .catalog-rich ol {
            display: grid;
            gap: 0.75rem;
            margin: 0 0 1.5rem;
            padding: 0;
            list-style: none;
        }

        .catalog-rich li {
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            border-radius: 1rem;
            color: #4B5563;
            padding: 1rem 1.125rem;
        }

        .catalog-rich strong {
            color: #001B39;
        }
    </style>
</head>
<body class="bg-white text-navy font-body" style="font-family: 'Montserrat', sans-serif; line-height: 2;">
    <div id="header-container"></div>
    <div id="hero-container"></div>

    <?= $content ?>

    <footer class="bg-navy text-white py-16 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-10">
                <div>
                    <h3 class="text-xl font-bold mb-4"><?= cms_escape($page['site_settings']['site_name']) ?></h3>
                    <p class="text-gray-400"><?= cms_escape($page['site_settings']['site_footer_blurb']) ?></p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Products</h4>
                    <ul class="space-y-2 text-gray-400">
                        <?php foreach ($page['footer_categories'] as $category): ?>
                            <li><a href="<?= cms_escape($category['href']) ?>" class="hover:text-white"><?= cms_escape($category['nav_label'] ?: $category['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Resources</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="<?= cms_escape(cms_localized_href('/downloads', $page['language']['code'])) ?>" class="hover:text-white">Downloads</a></li>
                        <li><a href="<?= cms_escape(cms_localized_href('/send-a-case', $page['language']['code'])) ?>" class="hover:text-white">Send A Case</a></li>
                        <li><a href="<?= cms_escape(cms_localized_href('/materials', $page['language']['code'])) ?>" class="hover:text-white">Materials</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><?= cms_escape($page['site_settings']['site_phone_display']) ?></li>
                        <li><?= cms_escape($page['site_settings']['site_email_display']) ?></li>
                        <li>Hong Kong &amp; Shenzhen</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; 2026 Global Dental Laboratory. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script data-cfasync="false" src="/js/header-hero.js?v=20260313-3"></script>
    <script data-cfasync="false">
        GlobalDentalLab.init(<?= cms_encode_json($page['hero']) ?>);
    </script>
</body>
</html>
