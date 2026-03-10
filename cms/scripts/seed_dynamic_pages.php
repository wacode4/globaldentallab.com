<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$pdo = cms_db();

$languageStmt = $pdo->prepare(
    'INSERT INTO languages (code, name, native_name, is_default, is_active, sort_order)
     VALUES (?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        native_name = VALUES(native_name),
        is_default = VALUES(is_default),
        is_active = VALUES(is_active),
        sort_order = VALUES(sort_order)'
);

$languages = [
    ['en', 'English', 'English', 1, 1, 10],
    ['fr', 'French', 'Français', 0, 1, 20],
    ['de', 'German', 'Deutsch', 0, 1, 30],
];

foreach ($languages as $language) {
    $languageStmt->execute($language);
}

$languageRows = cms_languages();
$languageIds = [];
foreach ($languageRows as $row) {
    $languageIds[$row['code']] = (int) $row['id'];
}

$pageStmt = $pdo->prepare(
    'INSERT INTO pages (slug, page_type, template_key, status, sort_order, show_in_nav)
     VALUES (?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        page_type = VALUES(page_type),
        template_key = VALUES(template_key),
        status = VALUES(status),
        sort_order = VALUES(sort_order),
        show_in_nav = VALUES(show_in_nav),
        updated_at = CURRENT_TIMESTAMP'
);

$pageTranslationStmt = $pdo->prepare(
    'INSERT INTO page_translations (page_id, language_id, page_name, nav_label, seo_title, seo_description)
     VALUES (?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        page_name = VALUES(page_name),
        nav_label = VALUES(nav_label),
        seo_title = VALUES(seo_title),
        seo_description = VALUES(seo_description)'
);

$moduleStmt = $pdo->prepare(
    'INSERT INTO modules (module_key, module_type, variant, status, settings_json)
     VALUES (?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        module_type = VALUES(module_type),
        variant = VALUES(variant),
        status = VALUES(status),
        settings_json = VALUES(settings_json),
        updated_at = CURRENT_TIMESTAMP'
);

$moduleTranslationStmt = $pdo->prepare(
    'INSERT INTO module_translations (module_id, language_id, title, kicker, subtitle, content_html, content_json)
     VALUES (?, ?, ?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        kicker = VALUES(kicker),
        subtitle = VALUES(subtitle),
        content_html = VALUES(content_html),
        content_json = VALUES(content_json)'
);

function cms_seed_page_translations(string $name, string $navLabel, string $seoTitle, string $seoDescription): array
{
    return [
        'en' => [$name, $navLabel, $seoTitle, $seoDescription],
        'fr' => [$name, $navLabel, $seoTitle, $seoDescription],
        'de' => [$name, $navLabel, $seoTitle, $seoDescription],
    ];
}

function cms_seed_product_bundle(string $slug, array $config): array
{
    $page = [
        'definition' => [$slug, 'product', 'marketing', 'published', (int) $config['sort_order'], 0],
        'translations' => cms_seed_page_translations(
            $config['name'],
            $config['name'],
            $config['seo_title'],
            $config['seo_description']
        ),
    ];

    $modules = [
        $slug . '-hero' => [
            'definition' => [$slug . '-hero', 'hero', 'primary', 'published', '{}'],
            'translations' => [
                'en' => [
                    $config['name'],
                    $config['hero_kicker'],
                    $config['hero_subtitle'],
                    '',
                    cms_encode_json([
                        'label' => $config['hero_label'],
                        'title_html' => $config['hero_title_html'],
                        'subtitle_html' => $config['hero_subtitle_html'],
                        'buttons' => [
                            ['text' => 'Ask About A Case', 'href' => '/en/contact', 'style' => 'primary'],
                            ['text' => 'Back To Ceramics', 'href' => '/en/ceramics', 'style' => 'secondary'],
                        ],
                    ]),
                ],
            ],
        ],
        $slug . '-overview' => [
            'definition' => [$slug . '-overview', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'left', 'section_class' => 'bg-white py-20'])],
            'translations' => [
                'en' => [
                    $config['overview_title'],
                    'Product Positioning',
                    $config['overview_subtitle'],
                    $config['overview_html'],
                    cms_encode_json([
                        'image' => $config['image'],
                        'image_alt' => $config['image_alt'],
                        'buttons' => [
                            ['text' => 'Compare All Ceramics', 'href' => '/en/ceramics', 'style' => 'primary'],
                        ],
                    ]),
                ],
            ],
        ],
        $slug . '-indications' => [
            'definition' => [$slug . '-indications', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 3, 'section_class' => 'bg-slate-50 py-20'])],
            'translations' => [
                'en' => [
                    'Typical Use Cases',
                    'Indications',
                    $config['indications_subtitle'],
                    '',
                    cms_encode_json(['items' => $config['indications_items']]),
                ],
            ],
        ],
        $slug . '-specs' => [
            'definition' => [$slug . '-specs', 'stats_grid', 'default', 'published', '{}'],
            'translations' => [
                'en' => [
                    'Decision Snapshot',
                    'Material Profile',
                    $config['stats_subtitle'],
                    '',
                    cms_encode_json(['items' => $config['stats_items']]),
                ],
            ],
        ],
        $slug . '-related' => [
            'definition' => [$slug . '-related', 'card_grid', 'default', 'published', '{}'],
            'translations' => [
                'en' => [
                    'Related Options',
                    'Compare Nearby Paths',
                    $config['related_subtitle'],
                    '',
                    cms_encode_json(['cards' => $config['related_cards']]),
                ],
            ],
        ],
        $slug . '-cta' => [
            'definition' => [$slug . '-cta', 'cta_banner', 'default', 'published', '{}'],
            'translations' => [
                'en' => [
                    $config['cta_title'],
                    $config['cta_kicker'],
                    $config['cta_subtitle'],
                    '',
                    cms_encode_json([
                        'buttons' => [
                            ['text' => 'Open Contact', 'href' => '/en/contact', 'style' => 'primary'],
                            ['text' => 'Back To Services', 'href' => '/en/services', 'style' => 'secondary'],
                        ],
                    ]),
                ],
            ],
        ],
    ];

    $assignments = [
        [$slug, $slug . '-hero', 'main', 10, 1],
        [$slug, $slug . '-overview', 'main', 20, 1],
        [$slug, $slug . '-indications', 'main', 30, 1],
        [$slug, $slug . '-specs', 'main', 40, 1],
        [$slug, $slug . '-related', 'main', 50, 1],
        [$slug, $slug . '-cta', 'main', 60, 1],
    ];

    return [
        'page' => $page,
        'modules' => $modules,
        'assignments' => $assignments,
    ];
}

$pages = [
    'home' => [
        'definition' => ['home', 'home', 'marketing', 'published', 10, 1],
        'translations' => [
            'en' => ['Home', 'Home', 'Global Dental Lab Dynamic Homepage', 'Dynamic multilingual homepage rendered from PHP modules.'],
            'fr' => ['Accueil', 'Accueil', 'Global Dental Lab Dynamic Homepage', 'Dynamic multilingual homepage rendered from PHP modules.'],
            'de' => ['Startseite', 'Start', 'Global Dental Lab Dynamic Homepage', 'Dynamic multilingual homepage rendered from PHP modules.'],
        ],
    ],
    'contact' => [
        'definition' => ['contact', 'contact', 'marketing', 'published', 20, 1],
        'translations' => [
            'en' => ['Contact', 'Contact', 'Contact Global Dental Lab', 'Contact page rendered from reusable CMS modules.'],
            'fr' => ['Contact', 'Contact', 'Contact Global Dental Lab', 'Contact page rendered from reusable CMS modules.'],
            'de' => ['Kontakt', 'Kontakt', 'Contact Global Dental Lab', 'Contact page rendered from reusable CMS modules.'],
        ],
    ],
    'about' => [
        'definition' => ['about', 'page', 'marketing', 'published', 30, 1],
        'translations' => [
            'en' => ['About', 'About', 'About Global Dental Lab', 'Brand story, production model, and collaboration standards for Global Dental Lab.'],
            'fr' => ['About', 'About', 'About Global Dental Lab', 'Brand story, production model, and collaboration standards for Global Dental Lab.'],
            'de' => ['About', 'About', 'About Global Dental Lab', 'Brand story, production model, and collaboration standards for Global Dental Lab.'],
        ],
    ],
    'technology' => [
        'definition' => ['technology', 'page', 'marketing', 'published', 40, 1],
        'translations' => [
            'en' => ['Technology', 'Technology', 'Dental Lab Technology Workflow', 'CAD/CAM, quality checkpoints, and lab workflow systems behind Global Dental Lab cases.'],
            'fr' => ['Technology', 'Technology', 'Dental Lab Technology Workflow', 'CAD/CAM, quality checkpoints, and lab workflow systems behind Global Dental Lab cases.'],
            'de' => ['Technology', 'Technology', 'Dental Lab Technology Workflow', 'CAD/CAM, quality checkpoints, and lab workflow systems behind Global Dental Lab cases.'],
        ],
    ],
    'services' => [
        'definition' => ['services', 'collection', 'marketing', 'published', 50, 1],
        'translations' => [
            'en' => ['Services', 'Services', 'Dental Lab Products And Services', 'Overview of restorative, implant, orthodontic, and removable workflows at Global Dental Lab.'],
            'fr' => ['Services', 'Services', 'Dental Lab Products And Services', 'Overview of restorative, implant, orthodontic, and removable workflows at Global Dental Lab.'],
            'de' => ['Services', 'Services', 'Dental Lab Products And Services', 'Overview of restorative, implant, orthodontic, and removable workflows at Global Dental Lab.'],
        ],
    ],
    'ceramics' => [
        'definition' => ['ceramics', 'category', 'marketing', 'published', 60, 0],
        'translations' => [
            'en' => ['Ceramics', 'Ceramics', 'Ceramic Restorations', 'Ceramic product category page covering zirconia, e.max, layered, and monolithic workflows.'],
            'fr' => ['Ceramics', 'Ceramics', 'Ceramic Restorations', 'Ceramic product category page covering zirconia, e.max, layered, and monolithic workflows.'],
            'de' => ['Ceramics', 'Ceramics', 'Ceramic Restorations', 'Ceramic product category page covering zirconia, e.max, layered, and monolithic workflows.'],
        ],
    ],
    'zirconia-ultra' => [
        'definition' => ['zirconia-ultra', 'product', 'marketing', 'published', 70, 0],
        'translations' => [
            'en' => ['Zirconia Ultra', 'Zirconia Ultra', 'Zirconia Ultra Restorations', 'Product detail page for high-translucency zirconia cases and indications.'],
            'fr' => ['Zirconia Ultra', 'Zirconia Ultra', 'Zirconia Ultra Restorations', 'Product detail page for high-translucency zirconia cases and indications.'],
            'de' => ['Zirconia Ultra', 'Zirconia Ultra', 'Zirconia Ultra Restorations', 'Product detail page for high-translucency zirconia cases and indications.'],
        ],
    ],
];

$productBundles = [
    'emax' => cms_seed_product_bundle('emax', [
        'name' => 'IPS e.max',
        'sort_order' => 80,
        'seo_title' => 'IPS e.max Lithium Disilicate',
        'seo_description' => 'Lithium disilicate restorations for high-esthetic cases, veneer workflows, and adhesive restorative planning.',
        'hero_label' => 'Lithium Disilicate',
        'hero_kicker' => 'Product Detail Template',
        'hero_subtitle' => 'A reusable product page model for adhesive ceramic workflows and high-esthetic planning.',
        'hero_title_html' => 'IPS e.max<br>For Esthetic Cases With Detail Demand',
        'hero_subtitle_html' => 'This dynamic template positions e.max around esthetics, conservative prep compatibility, and the communication discipline needed for refined anterior work.',
        'overview_title' => 'Where This Product Fits Best',
        'overview_subtitle' => 'A ceramic option centered on esthetics, adhesive protocols, and smile-zone refinement.',
        'overview_html' => '<p>IPS e.max is typically used when translucency, incisal character, and conservative restorative planning are part of the treatment objective.</p><p>It is a strong fit for anterior work, veneers, inlays, and selected single-unit situations where the esthetic requirement is high and the indication is appropriate.</p>',
        'image' => '/images/content/emax.jpg',
        'image_alt' => 'IPS e.max restorative example',
        'indications_subtitle' => 'This template gives every product page a consistent structure for fit, indications, and next-step planning.',
        'indications_items' => [
            ['title' => 'Anterior Esthetic Cases', 'text' => 'Useful when translucency, texture, and refined smile-zone control are primary concerns.'],
            ['title' => 'Veneers And Conservative Restorations', 'text' => 'Well suited to adhesive workflows where preserving preparation strategy matters.'],
            ['title' => 'Doctors Prioritizing Shade Integration', 'text' => 'A good route when photos, stump shade, and detailed esthetic communication are part of the case workflow.'],
        ],
        'stats_subtitle' => 'A standardized comparison block keeps product pages easier to scan and compare.',
        'stats_items' => [
            ['value' => 'High', 'label' => 'Esthetic Range', 'description' => 'Positioned for cases where translucency and polish matter.'],
            ['value' => 'Adhesive', 'label' => 'Workflow', 'description' => 'Often aligned with bonding-driven restorative planning.'],
            ['value' => 'Refined', 'label' => 'Case Type', 'description' => 'Best for cases that justify a more detailed esthetic process.'],
        ],
        'related_subtitle' => 'Doctors should be able to move laterally into adjacent ceramic options without leaving the template system.',
        'related_cards' => [
            ['title' => 'Ceramic Veneers', 'text' => 'A more smile-design-focused route when facial coverage and esthetic control are central.', 'href' => '/en/veneers', 'cta' => 'Open Product', 'image' => '/images/content/veneers.jpg'],
            ['title' => 'Inlays And Onlays', 'text' => 'A more conservative indirect restoration path for selected posterior and partial-coverage cases.', 'href' => '/en/inlays-onlays', 'cta' => 'Open Product', 'image' => '/images/content/emax.jpg'],
            ['title' => 'Zirconia Ultra', 'text' => 'Compare against zirconia when strength requirements start to outweigh purely esthetic priorities.', 'href' => '/en/zirconia-ultra', 'cta' => 'Compare Product', 'image' => '/images/content/zirconia.jpg'],
            ['title' => 'Ceramics Category', 'text' => 'Return to the full ceramic category to compare workflow directions.', 'href' => '/en/ceramics', 'cta' => 'Open Category', 'image' => '/images/content/dental-lab-2.jpg'],
        ],
        'cta_title' => 'Move To Case Review',
        'cta_kicker' => 'Need Help Confirming If e.max Is The Right Choice?',
        'cta_subtitle' => 'Use the contact route when you want to validate indication, prep design, or shade communication requirements before production starts.',
    ]),
    'layered-zirconia' => cms_seed_product_bundle('layered-zirconia', [
        'name' => 'Layered Zirconia',
        'sort_order' => 90,
        'seo_title' => 'Layered Zirconia',
        'seo_description' => 'Layered zirconia restorations for smile-zone cases where zirconia strength and layered esthetics need to work together.',
        'hero_label' => 'Esthetic Zirconia',
        'hero_kicker' => 'Product Detail Template',
        'hero_subtitle' => 'A product page model for cases where zirconia strength and layering detail need to coexist.',
        'hero_title_html' => 'Layered Zirconia<br>For More Expressive Esthetic Work',
        'hero_subtitle_html' => 'Layered zirconia fits cases that ask for more surface character and esthetic nuance than a simpler monolithic route usually provides.',
        'overview_title' => 'Where This Product Fits Best',
        'overview_subtitle' => 'A zirconia-based option for visible-zone cases where esthetic depth matters as much as material confidence.',
        'overview_html' => '<p>Layered zirconia is most useful when the doctor wants the structural benefits of zirconia but also expects more detailed esthetic layering for smile-zone presentation.</p><p>It performs best when photos, provisional references, and communication around facial anatomy are shared early in the case flow.</p>',
        'image' => '/images/content/dental-lab-3.jpg',
        'image_alt' => 'Layered zirconia example',
        'indications_subtitle' => 'The goal is to make “when should I use this?” obvious before the clinic reaches out for case review.',
        'indications_items' => [
            ['title' => 'Anterior Or Premolar Visibility', 'text' => 'Useful when the restoration sits in a zone where esthetic layering is likely to be noticed.'],
            ['title' => 'Cases With Photo And Provisional References', 'text' => 'Best when the clinic can share richer esthetic reference material at intake.'],
            ['title' => 'Doctors Wanting More Character Than Monolithic', 'text' => 'A middle path when basic zirconia efficiency is not enough for the visual result required.'],
        ],
        'stats_subtitle' => 'This summary block standardizes how we compare ceramic products inside the CMS.',
        'stats_items' => [
            ['value' => 'High', 'label' => 'Esthetic Detail', 'description' => 'Supports more expressive characterization than basic monolithic workflows.'],
            ['value' => 'Zirconia', 'label' => 'Material Base', 'description' => 'Retains zirconia as the underlying restorative platform.'],
            ['value' => 'Planned', 'label' => 'Communication Need', 'description' => 'Benefits from stronger pre-production alignment on esthetic expectations.'],
        ],
        'related_subtitle' => 'Related product links help this template behave like a real catalog instead of a dead-end detail page.',
        'related_cards' => [
            ['title' => 'Zirconia Ultra', 'text' => 'Compare against a more translucent zirconia route when the esthetic goal is similar.', 'href' => '/en/zirconia-ultra', 'cta' => 'Compare Product', 'image' => '/images/content/zirconia.jpg'],
            ['title' => 'Monolithic Zirconia', 'text' => 'Shift to a more durability-driven route when simplicity and strength matter more than layered esthetics.', 'href' => '/en/monolithic-zirconia', 'cta' => 'Open Product', 'image' => '/images/content/dental-lab-2.jpg'],
            ['title' => 'IPS e.max', 'text' => 'Review lithium disilicate when the case leans further toward adhesive esthetic planning.', 'href' => '/en/emax', 'cta' => 'Open Product', 'image' => '/images/content/emax.jpg'],
            ['title' => 'Ceramics Category', 'text' => 'Return to the full ceramic category for broader comparison.', 'href' => '/en/ceramics', 'cta' => 'Open Category', 'image' => '/images/content/veneers.jpg'],
        ],
        'cta_title' => 'Need Help Aligning Esthetic Expectations?',
        'cta_kicker' => 'Use The Lab Before You Commit The Material Path',
        'cta_subtitle' => 'If the final result depends heavily on anatomy, layering character, or smile-zone presentation, use the contact route for case planning support.',
    ]),
    'monolithic-zirconia' => cms_seed_product_bundle('monolithic-zirconia', [
        'name' => 'Monolithic Zirconia',
        'sort_order' => 100,
        'seo_title' => 'Monolithic Zirconia',
        'seo_description' => 'Monolithic zirconia restorations for strength-driven workflows, posterior durability, and high-volume case consistency.',
        'hero_label' => 'Durability-Focused Zirconia',
        'hero_kicker' => 'Product Detail Template',
        'hero_subtitle' => 'A product template centered on strength, consistency, and streamlined zirconia production.',
        'hero_title_html' => 'Monolithic Zirconia<br>For Strength And Daily Predictability',
        'hero_subtitle_html' => 'This route is built for cases where durability, production efficiency, and routine consistency are stronger priorities than layered esthetic nuance.',
        'overview_title' => 'Where This Product Fits Best',
        'overview_subtitle' => 'A zirconia route for load-bearing and routine restorative workflows where dependable strength leads the decision.',
        'overview_html' => '<p>Monolithic zirconia is often chosen for posterior cases, routine crown-and-bridge workflows, and situations where wear resistance and practical predictability are central.</p><p>It is especially useful when clinics want a durable zirconia-centered workflow that scales cleanly across regular case volume.</p>',
        'image' => '/images/content/dental-lab-2.jpg',
        'image_alt' => 'Monolithic zirconia workflow',
        'indications_subtitle' => 'The product page should make it clear when monolithic zirconia is the practical answer, not just a material option.',
        'indications_items' => [
            ['title' => 'Posterior Strength Cases', 'text' => 'A strong fit when load, contact stability, and durability shape the material decision.'],
            ['title' => 'Routine High-Volume Restorative Flow', 'text' => 'Useful for clinics that want a repeatable zirconia workflow across daily case volume.'],
            ['title' => 'Doctors Prioritizing Operational Simplicity', 'text' => 'A practical route when straightforward production and reliability outweigh esthetic layering needs.'],
        ],
        'stats_subtitle' => 'This block keeps product comparison consistent while the catalog expands.',
        'stats_items' => [
            ['value' => 'Strong', 'label' => 'Durability', 'description' => 'Positioned for strength-led restorative planning.'],
            ['value' => 'Routine', 'label' => 'Workflow Fit', 'description' => 'Works well in repeatable day-to-day zirconia production.'],
            ['value' => 'Practical', 'label' => 'Case Focus', 'description' => 'Optimized for clinical reliability rather than maximal esthetic expression.'],
        ],
        'related_subtitle' => 'Related routes help clinics compare zirconia options without leaving the product family.',
        'related_cards' => [
            ['title' => 'Layered Zirconia', 'text' => 'Move toward a more esthetic layered route when visual detail matters more.', 'href' => '/en/layered-zirconia', 'cta' => 'Compare Product', 'image' => '/images/content/dental-lab-3.jpg'],
            ['title' => 'Zirconia Ultra', 'text' => 'Compare against a more translucent zirconia path for visible-zone work.', 'href' => '/en/zirconia-ultra', 'cta' => 'Compare Product', 'image' => '/images/content/zirconia.jpg'],
            ['title' => 'Ceramics Category', 'text' => 'Return to the category overview to compare the full ceramic set.', 'href' => '/en/ceramics', 'cta' => 'Open Category', 'image' => '/images/content/veneers.jpg'],
            ['title' => 'Contact The Lab', 'text' => 'Ask the lab to confirm whether the case should stay in a monolithic path.', 'href' => '/en/contact', 'cta' => 'Ask The Lab', 'image' => '/images/source-lab-tour/lab-staff.jpg'],
        ],
        'cta_title' => 'Want Help Confirming A Strength-Driven Workflow?',
        'cta_kicker' => 'Use Intake To Check Indication Before Production',
        'cta_subtitle' => 'If clearance, occlusion, load, or case longevity is driving the discussion, use the contact route so the lab can review the material choice with you.',
    ]),
    'veneers' => cms_seed_product_bundle('veneers', [
        'name' => 'Ceramic Veneers',
        'sort_order' => 110,
        'seo_title' => 'Ceramic Veneers',
        'seo_description' => 'Ceramic veneer restorations for smile design, anterior esthetics, and refined shade communication workflows.',
        'hero_label' => 'Smile-Design Ceramics',
        'hero_kicker' => 'Product Detail Template',
        'hero_subtitle' => 'A product template for veneer cases that depend on esthetic communication as much as material choice.',
        'hero_title_html' => 'Ceramic Veneers<br>For Smile-Zone Esthetic Control',
        'hero_subtitle_html' => 'This page positions veneer workflows around smile design, facial coverage planning, and the higher communication standard that refined anterior cases require.',
        'overview_title' => 'Where This Product Fits Best',
        'overview_subtitle' => 'A veneer-focused ceramic route for highly esthetic anterior planning and conservative facial enhancement workflows.',
        'overview_html' => '<p>Ceramic veneers are best suited to cases where smile-line visibility, surface character, and detailed shade communication are central to the treatment goal.</p><p>They work especially well when the case process includes photography, mock-up feedback, and a clearly defined esthetic brief.</p>',
        'image' => '/images/content/veneers.jpg',
        'image_alt' => 'Ceramic veneers example',
        'indications_subtitle' => 'This product page should make esthetic-first case selection easier before a clinic reaches out.',
        'indications_items' => [
            ['title' => 'Smile Design Cases', 'text' => 'Best for treatments where anterior esthetics and facial presentation are the primary driver.'],
            ['title' => 'Conservative Facial Coverage', 'text' => 'A strong option when the treatment plan aims to preserve a more conservative restorative approach.'],
            ['title' => 'High-Communication Esthetic Work', 'text' => 'Fits clinics prepared to share photos, mock-up guidance, and detailed esthetic references.'],
        ],
        'stats_subtitle' => 'A standard summary block helps veneer pages stay comparable to the rest of the catalog.',
        'stats_items' => [
            ['value' => 'High', 'label' => 'Smile-Zone Focus', 'description' => 'Designed for visible esthetic zones and facial presentation.'],
            ['value' => 'Detailed', 'label' => 'Communication Need', 'description' => 'Works best when shade and design notes are explicit.'],
            ['value' => 'Refined', 'label' => 'Workflow Style', 'description' => 'Geared toward more deliberate esthetic planning, not commodity case flow.'],
        ],
        'related_subtitle' => 'This keeps veneer pages connected to adjacent esthetic options inside the same CMS architecture.',
        'related_cards' => [
            ['title' => 'IPS e.max', 'text' => 'Review e.max when the case needs a broader lithium disilicate restorative route.', 'href' => '/en/emax', 'cta' => 'Open Product', 'image' => '/images/content/emax.jpg'],
            ['title' => 'Inlays And Onlays', 'text' => 'Compare against partial-coverage indirect restorations for more conservative posterior situations.', 'href' => '/en/inlays-onlays', 'cta' => 'Open Product', 'image' => '/images/content/emax.jpg'],
            ['title' => 'Ceramics Category', 'text' => 'Return to the category overview to compare all ceramic directions.', 'href' => '/en/ceramics', 'cta' => 'Open Category', 'image' => '/images/content/zirconia.jpg'],
            ['title' => 'Contact The Lab', 'text' => 'Use intake if the veneer case needs help on esthetic planning or submission prep.', 'href' => '/en/contact', 'cta' => 'Ask The Lab', 'image' => '/images/source-lab-tour/lab-staff.jpg'],
        ],
        'cta_title' => 'Need Help With Shade Or Esthetic Planning?',
        'cta_kicker' => 'Bring The Lab In Before Veneer Production Starts',
        'cta_subtitle' => 'If the case outcome depends on mock-up alignment, photo review, or shade communication, use the contact page for pre-production support.',
    ]),
    'inlays-onlays' => cms_seed_product_bundle('inlays-onlays', [
        'name' => 'Ceramic Inlays & Onlays',
        'sort_order' => 120,
        'seo_title' => 'Ceramic Inlays And Onlays',
        'seo_description' => 'Ceramic inlay and onlay restorations for indirect partial-coverage planning and conservative ceramic workflows.',
        'hero_label' => 'Partial-Coverage Ceramics',
        'hero_kicker' => 'Product Detail Template',
        'hero_subtitle' => 'A reusable product page for conservative indirect restorations and adhesive ceramic planning.',
        'hero_title_html' => 'Ceramic Inlays &amp; Onlays<br>For Conservative Indirect Restorations',
        'hero_subtitle_html' => 'This template positions inlays and onlays around conservative preparation strategy, adhesive workflows, and practical case selection.',
        'overview_title' => 'Where This Product Fits Best',
        'overview_subtitle' => 'A ceramic route for indirect partial-coverage restorations where a full crown is not the intended solution.',
        'overview_html' => '<p>Inlays and onlays fit cases where the clinic wants a conservative indirect restoration while still benefiting from ceramic esthetics and durable lab fabrication.</p><p>They are especially relevant when preparation design, bonding strategy, and remaining tooth structure are part of the treatment logic.</p>',
        'image' => '/images/content/emax.jpg',
        'image_alt' => 'Ceramic inlay and onlay example',
        'indications_subtitle' => 'The template should clarify partial-coverage positioning without forcing doctors back into generic product lists.',
        'indications_items' => [
            ['title' => 'Conservative Indirect Restorations', 'text' => 'Useful when the treatment plan preserves more tooth structure than a full-coverage crown approach.'],
            ['title' => 'Adhesive Ceramic Planning', 'text' => 'Fits cases where bonding workflow and prep design are part of the material decision.'],
            ['title' => 'Doctors Avoiding Full-Crown Escalation', 'text' => 'Helpful when the clinical goal is a targeted restoration rather than a broader full-coverage route.'],
        ],
        'stats_subtitle' => 'A repeatable summary block helps partial-coverage products fit the same catalog structure.',
        'stats_items' => [
            ['value' => 'Conservative', 'label' => 'Prep Strategy', 'description' => 'Supports more tooth-preserving indirect restorative planning.'],
            ['value' => 'Adhesive', 'label' => 'Workflow', 'description' => 'Typically aligned with bonding and careful prep design.'],
            ['value' => 'Targeted', 'label' => 'Use Case', 'description' => 'Best for cases that do not warrant a full-coverage solution.'],
        ],
        'related_subtitle' => 'Related products keep partial-coverage cases connected to the broader ceramic decision tree.',
        'related_cards' => [
            ['title' => 'IPS e.max', 'text' => 'Review the broader lithium disilicate route for adjacent esthetic restorative cases.', 'href' => '/en/emax', 'cta' => 'Open Product', 'image' => '/images/content/emax.jpg'],
            ['title' => 'Ceramic Veneers', 'text' => 'Compare against a more facial-esthetic route for smile-zone treatment planning.', 'href' => '/en/veneers', 'cta' => 'Open Product', 'image' => '/images/content/veneers.jpg'],
            ['title' => 'Ceramics Category', 'text' => 'Return to the full ceramic family to compare workflow directions.', 'href' => '/en/ceramics', 'cta' => 'Open Category', 'image' => '/images/content/zirconia.jpg'],
            ['title' => 'Contact The Lab', 'text' => 'Use inquiry intake if the prep design or indication needs a second look before production.', 'href' => '/en/contact', 'cta' => 'Ask The Lab', 'image' => '/images/source-lab-tour/lab-staff.jpg'],
        ],
        'cta_title' => 'Want Help Confirming A Conservative Ceramic Route?',
        'cta_kicker' => 'Use The Lab To Pressure-Test The Plan',
        'cta_subtitle' => 'If the choice between inlay, onlay, full-coverage, or another ceramic path is still open, use the contact route and we will help evaluate the case.',
    ]),
];

foreach ($productBundles as $slug => $bundle) {
    $pages[$slug] = $bundle['page'];
}

$pageIds = [];
foreach ($pages as $slug => $page) {
    $pageStmt->execute($page['definition']);
    $pageId = (int) $pdo->query("SELECT id FROM pages WHERE slug = " . $pdo->quote($slug))->fetchColumn();
    $pageIds[$slug] = $pageId;

    foreach ($page['translations'] as $code => $translation) {
        $pageTranslationStmt->execute([
            $pageId,
            $languageIds[$code],
            $translation[0],
            $translation[1],
            $translation[2],
            $translation[3],
        ]);
    }
}

$modules = [
    'home-hero' => [
        'definition' => ['home-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Dynamic Homepage', 'Template-Driven Site Architecture', 'Reusable hero module with multilingual content and route-aware links.', '', json_encode([
                'label' => 'Multilingual Route Preview',
                'title_html' => 'A Modular PHP Site<br>Ready For Real Site Migration',
                'subtitle_html' => 'The new architecture separates content, templates, and page composition so category pages, product pages, and multi-language rollout can move into one system.',
                'buttons' => [
                    ['text' => 'Open Services', 'href' => '/en/services', 'style' => 'primary'],
                    ['text' => 'CMS Dashboard', 'href' => '/cms/dashboard.php', 'style' => 'secondary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'home-intro' => [
        'definition' => ['home-intro', 'rich_text', 'default', 'published', json_encode(['section_class' => 'py-20 bg-white'])],
        'translations' => [
            'en' => ['Content And Presentation Are Now Split', 'Architecture Goal', 'Pages will become compositions of reusable modules rather than one-off HTML files.', '<p>This first phase introduces a language-aware page model, module assignments, a template renderer, and a dynamic route pattern based on <code>/en/slug</code>.</p><p>From here, product pages, category pages, landing pages, and support pages can all move into the same system without fragmenting the codebase again.</p>', '{}'],
        ],
    ],
    'home-stats' => [
        'definition' => ['home-stats', 'stats_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Why This Refactor Matters', 'Operational Benefits', 'The architecture is designed to scale content operations, not just render one more page.', '', json_encode([
                'items' => [
                    ['value' => '1', 'label' => 'Page Model', 'description' => 'Pages, translations, and modules now live in separate tables.'],
                    ['value' => '3', 'label' => 'Languages Seeded', 'description' => 'English, French, and German are ready for translation expansion.'],
                    ['value' => '8', 'label' => 'Reusable Module Types', 'description' => 'Hero, rich text, stats, cards, feature lists, media splits, contact panels, and CTAs are now renderable.'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'home-cards' => [
        'definition' => ['home-cards', 'card_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Reusable Building Blocks', 'Module Library', 'Repeated sections are now components you can reuse, reorder, and translate independently.', '', json_encode([
                'cards' => [
                    ['title' => 'About Page', 'text' => 'Brand story and operating model rendered from reusable modules.', 'href' => '/en/about', 'cta' => 'Open Page', 'image' => '/images/source-lab-tour/lab-staff.jpg'],
                    ['title' => 'Technology Page', 'text' => 'Workflow and quality-control narrative managed without hard-coded page layouts.', 'href' => '/en/technology', 'cta' => 'Open Page', 'image' => '/images/content/digital-workflow.jpg'],
                    ['title' => 'Services Overview', 'text' => 'The product architecture now has a dynamic category-level landing page.', 'href' => '/en/services', 'cta' => 'Open Services', 'image' => '/images/content/dental-lab-2.jpg'],
                    ['title' => 'Ceramic Category', 'text' => 'Category and product detail templates can now live in the same page model.', 'href' => '/en/ceramics', 'cta' => 'Open Category', 'image' => '/images/content/zirconia.jpg'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'home-cta' => [
        'definition' => ['home-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Next Step', 'Second-Stage Migration Is Live', 'About, technology, services, category, and product pages can now be managed inside the same multilingual architecture.', '', json_encode([
                'buttons' => [
                    ['text' => 'Open Services', 'href' => '/en/services', 'style' => 'primary'],
                    ['text' => 'Edit Pages', 'href' => '/cms/pages.php', 'style' => 'secondary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'contact-hero' => [
        'definition' => ['contact-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Contact Route', 'Dynamic Contact Page', 'A contact and intake page rendered from modules so the same layout can be reused across languages.', '', json_encode([
                'label' => 'Reusable Contact Template',
                'title_html' => 'Contact The Lab<br>On The New Route',
                'subtitle_html' => 'This page demonstrates how a single template can support language-aware URLs, editable content, and server-side inquiry handling.',
                'buttons' => [
                    ['text' => 'Send Inquiry', 'href' => '#contact-form', 'style' => 'primary'],
                    ['text' => 'Back Home', 'href' => '/en/', 'style' => 'secondary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'contact-panel' => [
        'definition' => ['contact-panel', 'contact_panel', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Inquiry And Intake', 'Server-Side Form', 'Messages submitted here write directly into the MySQL-backed CMS instead of depending on Cloudflare functions.', '', '{}'],
        ],
    ],
    'contact-cta' => [
        'definition' => ['contact-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Editing Workflow', 'Everything Here Is CMS-Managed', 'After sign-in, page records, module records, and route composition are all editable from the content backend.', '', json_encode([
                'buttons' => [
                    ['text' => 'Edit Contact Page', 'href' => '/cms/pages.php', 'style' => 'primary'],
                    ['text' => 'View Inquiries', 'href' => '/cms/inquiries.php', 'style' => 'secondary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'about-hero' => [
        'definition' => ['about-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['About Global Dental Lab', 'Brand And Delivery Model', 'The operating model behind our communication, quality control, and production capacity.', '', cms_encode_json([
                'label' => 'Company Overview',
                'title_html' => 'A Lab Partner Built<br>For Consistency At Scale',
                'subtitle_html' => 'Global Dental Lab combines cross-border communication, digital workflow discipline, and production oversight so clinics can outsource without losing control.',
                'buttons' => [
                    ['text' => 'View Services', 'href' => '/en/services', 'style' => 'primary'],
                    ['text' => 'Contact The Lab', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'about-story' => [
        'definition' => ['about-story', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'right'])],
        'translations' => [
            'en' => ['Built Around Communication, Not Just Production', 'How We Work', 'A delivery model designed for dentists who need responsiveness as much as they need fit and finish.', '<p>We operate as a digital-first outsourcing lab with Hong Kong and Shenzhen coordination points. That structure helps us manage doctor communication, case planning, production handoff, and outbound logistics as one workflow instead of separate departments.</p><p>The goal is straightforward: reduce remakes, shorten clarification cycles, and make it easier for your clinic to submit work with confidence.</p>', cms_encode_json([
                'image' => '/images/source-lab-tour/lab-staff.jpg',
                'image_alt' => 'Global Dental Lab staff at work',
                'buttons' => [
                    ['text' => 'See Technology', 'href' => '/en/technology', 'style' => 'primary'],
                ],
            ])],
        ],
    ],
    'about-values' => [
        'definition' => ['about-values', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 3])],
        'translations' => [
            'en' => ['What Clinics Rely On', 'Operating Principles', 'The point of the system is not more complexity. It is more predictable delivery for every case type.', '', cms_encode_json([
                'items' => [
                    [
                        'eyebrow' => 'Communication',
                        'title' => 'Case Questions Resolved Early',
                        'text' => 'Shade, margin, occlusion, and implant compatibility questions are pushed forward in the workflow instead of surfacing at final delivery.',
                        'meta' => 'Pre-production alignment',
                    ],
                    [
                        'eyebrow' => 'Quality',
                        'title' => 'Digital Checks Before Shipment',
                        'text' => 'Every production stage is anchored by CAD review, model validation, and final inspection before a case leaves the lab.',
                        'meta' => 'Controlled release',
                    ],
                    [
                        'eyebrow' => 'Capacity',
                        'title' => 'Scalable Without Losing Oversight',
                        'text' => 'The team can support routine case flow and growth without forcing clinics into a fragmented service experience.',
                        'meta' => 'Operational consistency',
                    ],
                ],
            ])],
        ],
    ],
    'about-cta' => [
        'definition' => ['about-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Start The Relationship', 'Ready To Evaluate A Lab Partner?', 'If you want a clearer view of fit, turnaround, indications, or onboarding, use the contact route and we will map the next step with your team.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Open Contact', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'Browse Products', 'href' => '/en/services', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'technology-hero' => [
        'definition' => ['technology-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Technology Workflow', 'Digital Production System', 'A clearer view of the tools and checkpoints supporting case quality inside the lab.', '', cms_encode_json([
                'label' => 'CAD/CAM + QC',
                'title_html' => 'Technology That Supports<br>Repeatable Clinical Outcomes',
                'subtitle_html' => 'The value of the workflow is not the machine list by itself. It is the way scans, design review, production, and final inspection connect into one controlled process.',
                'buttons' => [
                    ['text' => 'See Ceramic Category', 'href' => '/en/ceramics', 'style' => 'primary'],
                    ['text' => 'Book A Review', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'technology-stack' => [
        'definition' => ['technology-stack', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 3, 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['Core Workflow Areas', 'Systems View', 'The technology stack is organized around how a case moves, not how a brochure lists equipment.', '', cms_encode_json([
                'items' => [
                    [
                        'eyebrow' => 'Input',
                        'title' => 'Digital Intake',
                        'text' => 'Intraoral scans, doctor instructions, bite records, and implant data are reviewed before design begins.',
                        'bullets' => ['Scan and order intake', 'Compatibility checks', 'Planning notes'],
                    ],
                    [
                        'eyebrow' => 'Production',
                        'title' => 'CAD/CAM Design Control',
                        'text' => 'Design approval, margin review, material selection, and manufacturing preparation are handled as one connected stage.',
                        'bullets' => ['Design review', 'Material choice', 'Manufacturing prep'],
                    ],
                    [
                        'eyebrow' => 'Release',
                        'title' => 'Quality Verification',
                        'text' => 'Final fit, surface finish, and case completeness are checked before packing and dispatch.',
                        'bullets' => ['Final inspection', 'Case completeness', 'Shipment release'],
                    ],
                ],
            ])],
        ],
    ],
    'technology-workflow' => [
        'definition' => ['technology-workflow', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'left', 'section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['From File To Delivery', 'Workflow Discipline', 'The lab workflow is built to reduce avoidable delays and improve handoff clarity between teams.', '<p>We structure the process around stage-specific decisions: intake validation, design review, manufacturing release, finishing, and shipment. That makes it easier to identify risk points early and avoid the common back-and-forth that slows turnaround.</p><p>This same operating pattern can support single crowns, larger restorative cases, implant work, and more technique-sensitive esthetic cases.</p>', cms_encode_json([
                'image' => '/images/content/digital-workflow.jpg',
                'image_alt' => 'Digital workflow in the dental lab',
                'buttons' => [
                    ['text' => 'Explore Services', 'href' => '/en/services', 'style' => 'primary'],
                ],
            ])],
        ],
    ],
    'technology-cta' => [
        'definition' => ['technology-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Apply The Workflow', 'Need Help Matching The Right Workflow To A Case?', 'Use the contact page if you want guidance on material selection, case indications, or production planning before you submit.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Open Contact', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'Go To Ceramics', 'href' => '/en/ceramics', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'services-hero' => [
        'definition' => ['services-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Products And Services', 'Clinical Workflow Coverage', 'An overview of the major case categories we support, organized for fast clinical scanning.', '', cms_encode_json([
                'label' => 'Product Overview',
                'title_html' => 'A Product Mix Built<br>For Daily Case Flow',
                'subtitle_html' => 'The goal of this page is to help clinics identify the right workflow quickly and then move into planning or submission without unnecessary friction.',
                'buttons' => [
                    ['text' => 'View Ceramics', 'href' => '/en/ceramics', 'style' => 'primary'],
                    ['text' => 'Talk To The Lab', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'services-grid' => [
        'definition' => ['services-grid', 'card_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Main Service Areas', 'Category Snapshot', 'These categories are the operating groups we can build into reusable page families as the CMS expands.', '', cms_encode_json([
                'cards' => [
                    ['title' => 'Ceramics', 'text' => 'Zirconia, e.max, layered, monolithic, veneers, and inlays for restorative workflows.', 'href' => '/en/ceramics', 'cta' => 'Open Category', 'image' => '/images/content/zirconia.jpg'],
                    ['title' => 'Implant Restorations', 'text' => 'Crown, bridge, and full-arch workflows with compatibility review and planning support.', 'href' => '/en/contact', 'cta' => 'Discuss Cases', 'image' => '/images/content/implants.jpg'],
                    ['title' => 'Orthodontics', 'text' => 'Retainers, aligner support, and appliance production for digital ortho case flow.', 'href' => '/en/contact', 'cta' => 'Ask About Turnaround', 'image' => '/images/content/orthodontics.jpg'],
                    ['title' => 'Removables', 'text' => 'Case planning and production support for removable prosthetic workflows.', 'href' => '/en/contact', 'cta' => 'Open Contact', 'image' => '/images/content/dental-lab-2.jpg'],
                ],
            ])],
        ],
    ],
    'services-why' => [
        'definition' => ['services-why', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 3, 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['Why The Structure Matters', 'Selection Guidance', 'This page is not just a list. It is the top-level decision layer for the product architecture.', '', cms_encode_json([
                'items' => [
                    [
                        'title' => 'Fast Category Identification',
                        'text' => 'Doctors can quickly find the main restorative path before discussing details with the lab.',
                        'meta' => 'Less front-end friction',
                    ],
                    [
                        'title' => 'Better Internal Navigation',
                        'text' => 'Category pages can now route into product detail pages without duplicating layout code.',
                        'meta' => 'Template-driven growth',
                    ],
                    [
                        'title' => 'Cleaner Translation Workflow',
                        'text' => 'As new languages are added, category structure stays shared and only content changes.',
                        'meta' => 'Multilingual by design',
                    ],
                ],
            ])],
        ],
    ],
    'services-cta' => [
        'definition' => ['services-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Move From Overview To Detail', 'Need Help Choosing The Right Service Path?', 'Use the contact page when you want to confirm indications, turnaround expectations, or compatibility before placing a case.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Contact The Lab', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'Open Ceramics', 'href' => '/en/ceramics', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'ceramics-hero' => [
        'definition' => ['ceramics-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Ceramic Restorations', 'Category Landing Page', 'A reusable category template for ceramic products, case indications, and next-step routing.', '', cms_encode_json([
                'label' => 'Ceramics Category',
                'title_html' => 'Ceramic Options<br>Organized For Faster Decisions',
                'subtitle_html' => 'This category page groups key ceramic workflows so doctors can move from broad selection into product-level detail without guessing where to start.',
                'buttons' => [
                    ['text' => 'View Zirconia Ultra', 'href' => '/en/zirconia-ultra', 'style' => 'primary'],
                    ['text' => 'Ask A Question', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'ceramics-intro' => [
        'definition' => ['ceramics-intro', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'right'])],
        'translations' => [
            'en' => ['Where This Category Fits', 'Restorative Focus', 'Ceramics remain the core of many restorative case flows because they balance esthetics, strength, and predictable workflows.', '<p>This category is structured to help doctors and coordinators navigate zirconia, layered restorations, e.max, veneers, inlays, and monolithic options from one place.</p><p>It also gives us a reusable template for any future category page, so implants, removables, and orthodontics can follow the same architecture.</p>', cms_encode_json([
                'image' => '/images/content/zirconia.jpg',
                'image_alt' => 'Ceramic restorations overview',
            ])],
        ],
    ],
    'ceramics-grid' => [
        'definition' => ['ceramics-grid', 'card_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Ceramic Product Paths', 'Product Family', 'Each product route can now share the same data model while keeping its own content and positioning.', '', cms_encode_json([
                'cards' => [
                    ['title' => 'Zirconia Ultra', 'text' => 'High-translucency zirconia for esthetic zones where strength still matters.', 'href' => '/en/zirconia-ultra', 'cta' => 'Open Product', 'image' => '/images/content/zirconia.jpg'],
                    ['title' => 'IPS e.max', 'text' => 'Lithium disilicate workflows for high esthetic demand and adhesive cases.', 'href' => '/en/emax', 'cta' => 'Open Product', 'image' => '/images/content/emax.jpg'],
                    ['title' => 'Layered Zirconia', 'text' => 'An esthetic option when layering detail is part of the restorative goal.', 'href' => '/en/layered-zirconia', 'cta' => 'Open Product', 'image' => '/images/content/dental-lab-3.jpg'],
                    ['title' => 'Monolithic Zirconia', 'text' => 'Durability-focused cases where streamlined manufacturing and strength are priorities.', 'href' => '/en/monolithic-zirconia', 'cta' => 'Open Product', 'image' => '/images/content/dental-lab-2.jpg'],
                    ['title' => 'Ceramic Veneers', 'text' => 'Smile-zone ceramic workflows for refined esthetic control and facial coverage planning.', 'href' => '/en/veneers', 'cta' => 'Open Product', 'image' => '/images/content/veneers.jpg'],
                    ['title' => 'Inlays & Onlays', 'text' => 'Partial-coverage ceramic restorations for conservative indirect treatment planning.', 'href' => '/en/inlays-onlays', 'cta' => 'Open Product', 'image' => '/images/content/emax.jpg'],
                ],
            ])],
        ],
    ],
    'ceramics-guide' => [
        'definition' => ['ceramics-guide', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 3, 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['How To Use This Category', 'Decision Guide', 'The category page should help a clinic narrow the field before discussing specific case variables with the lab.', '', cms_encode_json([
                'items' => [
                    [
                        'title' => 'Start With Indication',
                        'text' => 'Anterior esthetics, posterior load, prep design, and cementation requirements should narrow the shortlist first.',
                    ],
                    [
                        'title' => 'Then Compare Workflow',
                        'text' => 'Material strength, translucency, finishing steps, and turnaround needs help determine the final path.',
                    ],
                    [
                        'title' => 'Escalate To Case Review',
                        'text' => 'If the choice is not obvious, the category page should route the doctor into planning support instead of forcing self-selection.',
                    ],
                ],
            ])],
        ],
    ],
    'ceramics-cta' => [
        'definition' => ['ceramics-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Need A Recommendation?', 'Use The Lab As A Decision Partner', 'If you want help matching a case to the right ceramic workflow, send the case context through the contact route and we will guide the next step.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Open Contact', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'View Zirconia Ultra', 'href' => '/en/zirconia-ultra', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'zirconia-hero' => [
        'definition' => ['zirconia-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Zirconia Ultra', 'Product Detail Template', 'A product page model that can be reused across the ceramic catalog as more items move into CMS.', '', cms_encode_json([
                'label' => 'High-Translucency Zirconia',
                'title_html' => 'Zirconia Ultra<br>For Strength With Better Esthetics',
                'subtitle_html' => 'This product template is designed to make indications, positioning, and related options easier to compare without maintaining separate hard-coded pages.',
                'buttons' => [
                    ['text' => 'Ask About A Case', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'Back To Ceramics', 'href' => '/en/ceramics', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'zirconia-overview' => [
        'definition' => ['zirconia-overview', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'left', 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['Where It Fits Best', 'Product Positioning', 'A material option for cases that still need strength but cannot ignore esthetic expectations.', '<p>Zirconia Ultra is suited to clinics that want a more esthetic zirconia option for visible zones while preserving the operational benefits of zirconia-based production.</p><p>It is not a universal answer. It fits best when strength, polishability, and translucency need to be balanced rather than optimized in isolation.</p>', cms_encode_json([
                'image' => '/images/content/zirconia.jpg',
                'image_alt' => 'Zirconia restoration close-up',
                'buttons' => [
                    ['text' => 'Compare Ceramic Category', 'href' => '/en/ceramics', 'style' => 'primary'],
                ],
            ])],
        ],
    ],
    'zirconia-indications' => [
        'definition' => ['zirconia-indications', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 3, 'section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['Typical Use Cases', 'Indications', 'This section is the reusable decision layer that can appear on every product page in the catalog.', '', cms_encode_json([
                'items' => [
                    [
                        'title' => 'Anterior Cases With Strength Concern',
                        'text' => 'Useful when esthetics matter but the case still benefits from zirconia-level confidence.',
                    ],
                    [
                        'title' => 'Posterior Cases Needing Aesthetic Upgrade',
                        'text' => 'A stronger visual result than basic posterior-only positioning, without shifting into a purely veneer-driven workflow.',
                    ],
                    [
                        'title' => 'Doctors Standardizing Around Zirconia',
                        'text' => 'Helpful for clinics that prefer a zirconia-centered restorative workflow across a wider range of cases.',
                    ],
                ],
            ])],
        ],
    ],
    'zirconia-specs' => [
        'definition' => ['zirconia-specs', 'stats_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Decision Snapshot', 'Material Profile', 'A fast comparison block that can be repeated across products to standardize how detail is presented.', '', cms_encode_json([
                'items' => [
                    ['value' => 'High', 'label' => 'Translucency', 'description' => 'Positioned for better esthetics than conventional zirconia workflows.'],
                    ['value' => 'Strong', 'label' => 'Strength', 'description' => 'Designed to retain the structural confidence associated with zirconia.'],
                    ['value' => 'Broad', 'label' => 'Use Range', 'description' => 'Applicable across visible-zone restorative planning when the balance is appropriate.'],
                ],
            ])],
        ],
    ],
    'zirconia-related' => [
        'definition' => ['zirconia-related', 'card_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Related Options', 'Compare Nearby Paths', 'Product detail pages should also help doctors move sideways into adjacent options instead of dead-ending.', '', cms_encode_json([
                'cards' => [
                    ['title' => 'Ceramics Category', 'text' => 'Return to the broader ceramic family to compare workflow directions.', 'href' => '/en/ceramics', 'cta' => 'Open Category', 'image' => '/images/content/dental-lab-2.jpg'],
                    ['title' => 'IPS e.max', 'text' => 'Compare against a more esthetic lithium disilicate route when adhesion and translucency matter more.', 'href' => '/en/emax', 'cta' => 'Open Product', 'image' => '/images/content/emax.jpg'],
                    ['title' => 'Layered Zirconia', 'text' => 'Review the layered zirconia path when esthetic characterization needs more emphasis.', 'href' => '/en/layered-zirconia', 'cta' => 'Open Product', 'image' => '/images/content/dental-lab-3.jpg'],
                    ['title' => 'Contact The Lab', 'text' => 'Use inquiry intake when the case needs a recommendation instead of page browsing.', 'href' => '/en/contact', 'cta' => 'Ask The Lab', 'image' => '/images/source-lab-tour/lab-staff.jpg'],
                ],
            ])],
        ],
    ],
    'zirconia-cta' => [
        'definition' => ['zirconia-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Move To Case Planning', 'Need A Recommendation On Material Selection?', 'Send the case context, scan, or question through the contact route and the lab can help confirm whether Zirconia Ultra is the right choice.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Open Contact', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'Back To Services', 'href' => '/en/services', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
];

foreach ($productBundles as $bundle) {
    foreach ($bundle['modules'] as $key => $module) {
        $modules[$key] = $module;
    }
}

$moduleIds = [];
foreach ($modules as $key => $module) {
    $moduleStmt->execute($module['definition']);
    $moduleId = (int) $pdo->query("SELECT id FROM modules WHERE module_key = " . $pdo->quote($key))->fetchColumn();
    $moduleIds[$key] = $moduleId;

    foreach ($module['translations'] as $code => $translation) {
        $moduleTranslationStmt->execute([
            $moduleId,
            $languageIds[$code],
            $translation[0],
            $translation[1],
            $translation[2],
            $translation[3],
            $translation[4],
        ]);
    }
}

$pdo->exec('DELETE FROM page_modules');

$pageModules = [
    ['home', 'home-hero', 'main', 10, 1],
    ['home', 'home-intro', 'main', 20, 1],
    ['home', 'home-stats', 'main', 30, 1],
    ['home', 'home-cards', 'main', 40, 1],
    ['home', 'home-cta', 'main', 50, 1],
    ['contact', 'contact-hero', 'main', 10, 1],
    ['contact', 'contact-panel', 'main', 20, 1],
    ['contact', 'contact-cta', 'main', 30, 1],
    ['about', 'about-hero', 'main', 10, 1],
    ['about', 'about-story', 'main', 20, 1],
    ['about', 'about-values', 'main', 30, 1],
    ['about', 'about-cta', 'main', 40, 1],
    ['technology', 'technology-hero', 'main', 10, 1],
    ['technology', 'technology-stack', 'main', 20, 1],
    ['technology', 'technology-workflow', 'main', 30, 1],
    ['technology', 'technology-cta', 'main', 40, 1],
    ['services', 'services-hero', 'main', 10, 1],
    ['services', 'services-grid', 'main', 20, 1],
    ['services', 'services-why', 'main', 30, 1],
    ['services', 'services-cta', 'main', 40, 1],
    ['ceramics', 'ceramics-hero', 'main', 10, 1],
    ['ceramics', 'ceramics-intro', 'main', 20, 1],
    ['ceramics', 'ceramics-grid', 'main', 30, 1],
    ['ceramics', 'ceramics-guide', 'main', 40, 1],
    ['ceramics', 'ceramics-cta', 'main', 50, 1],
    ['zirconia-ultra', 'zirconia-hero', 'main', 10, 1],
    ['zirconia-ultra', 'zirconia-overview', 'main', 20, 1],
    ['zirconia-ultra', 'zirconia-indications', 'main', 30, 1],
    ['zirconia-ultra', 'zirconia-specs', 'main', 40, 1],
    ['zirconia-ultra', 'zirconia-related', 'main', 50, 1],
    ['zirconia-ultra', 'zirconia-cta', 'main', 60, 1],
];

foreach ($productBundles as $bundle) {
    foreach ($bundle['assignments'] as $assignment) {
        $pageModules[] = $assignment;
    }
}

$pageModuleStmt = $pdo->prepare(
    'INSERT INTO page_modules (page_id, module_id, region_name, sort_order, is_enabled) VALUES (?, ?, ?, ?, ?)'
);

foreach ($pageModules as $assignment) {
    $pageModuleStmt->execute([
        $pageIds[$assignment[0]],
        $moduleIds[$assignment[1]],
        $assignment[2],
        $assignment[3],
        $assignment[4],
    ]);
}

echo "Seed complete.\n";
