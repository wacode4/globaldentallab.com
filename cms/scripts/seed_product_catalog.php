<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$languages = cms_languages();

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
    $categoryIds[$category['slug']] = cms_upsert_product_category($category, $translations);
}

foreach ($products as $product) {
    $translations = [];
    foreach ($languages as $language) {
        $translations[$language['code']] = $product['translations'][$language['code']] ?? ($product['translations']['en'] ?? []);
    }
    cms_upsert_product([
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
