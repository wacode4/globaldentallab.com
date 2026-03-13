<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$languages = cms_languages();
$existingCategoryIds = [];
$existingProductIds = [];

foreach (cms_db()->query('SELECT id, slug FROM product_categories')->fetchAll() as $row) {
    $existingCategoryIds[$row['slug']] = (int) $row['id'];
}

foreach (cms_db()->query('SELECT id, slug FROM products')->fetchAll() as $row) {
    $existingProductIds[$row['slug']] = (int) $row['id'];
}

$categories = [
    [
        'slug' => 'new-cad-veneers',
        'page_slug' => 'services',
        'status' => 'published',
        'sort_order' => 10,
        'image_path' => '/images/content/veneers.jpg',
        'translations' => [
            'en' => [
                'name' => 'NEW CAD VENEERS',
                'nav_label' => 'NEW CAD VENEERS',
                'summary' => 'Digital smile-focused veneer design and anterior esthetic planning.',
                'content_html' => <<<'HTML'
<h3>Where this route fits</h3>
<ul>
    <li><strong>Smile-design cases</strong> that need planning before production starts.</li>
    <li><strong>Digital veneer workflows</strong> supported by scans, photos, and preparation guidance.</li>
    <li><strong>Anterior esthetic cases</strong> where communication matters as much as material choice.</li>
</ul>
<p>Use this category when veneer planning needs a distinct intake path rather than being buried inside general ceramics.</p>
HTML,
                'seo_title' => 'NEW CAD VENEERS',
                'seo_description' => 'Digital veneer workflow and esthetic planning support.',
            ],
        ],
    ],
    [
        'slug' => 'all-ceramics',
        'page_slug' => 'ceramics',
        'status' => 'published',
        'sort_order' => 20,
        'image_path' => '/images/content/zirconia.jpg',
        'translations' => [
            'en' => [
                'name' => 'ALL-CERAMICS',
                'nav_label' => 'ALL-CERAMICS',
                'summary' => 'Zirconia, e.max, layered, monolithic, veneers, and inlays for restorative workflows.',
                'content_html' => <<<'HTML'
<h3>What this category covers</h3>
<ul>
    <li><strong>Esthetic zirconia</strong> for visible-zone work where translucency matters.</li>
    <li><strong>Lithium disilicate</strong> for smile-zone crowns, veneers, and bonded restorations.</li>
    <li><strong>Strength-led zirconia</strong> for posterior or durability-driven case plans.</li>
    <li><strong>Conservative indirects</strong> such as veneers, inlays, and onlays.</li>
</ul>
<p>Use this category when the clinic is still comparing product routes inside ceramics and wants the product options narrowed before submission.</p>
HTML,
                'seo_title' => 'ALL-CERAMICS',
                'seo_description' => 'Ceramic workflow category and product family.',
            ],
        ],
    ],
    [
        'slug' => 'implant-products',
        'page_slug' => 'services',
        'status' => 'published',
        'sort_order' => 30,
        'image_path' => '/images/content/implants.jpg',
        'translations' => [
            'en' => [
                'name' => 'Implant Products',
                'nav_label' => 'Implant Products',
                'summary' => 'Implant restorative workflows with compatibility review and planning support.',
                'content_html' => <<<'HTML'
<h3>Primary use cases</h3>
<ul>
    <li><strong>Restorative implant cases</strong> needing compatibility review before production.</li>
    <li><strong>Single-unit to full-arch planning</strong> where the submission package must be checked early.</li>
    <li><strong>Cases crossing restorative and surgical decisions</strong> that need direct lab communication.</li>
</ul>
HTML,
                'seo_title' => 'Implant Products',
                'seo_description' => 'Implant restorations and support workflows.',
            ],
        ],
    ],
    [
        'slug' => 'implant-surgical-guide',
        'page_slug' => 'services',
        'status' => 'published',
        'sort_order' => 40,
        'image_path' => '/images/content/digital-workflow.jpg',
        'translations' => [
            'en' => [
                'name' => 'Implant Surgical Guide',
                'nav_label' => 'Implant Surgical Guide',
                'summary' => 'Planning-driven surgical guide workflows for implant cases.',
                'content_html' => <<<'HTML'
<h3>What makes guide cases different</h3>
<ul>
    <li><strong>Data quality matters first</strong> because guide production depends on accurate scans and planning files.</li>
    <li><strong>Submission should be planning-led</strong> with implant positions and restorative intent aligned.</li>
    <li><strong>Best paired with digital workflow pages</strong> when clinics need help on file transfer and intake.</li>
</ul>
HTML,
                'seo_title' => 'Implant Surgical Guide',
                'seo_description' => 'Digital planning and surgical guide support.',
            ],
        ],
    ],
    [
        'slug' => 'pfm-snap-on-smile',
        'page_slug' => 'services',
        'status' => 'published',
        'sort_order' => 50,
        'image_path' => '/images/content/dental-crown.jpg',
        'translations' => [
            'en' => [
                'name' => 'PFM / Snap-On Smile',
                'nav_label' => 'PFM / Snap-On Smile',
                'summary' => 'PFM and conventional crown-and-bridge support with RX-form workflows.',
                'content_html' => <<<'HTML'
<h3>Why this category stays separate</h3>
<ul>
    <li><strong>Conventional fixed workflows</strong> still need a clear intake route for repeat ordering clinics.</li>
    <li><strong>RX-form driven cases</strong> often move through downloads and shipment-based intake.</li>
    <li><strong>Snap-On Smile requests</strong> sit closer to practical restorative support than premium ceramic planning.</li>
</ul>
HTML,
                'seo_title' => 'PFM / Snap-On Smile',
                'seo_description' => 'PFM and conventional restorative workflows.',
            ],
        ],
    ],
    [
        'slug' => 'clear-aligners',
        'page_slug' => 'services',
        'status' => 'published',
        'sort_order' => 60,
        'image_path' => '/images/content/orthodontics.jpg',
        'translations' => [
            'en' => [
                'name' => 'Clear Aligners',
                'nav_label' => 'Clear Aligners',
                'summary' => 'Aligner workflows supported by digital case intake and planning.',
                'content_html' => <<<'HTML'
<h3>Typical submission profile</h3>
<ul>
    <li><strong>Digital-first practices</strong> using scanner-based records and cloud file transfer.</li>
    <li><strong>Cases needing guided intake</strong> before the first aligner order goes live.</li>
    <li><strong>Communication-sensitive workflows</strong> where setup review and iteration matter.</li>
</ul>
HTML,
                'seo_title' => 'Clear Aligners',
                'seo_description' => 'Clear aligner production and intake support.',
            ],
        ],
    ],
    [
        'slug' => 'removables-denture',
        'page_slug' => 'services',
        'status' => 'published',
        'sort_order' => 70,
        'image_path' => '/images/content/emax.jpg',
        'translations' => [
            'en' => [
                'name' => 'Removables Denture',
                'nav_label' => 'Removables Denture',
                'summary' => 'Removable prosthetic workflows with RX forms and shipping support.',
                'content_html' => <<<'HTML'
<h3>Where this category helps</h3>
<ul>
    <li><strong>Denture and partial cases</strong> that need a dependable removable workflow.</li>
    <li><strong>Hybrid clinics</strong> consolidating fixed and removable cases with one lab partner.</li>
    <li><strong>Conventional shipments</strong> where forms and physical intake still matter.</li>
</ul>
HTML,
                'seo_title' => 'Removables Denture',
                'seo_description' => 'Removable denture and prosthetic workflows.',
            ],
        ],
    ],
    [
        'slug' => 'orthodontics-products',
        'page_slug' => 'services',
        'status' => 'published',
        'sort_order' => 80,
        'image_path' => '/images/content/orthodontics.jpg',
        'translations' => [
            'en' => [
                'name' => 'Orthodontics Products',
                'nav_label' => 'Orthodontics Products',
                'summary' => 'Orthodontic appliances, retainers, and digital ortho support.',
                'content_html' => <<<'HTML'
<h3>Workflow scope</h3>
<ul>
    <li><strong>Retainers and appliances</strong> that sit outside aligner-only production.</li>
    <li><strong>Digital or traditional intake</strong> depending on the appliance type.</li>
    <li><strong>Pre-submission review</strong> when the appliance choice needs manual confirmation.</li>
</ul>
HTML,
                'seo_title' => 'Orthodontics Products',
                'seo_description' => 'Orthodontic product workflows and support.',
            ],
        ],
    ],
    [
        'slug' => 'clinical-cases-prosthodontics',
        'page_slug' => 'services',
        'status' => 'published',
        'sort_order' => 90,
        'image_path' => '/images/content/dental-lab-1.jpg',
        'translations' => [
            'en' => [
                'name' => 'Clinical Cases of Prosthodontics',
                'nav_label' => 'Clinical Cases of Prosthodontics',
                'summary' => 'Case examples and workflow positioning for prosthodontic treatment planning.',
                'content_html' => <<<'HTML'
<h3>How to use this section</h3>
<ul>
    <li><strong>Reference positioning</strong> for clinics comparing similar restorative paths.</li>
    <li><strong>Planning conversations</strong> before the case becomes a fixed production order.</li>
    <li><strong>Discussion support</strong> when treatment direction is still being decided.</li>
</ul>
HTML,
                'seo_title' => 'Clinical Cases of Prosthodontics',
                'seo_description' => 'Clinical case framing for prosthodontic workflows.',
            ],
        ],
    ],
];

