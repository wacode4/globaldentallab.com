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
            'en' => ['Inquiry And Intake', 'Server-Side Form', 'Messages submitted here write directly into the MySQL-backed CMS instead of depending on Cloudflare functions.', '', json_encode([
                'items' => [
                    ['label' => 'Phone', 'value' => '+852 9142 4923'],
                    ['label' => 'Email', 'value' => 'info@globaldentallab.com'],
                    ['label' => 'Hong Kong', 'value_html' => '1/F Tung Chung 41 Ma Wan New Village<br>Lantau Island, Hong Kong'],
                    ['label' => 'Shenzhen', 'value_html' => '4/F, Building 1 HeTai Industrial Area<br>Shenzhen, China'],
                ],
            ], JSON_UNESCAPED_SLASHES)],
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
                    ['title' => 'e.max', 'text' => 'Lithium disilicate workflows for high esthetic demand and adhesive cases.', 'href' => '/product-emax.html', 'cta' => 'View Current Page', 'image' => '/images/content/veneers.jpg'],
                    ['title' => 'Layered Zirconia', 'text' => 'An esthetic option when layering detail is part of the restorative goal.', 'href' => '/product-layered.html', 'cta' => 'View Current Page', 'image' => '/images/content/dental-lab-3.jpg'],
                    ['title' => 'Monolithic Zirconia', 'text' => 'Durability-focused cases where streamlined manufacturing and strength are priorities.', 'href' => '/product-monolithic.html', 'cta' => 'View Current Page', 'image' => '/images/content/dental-lab-2.jpg'],
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
                    ['title' => 'e.max', 'text' => 'Explore the current static e.max page while that product is still pending migration.', 'href' => '/product-emax.html', 'cta' => 'Current Page', 'image' => '/images/content/veneers.jpg'],
                    ['title' => 'Layered Zirconia', 'text' => 'Review the layered zirconia positioning on the current product page.', 'href' => '/product-layered.html', 'cta' => 'Current Page', 'image' => '/images/content/dental-lab-3.jpg'],
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
