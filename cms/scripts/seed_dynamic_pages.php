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
    'downloads' => [
        'definition' => ['downloads', 'resource', 'marketing', 'published', 130, 0],
        'translations' => cms_seed_page_translations(
            'Downloads',
            'Downloads',
            'Downloads - RX Forms, Catalogs & Guides',
            'Download RX forms, catalogs, preparation guides, and implant references from Global Dental Lab.'
        ),
    ],
    'send-a-case' => [
        'definition' => ['send-a-case', 'conversion', 'marketing', 'published', 135, 0],
        'translations' => cms_seed_page_translations(
            'Send A Case',
            'Send A Case',
            'Send A Case - Digital Submission & Shipping',
            'Send digital cases to Global Dental Lab through scanner platforms, cloud delivery, or physical shipment.'
        ),
    ],
    'materials' => [
        'definition' => ['materials', 'resource', 'marketing', 'published', 140, 0],
        'translations' => cms_seed_page_translations(
            'Materials',
            'Materials',
            'Materials & Compatible Systems',
            'Review ceramics, acrylics, attachment systems, and compatibility references used by Global Dental Lab.'
        ),
    ],
    'certificates' => [
        'definition' => ['certificates', 'trust', 'marketing', 'published', 150, 0],
        'translations' => cms_seed_page_translations(
            'Certificates',
            'Certificates',
            'Certificates - FDA, CE & ISO',
            'Review Global Dental Lab quality and compliance certificates, including FDA, CE, and ISO-related documentation.'
        ),
    ],
    'lab-tour' => [
        'definition' => ['lab-tour', 'trust', 'marketing', 'published', 160, 0],
        'translations' => cms_seed_page_translations(
            'Lab Tour',
            'Lab Tour',
            'Lab Tour - Facility, Workflow & Quality Control',
            'See the production environment, digital workflow, ceramic finishing, and quality control process behind Global Dental Lab cases.'
        ),
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

$menuStmt = $pdo->prepare(
    'INSERT INTO menus (menu_key, name)
     VALUES (?, ?)
     ON DUPLICATE KEY UPDATE
        name = VALUES(name),
        updated_at = CURRENT_TIMESTAMP'
);

$menuDefinitions = [
    'primary' => [
        'name' => 'Primary Navigation',
        'items' => [
            ['page_slug' => 'home', 'sort_order' => 10],
            ['page_slug' => 'about', 'sort_order' => 20],
            ['page_slug' => 'technology', 'sort_order' => 30],
            ['page_slug' => 'services', 'sort_order' => 40],
            ['page_slug' => 'contact', 'sort_order' => 50],
        ],
    ],
    'footer' => [
        'name' => 'Footer Navigation',
        'items' => [
            ['page_slug' => 'about', 'sort_order' => 10],
            ['page_slug' => 'technology', 'sort_order' => 20],
            ['page_slug' => 'services', 'sort_order' => 30],
            ['page_slug' => 'ceramics', 'sort_order' => 40],
            ['page_slug' => 'materials', 'sort_order' => 50],
            ['page_slug' => 'downloads', 'sort_order' => 60],
            ['page_slug' => 'send-a-case', 'sort_order' => 70],
            ['page_slug' => 'certificates', 'sort_order' => 80],
            ['page_slug' => 'lab-tour', 'sort_order' => 90],
            ['page_slug' => 'contact', 'sort_order' => 100],
        ],
    ],
];

$menuItemInsertStmt = $pdo->prepare(
    'INSERT INTO menu_items (menu_id, page_id, custom_label, custom_url, sort_order, target, is_enabled)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$menuItemTranslationInsertStmt = $pdo->prepare(
    'INSERT INTO menu_item_translations (menu_item_id, language_id, custom_label)
     VALUES (?, ?, ?)'
);

foreach ($menuDefinitions as $menuKey => $definition) {
    $menuStmt->execute([$menuKey, $definition['name']]);
    $menuId = (int) $pdo->query("SELECT id FROM menus WHERE menu_key = " . $pdo->quote($menuKey))->fetchColumn();
    $pdo->prepare('DELETE FROM menu_items WHERE menu_id = ?')->execute([$menuId]);

    foreach ($definition['items'] as $item) {
        $menuItemInsertStmt->execute([
            $menuId,
            $pageIds[$item['page_slug']] ?? null,
            $item['custom_label'] ?? '',
            $item['custom_url'] ?? '',
            (int) ($item['sort_order'] ?? 100),
            $item['target'] ?? '_self',
            1,
        ]);

        $menuItemId = (int) $pdo->lastInsertId();
        foreach (($item['translations'] ?? []) as $code => $translatedLabel) {
            $translatedLabel = trim((string) $translatedLabel);
            if ($translatedLabel === '' || !isset($languageIds[$code])) {
                continue;
            }

            $menuItemTranslationInsertStmt->execute([
                $menuItemId,
                $languageIds[$code],
                $translatedLabel,
            ]);
        }
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
            'en' => ['Contact & Case Intake', 'Contact The Lab Or Start Intake', 'Use the inquiry form for onboarding and support, or go straight to digital submission and downloads.', '', json_encode([
                'label' => 'Contact & Intake',
                'title_html' => 'Contact The Lab<br>Or Start Intake',
                'subtitle_html' => 'Use the inquiry form for onboarding and support, or go straight to digital submission and downloads.',
                'buttons' => [
                    ['text' => 'SEND A CASE', 'href' => '/en/send-a-case', 'style' => 'white'],
                    ['text' => 'DOWNLOAD FORMS', 'href' => '/en/downloads', 'style' => 'primary'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
        ],
    ],
    'contact-panel' => [
        'definition' => ['contact-panel', 'contact_panel', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Inquiry & Case Intake Form', '', 'Use this form for new account questions, case planning support, turnaround discussions, or anything that does not fit one of the direct submission routes below.', '', cms_encode_json([
                'aside_title' => 'Fastest Ways To Reach The Lab',
                'aside_intro' => 'Use this page as the intake hub for first-contact questions, submission support, shipping coordination, and onboarding help.',
                'contacts' => [
                    ['kind' => 'phone', 'label' => 'Phone', 'value' => '+852 9142 4923', 'href' => 'tel:+85291424923'],
                    ['kind' => 'whatsapp', 'label' => 'WhatsApp', 'value' => '+852 9142 4923', 'href' => 'https://wa.me/85291424923'],
                    ['kind' => 'email', 'label' => 'Email', 'value' => 'info@globaldentallab.com', 'href' => 'mailto:info@globaldentallab.com'],
                ],
                'next_steps' => [
                    ['eyebrow' => 'Best For', 'title' => 'Digital Case Submission', 'text' => 'Platform connections, scanner workflows, and cloud delivery routes.', 'href' => '/en/send-a-case', 'cta' => 'Send A Case', 'tone' => 'primary'],
                    ['eyebrow' => 'Best For', 'title' => 'RX Forms & Guides', 'text' => 'Preparation guides, PFM forms, denture forms, and product catalogs.', 'href' => '/en/downloads', 'cta' => 'Open Downloads', 'tone' => 'secondary'],
                ],
                'locations_title' => 'Shipping Addresses',
                'locations' => [
                    ['kind' => 'location', 'title' => 'Hong Kong Office', 'body_html' => '1/F Tung Chung 41 Ma Wan New Village<br>Lantau Island, Hong Kong'],
                    ['kind' => 'facility', 'title' => 'Shenzhen Production Facility', 'body_html' => '4/F, Building 1 HeTai Industrial Area<br>Shenzhen, China'],
                ],
                'hours_title' => 'Business Hours',
                'hours_note' => 'Times shown in Hong Kong Time (HKT)',
                'hours' => [
                    ['label' => 'Monday - Friday', 'value' => '9:00 AM - 6:00 PM'],
                    ['label' => 'Saturday', 'value' => '9:00 AM - 1:00 PM'],
                    ['label' => 'Sunday', 'value' => 'Closed'],
                ],
                'service_options' => [
                    ['value' => '', 'label' => 'Select a service'],
                    ['value' => 'cad-veneers', 'label' => 'CAD Veneers'],
                    ['value' => 'ceramics', 'label' => 'All Ceramics'],
                    ['value' => 'implants', 'label' => 'Implant Products'],
                    ['value' => 'surgical-guides', 'label' => 'Implant Surgical Guides'],
                    ['value' => 'pfm', 'label' => 'PFM & Snap-On Smile'],
                    ['value' => 'aligners', 'label' => 'Clear Aligners'],
                    ['value' => 'removable', 'label' => 'Removables'],
                    ['value' => 'orthodontics', 'label' => 'Orthodontics'],
                    ['value' => 'shipping', 'label' => 'Shipping / New Account Setup'],
                    ['value' => 'other', 'label' => 'Other'],
                ],
            ])],
        ],
    ],
    'contact-faq' => [
        'definition' => ['contact-faq', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 2, 'section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['Before You Send The First Case', 'Common Questions', '', '', cms_encode_json([
                'items' => [
                    ['title' => 'Can you accept digital impressions?', 'text' => 'Yes. The current workflow supports major platforms including TRIOS, Medit, iTero, Carestream, Dentsply Sirona, Shining3D, and cloud-delivered files.'],
                    ['title' => 'Can I still ship physical impressions?', 'text' => 'Yes. Traditional case intake remains part of the operating model. Use the shipping addresses above and pair the shipment with the correct RX form.'],
                    ['title' => 'Where do I get RX forms?', 'text' => 'Use the downloads page for catalogs, preparation guides, denture RX forms, and PFM forms.'],
                    ['title' => 'What if my scanner platform is not listed?', 'text' => 'Use the contact form, phone, or WhatsApp and the intake team will route you to the correct submission method.'],
                ],
            ])],
        ],
    ],
    'contact-cta' => [
        'definition' => ['contact-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Next Step', 'Ready To Start The Relationship Properly?', 'Use the right intake route from the beginning: digital submission for platform-connected cases, downloads for RX forms, or direct contact for onboarding and shipping.', '', json_encode([
                'buttons' => [
                    ['text' => 'Send A Case', 'href' => '/en/send-a-case', 'style' => 'primary'],
                    ['text' => 'Open Downloads', 'href' => '/en/downloads', 'style' => 'secondary'],
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
    'downloads-hero' => [
        'definition' => ['downloads-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Downloads', 'Resource Center', 'Catalogs, RX forms, preparation guides, and manufacturer references for faster case intake.', '', cms_encode_json([
                'label' => 'Resource Center',
                'title_html' => 'Downloads &amp;<br>Case Forms',
                'subtitle_html' => 'Catalogs, RX forms, preparation guides, and manufacturer references for faster case intake.',
                'buttons' => [
                    ['text' => 'Send A Case', 'href' => '/en/send-a-case', 'style' => 'primary'],
                    ['text' => 'Contact Us', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'downloads-intro' => [
        'definition' => ['downloads-intro', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'right', 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['Everything Your Team Needs Before Submission', 'Case Resources', 'This page brings together the core files clinics, production coordinators, and chairside teams need before submitting a case.', '<p>Use the right RX form first, then pair it with scans, photos, or physical impressions so the case arrives with less back-and-forth.</p><p>This resource page is designed to support intake clarity, not just file storage, so the linked materials stay aligned with practical submission workflow.</p>', cms_encode_json([
                'image' => '/images/content/digital-workflow.jpg',
                'image_alt' => 'Digital intake and planning workflow',
                'buttons' => [
                    ['text' => 'Go To Send A Case', 'href' => '/en/send-a-case', 'style' => 'primary'],
                    ['text' => 'Need Help Choosing?', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'downloads-files' => [
        'definition' => ['downloads-files', 'rich_text', 'default', 'published', cms_encode_json(['section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['Catalogs, Forms, And Practical Guides', 'Download Library', 'Open the file that matches the case type or planning question before submission.', <<<'HTML'
<div class="grid gap-8 lg:grid-cols-2 not-prose">
    <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary">Catalogs</p>
        <h3 class="mt-3 text-2xl font-bold text-navy">Catalogs &amp; Product Reference</h3>
        <div class="mt-6 space-y-4">
            <a href="/downloads/catalog-1.pdf" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">
                <span>Catalog 1</span><span>Download</span>
            </a>
            <a href="/downloads/catalog-2.pdf" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">
                <span>Catalog 2</span><span>Download</span>
            </a>
            <a href="/downloads/all-ceramic-chairside-prep-guide.pdf" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">
                <span>Full Ceramic Restoration Chart &amp; Preparation Guide</span><span>Download</span>
            </a>
        </div>
    </div>
    <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-primary">RX Forms</p>
        <h3 class="mt-3 text-2xl font-bold text-navy">Prescription Forms</h3>
        <div class="mt-6 space-y-4">
            <a href="/downloads/lab-rx-denture.pdf" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">
                <span>Lab RX Denture</span><span>Download</span>
            </a>
            <a href="/downloads/lab-rx-pfm.pdf" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">
                <span>Lab RX PFM</span><span>Download</span>
            </a>
            <a href="/downloads/lab-rx-pfm-denture-combined.pdf" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">
                <span>Lab RX PFM &amp; Denture Combined</span><span>Download</span>
            </a>
            <a href="/downloads/noble-crown-reference.pdf" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">
                <span>Noble Crown Reference</span><span>Download</span>
            </a>
        </div>
    </div>
</div>
HTML, '{}'],
        ],
    ],
    'downloads-links' => [
        'definition' => ['downloads-links', 'rich_text', 'default', 'published', cms_encode_json(['section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['Implant & Manufacturer Reference Links', 'Useful References', 'Quick outbound links for restorative compatibility checks and vendor familiarity.', <<<'HTML'
<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4 not-prose">
    <a href="http://www.nobelbiocare.com/en/products-solutions/default.aspx" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Nobel Biocare</a>
    <a href="http://www.zimmerdental.com/Home/zimmerDental.aspx" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Zimmer</a>
    <a href="http://biomet3i.com/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">BIOMET 3i</a>
    <a href="http://www.dentsply.com/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Dentsply</a>
    <a href="http://www.mis-implants.com/Products/Implants.aspx" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">MIS Implants</a>
    <a href="http://www.implantdirect.com/us/default.asp" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Implant Direct</a>
    <a href="http://www.astratechdental.us/Main.aspx/Item/775955/navt/72677/navl/90870/nava/90873" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Atlantis</a>
    <a href="http://www.camlogimplants.com/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Camlog</a>
    <a href="http://www.astratechdental.us/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Astra Tech</a>
    <a href="http://www.ocobiomedical.com/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">OCO Biomedical</a>
    <a href="http://www.straumann.us/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Straumann</a>
    <a href="http://www.imtec.com/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Imtec</a>
    <a href="http://www.biohorizons.com/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">BioHorizons</a>
    <a href="http://www.bicon.com/" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Bicon</a>
    <a href="http://www.innovalife.com/index/innovalife" target="_blank" rel="noopener noreferrer" class="rounded-2xl border border-slate-200 px-5 py-4 text-sm font-semibold text-navy transition hover:border-primary hover:text-primary">Innova Corporation</a>
</div>
HTML, '{}'],
        ],
    ],
    'downloads-cta' => [
        'definition' => ['downloads-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Use The Right File Before Intake', 'Download The Form, Then Submit The Case', 'Pick the matching RX form, prep the scans or shipment, and move straight into intake with fewer clarification loops.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Go To Send A Case', 'href' => '/en/send-a-case', 'style' => 'primary'],
                    ['text' => 'Need Help Choosing?', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'send-a-case-hero' => [
        'definition' => ['send-a-case-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Send A Case', 'Case Intake', 'Connect by scanner platform, cloud delivery, or conventional shipping workflow.', '', cms_encode_json([
                'label' => 'Case Intake',
                'title_html' => 'Send A Case<br>Without Friction',
                'subtitle_html' => 'Connect by scanner platform, cloud delivery, or conventional shipping workflow.',
                'buttons' => [
                    ['text' => 'Download RX Forms', 'href' => '/en/downloads', 'style' => 'primary'],
                    ['text' => 'Contact Intake', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'send-a-case-workflow' => [
        'definition' => ['send-a-case-workflow', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 4, 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['Connect Your Scanner Or Send Files Directly', 'Digital Case Workflow', 'The legacy Bright Dental Lab workflow is preserved here so clinics can submit cases with minimal retraining.', '', cms_encode_json([
                'items' => [
                    [
                        'eyebrow' => 'Platforms',
                        'title' => 'Supported Scanners',
                        'text' => 'Major scanner ecosystems can be routed directly into the lab workflow.',
                        'bullets' => ['TRIOS / 3Shape Communicate', 'Medit', 'iTero', 'Carestream', 'Dentsply Sirona', 'Shining3D'],
                    ],
                    [
                        'eyebrow' => 'Fallback',
                        'title' => 'Cloud Delivery',
                        'text' => 'If direct scanner routing is not available, file delivery can still move through common transfer tools.',
                        'bullets' => ['Dropbox', 'OneDrive', 'WeTransfer', 'Direct email delivery'],
                    ],
                    [
                        'eyebrow' => 'Support',
                        'title' => 'Intake Follow-Up',
                        'text' => 'Use support channels when a case needs help before submission is finalized.',
                        'bullets' => ['WhatsApp follow-up', 'Intake email support', 'Phone support'],
                    ],
                    [
                        'eyebrow' => 'Outcome',
                        'title' => 'Less Retraining',
                        'text' => 'The page preserves familiar submission behavior while fitting the new CMS route model.',
                        'meta' => 'Fast intake routing',
                    ],
                ],
            ])],
        ],
    ],
    'send-a-case-accounts' => [
        'definition' => ['send-a-case-accounts', 'rich_text', 'default', 'published', cms_encode_json(['section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['Submission Accounts And Platform Steps', 'Current Accounts', 'Use the matching account block below, then follow the platform-specific submission path.', <<<'HTML'
<div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] not-prose">
    <div class="rounded-3xl bg-navy p-8 text-white shadow-sm">
        <p class="text-xs font-bold uppercase tracking-[0.24em] text-white/60">Current Accounts</p>
        <div class="mt-6 space-y-4 text-sm">
            <div class="rounded-2xl bg-white/5 p-4">
                <p class="font-semibold text-white">Medit / TRIOS / iTero / 3Shape / Carestream</p>
                <p class="mt-1 text-white/75">dentallabcn@gmail.com</p>
            </div>
            <div class="rounded-2xl bg-white/5 p-4">
                <p class="font-semibold text-white">Dentsply Sirona</p>
                <p class="mt-1 text-white/75">BDL-2025</p>
            </div>
            <div class="rounded-2xl bg-white/5 p-4">
                <p class="font-semibold text-white">Shining3D</p>
                <p class="mt-1 text-white/75">+86-138-2352-9264</p>
            </div>
            <div class="rounded-2xl bg-white/5 p-4">
                <p class="font-semibold text-white">Dropbox / OneDrive / WeTransfer</p>
                <p class="mt-1 text-white/75">Dropbox: info@dental-lab-china.com</p>
                <p class="text-white/75">OneDrive: brightdentallab@outlook.com</p>
                <p class="text-white/75">WeTransfer / email: bright-digital@dental-lab-china.com</p>
            </div>
        </div>
    </div>
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm">
            <h3 class="text-2xl font-bold text-navy">TRIOS / 3Shape Communicate</h3>
            <ol class="mt-4 list-inside list-decimal space-y-3 text-sm leading-7 text-slate-600">
                <li>Visit portal.3shapecommunicate.com and log in.</li>
                <li>Open Connections and choose Add Connection.</li>
                <li>Search for dentallabcn@gmail.com with Show me Labs enabled.</li>
                <li>Select Bright Dental Laboratory and request connection.</li>
                <li>After approval, refresh scanner lab connections and submit normally.</li>
            </ol>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm">
            <h3 class="text-2xl font-bold text-navy">Dentsply Sirona / CEREC Connect</h3>
            <ol class="mt-4 list-inside list-decimal space-y-3 text-sm leading-7 text-slate-600">
                <li>Log in via customer.connectcasecentre.com.</li>
                <li>Open My Favourite Contractors and search recipients.</li>
                <li>Select country China and use zip code 518103.</li>
                <li>Add Bright Dental Lab from the search results.</li>
                <li>After scanning, choose Connect and submit the impression to the lab.</li>
            </ol>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm">
            <h3 class="text-2xl font-bold text-navy">Carestream Connect</h3>
            <ol class="mt-4 list-inside list-decimal space-y-3 text-sm leading-7 text-slate-600">
                <li>Log into CS Connect.</li>
                <li>Open Partners and choose Invite a partner.</li>
                <li>Enter dentallabcn@gmail.com and send the invitation.</li>
                <li>Once accepted, select Global Dental Lab during case submission.</li>
            </ol>
        </div>
        <div class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm">
            <h3 class="text-2xl font-bold text-navy">If Your Platform Is Not Listed</h3>
            <p class="mt-4 text-sm leading-7 text-slate-600">Contact the intake team and we will map the correct delivery route for your scanner, transfer portal, or cloud storage workflow.</p>
            <div class="mt-5 space-y-3 text-sm">
                <a href="mailto:info@globaldentallab.com" class="flex items-center justify-between rounded-2xl bg-slate-50 px-5 py-4 font-semibold text-navy transition hover:bg-slate-100"><span>Email intake support</span><span class="text-primary">info@globaldentallab.com</span></a>
                <a href="https://wa.me/85291424923" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between rounded-2xl bg-slate-50 px-5 py-4 font-semibold text-navy transition hover:bg-slate-100"><span>WhatsApp case support</span><span class="text-primary">Open chat</span></a>
                <a href="tel:+85291424923" class="flex items-center justify-between rounded-2xl bg-slate-50 px-5 py-4 font-semibold text-navy transition hover:bg-slate-100"><span>Call the intake desk</span><span class="text-primary">+852 9142 4923</span></a>
            </div>
        </div>
    </div>
</div>
HTML, '{}'],
        ],
    ],
    'send-a-case-shipping' => [
        'definition' => ['send-a-case-shipping', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'left', 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['Ship Traditional Impressions Or Models', 'Physical Case Shipping', 'You can still send conventional cases. Download the correct RX form, pack impressions or models securely, and notify the lab with the shipment tracking number.', '<div class="space-y-4"><div class="rounded-2xl bg-slate-50 p-6"><p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Hong Kong</p><p class="mt-2 font-semibold text-navy">Global Dental Lab</p><p class="mt-2 text-sm leading-7 text-slate-600">1/F Tung Chung 41 Ma Wan New Village<br>Lantau Island, Hong Kong</p></div><div class="rounded-2xl bg-slate-50 p-6"><p class="text-xs font-bold uppercase tracking-[0.22em] text-slate-400">Shenzhen Production</p><p class="mt-2 font-semibold text-navy">Global Dental Lab Production Facility</p><p class="mt-2 text-sm leading-7 text-slate-600">4/F, Building 1 HeTai Industrial Area<br>Shenzhen, China</p></div></div>', cms_encode_json([
                'image' => '/images/source-lab-tour/lab-tour-07.jpg',
                'image_alt' => 'Facility shipping and intake support context',
                'buttons' => [
                    ['text' => 'Download RX Forms', 'href' => '/en/downloads', 'style' => 'primary'],
                    ['text' => 'Ask About Shipping', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'send-a-case-cta' => [
        'definition' => ['send-a-case-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Need Help Mapping Your Submission Route?', 'Still Not Sure Which Workflow To Use?', 'Use the contact route when you need help matching the right scanner path, cloud delivery option, or shipping workflow before the case is sent.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Contact Intake', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'Open Downloads', 'href' => '/en/downloads', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'materials-hero' => [
        'definition' => ['materials-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Materials', 'Materials & Compatibility', 'A practical overview of restorative materials, removable options, and supported compatibility references.', '', cms_encode_json([
                'label' => 'Materials & Compatibility',
                'title_html' => 'Materials &amp;<br>Compatible Systems',
                'subtitle_html' => 'A practical overview of restorative materials, removable options, and supported compatibility references.',
                'buttons' => [
                    ['text' => 'See Downloads', 'href' => '/en/downloads', 'style' => 'primary'],
                    ['text' => 'Send A Case', 'href' => '/en/send-a-case', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'materials-intro' => [
        'definition' => ['materials-intro', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'right', 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['A Clearer Reference Point For Materials, Attachments, And Case Planning', 'Working Reference', 'Use this page as a practical summary of the material and compatibility references that matter most during outsourcing.', '<p>The goal is not to list every SKU. It is to group materials into decision-ready categories so doctors, planners, and coordinators can align the case faster.</p><p>This page works best alongside RX forms, scans, shade notes, and any implant or attachment detail your clinic already has before submission.</p>', cms_encode_json([
                'image' => '/images/source-materials/materials-content.png',
                'image_alt' => 'Compatibility reference chart for materials',
                'buttons' => [
                    ['text' => 'Open Downloads', 'href' => '/en/downloads', 'style' => 'primary'],
                    ['text' => 'Send A Case', 'href' => '/en/send-a-case', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'materials-brands' => [
        'definition' => ['materials-brands', 'rich_text', 'default', 'published', cms_encode_json(['section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['Material Families Your Team Is Likely To Recognize', 'Brand Familiarity', 'Doctors and coordinators often map a page faster when familiar logos and material families stay visible.', <<<'HTML'
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-5 not-prose">
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/ips-emax.png" alt="IPS e.max logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">IPS e.max</h3><p class="mt-2 text-sm text-slate-600">Pressed and layered esthetic ceramic workflows.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/vita-vm9.png" alt="VITA VM9 logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">VITA VM9</h3><p class="mt-2 text-sm text-slate-600">Ceramic layering support for high-esthetic cases.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/wieland.png" alt="Wieland logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">Wieland</h3><p class="mt-2 text-sm text-slate-600">Zirconia-oriented workflows and restorative planning.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/aidite.jpg" alt="Aidite logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">Aidite</h3><p class="mt-2 text-sm text-slate-600">Digital ceramic production and milling-adjacent systems.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/shofu.png" alt="Shofu logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">Shofu</h3><p class="mt-2 text-sm text-slate-600">Finishing and esthetic material familiarity for lab teams.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/bredent.png" alt="bredent logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">bredent</h3><p class="mt-2 text-sm text-slate-600">Bars, attachment concepts, and removable support options.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/ceka.jpg" alt="CEKA logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">CEKA</h3><p class="mt-2 text-sm text-slate-600">Attachment systems referenced for precision removable cases.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/dentsply.jpg" alt="Dentsply logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">Dentsply</h3><p class="mt-2 text-sm text-slate-600">Recognized implant and denture-related material references.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/valplast.png" alt="Valplast logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">Valplast</h3><p class="mt-2 text-sm text-slate-600">Flexible removable indications and acrylic alternatives.</p></div>
    <div class="rounded-3xl border border-slate-200 bg-white p-6 text-center"><img src="/images/source-materials/tcs.jpg" alt="TCS logo" class="mx-auto h-12 object-contain"><h3 class="mt-4 text-lg font-bold text-navy">TCS</h3><p class="mt-2 text-sm text-slate-600">Thermoplastic removable pathways for selective indications.</p></div>
</div>
HTML, '{}'],
        ],
    ],
    'materials-guide' => [
        'definition' => ['materials-guide', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 3, 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['How We Frame Material Choice Inside Case Communication', 'Decision Categories', 'The page should make planning conversations faster by grouping materials into decision-ready buckets.', '', cms_encode_json([
                'items' => [
                    ['title' => 'Fixed And Esthetic', 'text' => 'PFM, zirconia, layered ceramics, and anterior workflows supported by digital design or conventional submission.'],
                    ['title' => 'Attachments And Bars', 'text' => 'Attachment-oriented removable cases benefit from early communication around bars, locks, and component direction.'],
                    ['title' => 'Removable And Acrylic', 'text' => 'Flexible and acrylic denture cases usually need the clearest indication notes, bite detail, and finishing expectations.'],
                ],
            ])],
        ],
    ],
    'materials-charts' => [
        'definition' => ['materials-charts', 'rich_text', 'default', 'published', cms_encode_json(['section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['Reference Charts For Compatibility Review', 'Visual Reference', 'Use these charts when your team needs a practical reminder of material families and compatibility groupings.', <<<'HTML'
<div class="grid gap-6 md:grid-cols-3 not-prose">
    <figure class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <img src="/images/source-materials/materials-chart-1.png" alt="Material chart for porcelain fused to metal references" class="h-80 w-full object-cover object-top">
        <figcaption class="p-4 text-sm text-slate-600">Fixed restorative references including PFM, ceramic, and attachment-related groupings.</figcaption>
    </figure>
    <figure class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <img src="/images/source-materials/materials-chart-2.png" alt="Material chart for restorative compatibility references" class="h-80 w-full object-cover object-top">
        <figcaption class="p-4 text-sm text-slate-600">Additional compatibility tables for reviewing restorative material groupings and planning references.</figcaption>
    </figure>
    <figure class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <img src="/images/source-materials/materials-chart-3.png" alt="Material chart for acrylic and attachment compatibility" class="h-80 w-full object-cover object-top">
        <figcaption class="p-4 text-sm text-slate-600">Useful when reviewing acrylic, denture base, and precision attachment references.</figcaption>
    </figure>
</div>
HTML, '{}'],
        ],
    ],
    'materials-cta' => [
        'definition' => ['materials-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Turn Compatibility Questions Into Cleaner Submissions', 'Next Step', 'If the restorative direction is clear, use this page as a pre-submission check. If not, send the scans, photos, and treatment goal first and let the lab help align the material route.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Send A Case', 'href' => '/en/send-a-case', 'style' => 'primary'],
                    ['text' => 'Ask About Compatibility', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'certificates-hero' => [
        'definition' => ['certificates-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Certificates', 'Trust Center', 'A clear trust page for clinics that need documentation before onboarding or scaling volume.', '', cms_encode_json([
                'label' => 'Trust Center',
                'title_html' => 'Certificates &amp;<br>Compliance',
                'subtitle_html' => 'A clear trust page for clinics that need documentation before onboarding or scaling volume.',
                'buttons' => [
                    ['text' => 'Send A Case', 'href' => '/en/send-a-case', 'style' => 'primary'],
                    ['text' => 'Contact Us', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'certificates-intro' => [
        'definition' => ['certificates-intro', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'left', 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['Certificate Visibility For Doctors, Buyers, And Procurement Teams', 'Quality & Compliance', 'This page brings together the core certificate view clinics, distributors, and procurement teams usually request before trial cases or onboarding.', '<p>Use this page when a clinic or procurement contact needs an immediate proof set before trial cases, tender review, or vendor onboarding.</p><p>It is designed to work alongside the lab tour, downloads, and direct intake so trust-building assets stay organized instead of being scattered across one-off requests.</p>', cms_encode_json([
                'image' => '/images/source-certificates/certificate-board.jpg',
                'image_alt' => 'Global Dental Lab certificate board',
                'buttons' => [
                    ['text' => 'Request Specific Document', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'View Lab Tour', 'href' => '/en/lab-tour', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'certificates-benefits' => [
        'definition' => ['certificates-benefits', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 4, 'section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['Where This Page Helps Most', 'Trust Signals', 'A short certificate page should still explain how procurement teams and clinics actually use it.', '', cms_encode_json([
                'items' => [
                    ['eyebrow' => 'Quality', 'title' => 'ISO 13485 Alignment', 'text' => 'Quality-system language and documentation support trust before the clinic places larger volume.'],
                    ['eyebrow' => 'Compliance', 'title' => 'CE And Export-Facing Proof', 'text' => 'Useful when buyers or clinical leadership want a faster view of compliance-related documentation.'],
                    ['eyebrow' => 'Procurement', 'title' => 'Vendor Qualification', 'text' => 'Supports onboarding, group-practice review, and outsourced production approval workflows.'],
                    ['eyebrow' => 'Sales Support', 'title' => 'Trial Case Confidence', 'text' => 'Pairs well with lab tour and downloads when the relationship is still at evaluation stage.'],
                ],
            ])],
        ],
    ],
    'certificates-cta' => [
        'definition' => ['certificates-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Need A Specific Document Set?', 'Turn Trust Review Into The Next Step', 'If a doctor, buyer, or distributor needs a more specific compliance or quality document, use the contact route and request the exact proof set.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Request Documents', 'href' => '/en/contact', 'style' => 'primary'],
                    ['text' => 'View Lab Tour', 'href' => '/en/lab-tour', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'lab-tour-hero' => [
        'definition' => ['lab-tour-hero', 'hero', 'primary', 'published', '{}'],
        'translations' => [
            'en' => ['Lab Tour', 'Trust Center', 'A clearer look at the facility, team handoffs, digital production, and final inspection mindset behind every case.', '', cms_encode_json([
                'label' => 'Trust Center',
                'title_html' => 'Lab Tour &amp;<br>Workflow',
                'subtitle_html' => 'A clearer look at the facility, team handoffs, digital production, and final inspection mindset behind every case.',
                'buttons' => [
                    ['text' => 'View Certificates', 'href' => '/en/certificates', 'style' => 'primary'],
                    ['text' => 'Contact Us', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'lab-tour-intro' => [
        'definition' => ['lab-tour-intro', 'media_split', 'default', 'published', cms_encode_json(['image_position' => 'right', 'section_class' => 'bg-white py-20'])],
        'translations' => [
            'en' => ['A Production Floor Built Around Digital Handoff, Finishing Discipline, And Final Inspection', 'Facility View', 'This tour gives clinics a clearer look at what happens inside the lab, how cases move between teams, and where quality control sits in the workflow.', '<ul><li>Digital files and prescription details move into planning before production begins.</li><li>CAD/CAM, ceramic work, finishing, and inspection remain visible parts of the story.</li><li>This page is especially useful for clinics evaluating a new outsourcing partner before trial cases.</li></ul>', cms_encode_json([
                'image' => '/images/source-lab-tour/production-team.jpg',
                'image_alt' => 'Production team and working environment at Global Dental Lab',
                'buttons' => [
                    ['text' => 'View Certificates', 'href' => '/en/certificates', 'style' => 'primary'],
                    ['text' => 'Book A Conversation', 'href' => '/en/contact', 'style' => 'secondary'],
                ],
            ])],
        ],
    ],
    'lab-tour-checkpoints' => [
        'definition' => ['lab-tour-checkpoints', 'feature_list', 'default', 'published', cms_encode_json(['columns' => 4, 'section_class' => 'bg-slate-50 py-20'])],
        'translations' => [
            'en' => ['The Checkpoints Clinics Usually Want To See Before Scaling Volume', 'What This Tour Shows', 'This section maps the workflow stages doctors and coordinators usually ask about before sending larger case volume.', '', cms_encode_json([
                'items' => [
                    ['eyebrow' => '01', 'title' => 'Digital Intake', 'text' => 'Incoming scans, photos, and RX notes are organized before design and manufacturing begin.'],
                    ['eyebrow' => '02', 'title' => 'CAD/CAM Production', 'text' => 'Machine-based workflows support repeatability for outsourced restorative work.'],
                    ['eyebrow' => '03', 'title' => 'Ceramic & Finishing', 'text' => 'Esthetic refinement, shade work, contouring, and hand-finished details remain visible parts of the process.'],
                    ['eyebrow' => '04', 'title' => 'Quality Control', 'text' => 'Final inspection and release checks help protect consistency before dispatch and delivery.'],
                ],
            ])],
        ],
    ],
    'lab-tour-gallery' => [
        'definition' => ['lab-tour-gallery', 'card_grid', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['Department Flow And Facility Views', 'Visual Gallery', 'A mix of department views helps clinics understand how cases move through the lab without visiting in person.', '', cms_encode_json([
                'cards' => [
                    ['title' => 'Machine-Side Production', 'text' => 'Repeatable digital workflows supported by milling and machine-area coordination.', 'image' => '/images/source-lab-tour/lab-tour-01.jpg'],
                    ['title' => 'Technician Workspace', 'text' => 'Production benches and active case handling zones used across daily workflow.', 'image' => '/images/source-lab-tour/lab-tour-03.jpg'],
                    ['title' => 'Facility Interior', 'text' => 'A broader view of work zones and department layout inside the lab.', 'image' => '/images/source-lab-tour/lab-tour-07.jpg'],
                    ['title' => 'Restorative Department', 'text' => 'A case-handling area where restorative work is coordinated through production.', 'image' => '/images/source-lab-tour/lab-tour-08.jpg'],
                    ['title' => 'Finishing And QC', 'text' => 'Finishing and release-oriented zones tied to final inspection steps.', 'image' => '/images/source-lab-tour/lab-tour-09.jpg'],
                    ['title' => 'Supporting Equipment', 'text' => 'Equipment that helps stabilize turnaround and workflow consistency.', 'image' => '/images/source-lab-tour/lab-tour-10.jpg'],
                ],
            ])],
        ],
    ],
    'lab-tour-cta' => [
        'definition' => ['lab-tour-cta', 'cta_banner', 'default', 'published', '{}'],
        'translations' => [
            'en' => ['You Do Not Need An In-Person Visit To Understand How The Lab Works', 'Remote Trust', 'For many clinics this is the first confidence-building step before sample cases. Pair it with certificates, downloads, and a direct case review conversation so the relationship starts with clearer expectations.', '', cms_encode_json([
                'buttons' => [
                    ['text' => 'Send A Trial Case', 'href' => '/en/send-a-case', 'style' => 'primary'],
                    ['text' => 'Check Documents', 'href' => '/en/certificates', 'style' => 'secondary'],
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
    ['contact', 'contact-faq', 'main', 30, 1],
    ['contact', 'contact-cta', 'main', 40, 1],
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
    ['downloads', 'downloads-hero', 'main', 10, 1],
    ['downloads', 'downloads-intro', 'main', 20, 1],
    ['downloads', 'downloads-files', 'main', 30, 1],
    ['downloads', 'downloads-links', 'main', 40, 1],
    ['downloads', 'downloads-cta', 'main', 50, 1],
    ['send-a-case', 'send-a-case-hero', 'main', 10, 1],
    ['send-a-case', 'send-a-case-workflow', 'main', 20, 1],
    ['send-a-case', 'send-a-case-accounts', 'main', 30, 1],
    ['send-a-case', 'send-a-case-shipping', 'main', 40, 1],
    ['send-a-case', 'send-a-case-cta', 'main', 50, 1],
    ['materials', 'materials-hero', 'main', 10, 1],
    ['materials', 'materials-intro', 'main', 20, 1],
    ['materials', 'materials-brands', 'main', 30, 1],
    ['materials', 'materials-guide', 'main', 40, 1],
    ['materials', 'materials-charts', 'main', 50, 1],
    ['materials', 'materials-cta', 'main', 60, 1],
    ['certificates', 'certificates-hero', 'main', 10, 1],
    ['certificates', 'certificates-intro', 'main', 20, 1],
    ['certificates', 'certificates-benefits', 'main', 30, 1],
    ['certificates', 'certificates-cta', 'main', 40, 1],
    ['lab-tour', 'lab-tour-hero', 'main', 10, 1],
    ['lab-tour', 'lab-tour-intro', 'main', 20, 1],
    ['lab-tour', 'lab-tour-checkpoints', 'main', 30, 1],
    ['lab-tour', 'lab-tour-gallery', 'main', 40, 1],
    ['lab-tour', 'lab-tour-cta', 'main', 50, 1],
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