$products = [
    [
        'category_slug' => 'all-ceramics',
        'slug' => 'zirconia-ultra',
        'page_slug' => 'zirconia-ultra',
        'status' => 'published',
        'sort_order' => 10,
        'image_path' => '/images/content/zirconia.jpg',
        'badge' => 'Esthetic Zirconia',
        'translations' => [
            'en' => [
                'name' => 'Zirconia Ultra',
                'nav_label' => 'Zirconia Ultra',
                'short_description' => 'High-translucency zirconia for visible-zone restorations.',
                'content_html' => <<<'HTML'
<h3>Best fit</h3>
<ul>
    <li><strong>Visible-zone zirconia cases</strong> where the clinic wants more translucency without leaving the zirconia family.</li>
    <li><strong>Cases balancing esthetics and practicality</strong> rather than going fully glass-ceramic first.</li>
    <li><strong>Doctors who want a zirconia-led workflow</strong> with more natural presentation than a strength-only route.</li>
</ul>
<h3>Submission notes</h3>
<p>Include scans, shade notes, and esthetic direction so translucency expectations are aligned before production.</p>
HTML,
                'seo_title' => 'Zirconia Ultra',
                'seo_description' => 'High-translucency zirconia restorations.',
            ],
        ],
    ],
    [
        'category_slug' => 'all-ceramics',
        'slug' => 'emax',
        'page_slug' => 'emax',
        'status' => 'published',
        'sort_order' => 20,
        'image_path' => '/images/content/emax.jpg',
        'badge' => 'High Esthetics',
        'translations' => [
            'en' => [
                'name' => 'IPS e.max Lithium Disilicate',
                'nav_label' => 'IPS e.max',
                'short_description' => 'Lithium disilicate for smile-zone and adhesive restorative cases.',
                'content_html' => <<<'HTML'
<h3>Decision checkpoints</h3>
<ul>
    <li><strong>Prep design</strong> should support thickness, finish-line clarity, and esthetic control.</li>
    <li><strong>Shade communication</strong> matters more when this route is chosen for visible-zone cases.</li>
    <li><strong>Material expectation</strong> should be esthetic-led rather than strength-led.</li>
</ul>
<h3>Typical comparison set</h3>
<p>Doctors usually compare IPS e.max against ultra translucent zirconia and veneers when refining smile-zone treatment plans.</p>
HTML,
                'seo_title' => 'IPS e.max Lithium Disilicate',
                'seo_description' => 'Lithium disilicate ceramic workflows for high-esthetic cases.',
            ],
        ],
    ],
    [
        'category_slug' => 'all-ceramics',
        'slug' => 'layered-zirconia',
        'page_slug' => 'layered-zirconia',
        'status' => 'published',
        'sort_order' => 30,
        'image_path' => '/images/content/dental-lab-3.jpg',
        'badge' => 'Layered Esthetics',
        'translations' => [
            'en' => [
                'name' => 'Layered Zirconia',
                'nav_label' => 'Layered Zirconia',
                'short_description' => 'Zirconia workflow for cases that need layering detail.',
                'content_html' => <<<'HTML'
<h3>Where layered zirconia fits</h3>
<ul>
    <li><strong>Cases needing zirconia support</strong> plus more nuanced esthetic finishing.</li>
    <li><strong>Visible restorations</strong> where a monolithic route may look too flat.</li>
    <li><strong>Clinics balancing strength and presentation</strong> inside the zirconia family.</li>
</ul>
HTML,
                'seo_title' => 'Layered Zirconia',
                'seo_description' => 'Layered zirconia workflow for esthetic restorative cases.',
            ],
        ],
    ],
    [
        'category_slug' => 'all-ceramics',
        'slug' => 'monolithic-zirconia',
        'page_slug' => 'monolithic-zirconia',
        'status' => 'published',
        'sort_order' => 40,
        'image_path' => '/images/content/dental-lab-2.jpg',
        'badge' => 'Strength Route',
        'translations' => [
            'en' => [
                'name' => 'Monolithic Zirconia',
                'nav_label' => 'Monolithic Zirconia',
                'short_description' => 'Durability-first zirconia route for posterior and load-bearing cases.',
                'content_html' => <<<'HTML'
<h3>Why clinics choose this route</h3>
<ul>
    <li><strong>Posterior durability</strong> matters more than layered esthetic detail.</li>
    <li><strong>Simpler material decisions</strong> help repeat posterior workflows move faster.</li>
    <li><strong>Load-bearing cases</strong> often default here when resilience is the main concern.</li>
</ul>
HTML,
                'seo_title' => 'Monolithic Zirconia',
                'seo_description' => 'Monolithic zirconia workflow for durability-driven cases.',
            ],
        ],
    ],
    [
        'category_slug' => 'all-ceramics',
        'slug' => 'veneers',
        'page_slug' => 'veneers',
        'status' => 'published',
        'sort_order' => 50,
        'image_path' => '/images/content/veneers.jpg',
        'badge' => 'Smile Design',
        'translations' => [
            'en' => [
                'name' => 'Ceramic Veneers',
                'nav_label' => 'Ceramic Veneers',
                'short_description' => 'Smile-zone ceramic workflows with refined esthetic control.',
                'content_html' => <<<'HTML'
<h3>What makes veneer cases different</h3>
<ul>
    <li><strong>Preparation discipline</strong> and reduction planning influence final esthetics immediately.</li>
    <li><strong>Photo communication</strong> is often as important as impressions or scans.</li>
    <li><strong>Smile-design intent</strong> should be clear before production begins.</li>
</ul>
HTML,
                'seo_title' => 'Ceramic Veneers',
                'seo_description' => 'Ceramic veneer workflows for smile-zone restorations.',
            ],
        ],
    ],
    [
        'category_slug' => 'all-ceramics',
        'slug' => 'inlays-onlays',
        'page_slug' => 'inlays-onlays',
        'status' => 'published',
        'sort_order' => 60,
        'image_path' => '/images/content/emax.jpg',
        'badge' => 'Conservative Restorations',
        'translations' => [
            'en' => [
                'name' => 'Inlays & Onlays',
                'nav_label' => 'Inlays & Onlays',
                'short_description' => 'Partial-coverage indirect restorations for conservative treatment planning.',
                'content_html' => <<<'HTML'
<h3>Where these restorations fit</h3>
<ul>
    <li><strong>Conservative preparations</strong> where full coverage is not the preferred first move.</li>
    <li><strong>Bonded indirect workflows</strong> that preserve more natural structure.</li>
    <li><strong>Doctors comparing restoration extent</strong> before defaulting to crowns.</li>
</ul>
HTML,
                'seo_title' => 'Inlays & Onlays',
                'seo_description' => 'Partial-coverage ceramic restorations and conservative workflows.',
            ],
        ],
    ],
];

$categoryIds = [];
foreach ($categories as $category) {
    $translations = [];
    foreach ($languages as $language) {
        $translations[$language['code']] = $category['translations'][$language['code']] ?? ($category['translations']['en'] ?? []);
    }
    $category['id'] = $existingCategoryIds[$category['slug']] ?? null;
    $categoryIds[$category['slug']] = cms_upsert_product_category($category, $translations);
}

foreach ($products as $product) {
    $translations = [];
    foreach ($languages as $language) {
        $translations[$language['code']] = $product['translations'][$language['code']] ?? ($product['translations']['en'] ?? []);
    }
    cms_upsert_product([
        'id' => $existingProductIds[$product['slug']] ?? null,
        'category_id' => $categoryIds[$product['category_slug']] ?? null,
        'slug' => $product['slug'],
        'page_slug' => $product['page_slug'],
        'status' => $product['status'],
        'sort_order' => $product['sort_order'],
        'image_path' => $product['image_path'],
        'badge' => $product['badge'],
    ], $translations);
}

echo "Seeded product catalog.\n";
