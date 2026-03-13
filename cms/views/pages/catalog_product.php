<main>
    <div class="bg-gray-50 py-4 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="breadcrumb flex items-center gap-2 text-sm text-gray-600">
                <a href="<?= cms_escape(cms_localized_href('/', $page['language']['code'])) ?>" class="hover:text-primary">Home</a>
                <span>/</span>
                <a href="<?= cms_escape(cms_localized_href('/services', $page['language']['code'])) ?>" class="hover:text-primary">Products</a>
                <span>/</span>
                <a href="<?= cms_escape($page['category']['href']) ?>" class="hover:text-primary"><?= cms_escape($page['category']['name']) ?></a>
                <span>/</span>
                <span class="text-navy font-medium"><?= cms_escape($page['product']['name']) ?></span>
            </nav>
        </div>
    </div>

    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16">
                <div class="fade-up">
                    <div class="rounded-2xl overflow-hidden shadow-lg">
                        <img src="<?= cms_escape($page['product']['image_path'] ?: $page['category']['image_path'] ?: '/images/content/dental-lab-1.jpg') ?>" alt="<?= cms_escape($page['product']['name']) ?>" class="w-full h-auto">
                    </div>
                </div>
                <div class="fade-up fade-up-delay-1">
                    <?php if (!empty($page['product']['badge'])): ?>
                        <span class="inline-block bg-accent/10 text-accent text-sm font-semibold px-4 py-1.5 rounded-full mb-4"><?= cms_escape($page['product']['badge']) ?></span>
                    <?php endif; ?>
                    <h1 class="text-3xl md:text-4xl font-bold text-navy mb-4"><?= cms_escape($page['product']['name']) ?></h1>
                    <p class="text-xl text-gray-600 mb-6"><?= cms_escape($page['product']['short_description']) ?></p>
                    <p class="text-gray-600 mb-8">This product sits inside <?= cms_escape($page['category']['name']) ?> and is now managed from the CMS product catalog, so naming, summary, and page content can be updated centrally.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h3 class="font-semibold text-navy mb-2">Category</h3>
                            <p class="text-sm text-gray-600"><?= cms_escape($page['category']['name']) ?></p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h3 class="font-semibold text-navy mb-2">Navigation Label</h3>
                            <p class="text-sm text-gray-600"><?= cms_escape($page['product']['nav_label'] ?: $page['product']['name']) ?></p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h3 class="font-semibold text-navy mb-2">Submission Route</h3>
                            <p class="text-sm text-gray-600">Use Send A Case when the workflow is already defined, or contact the lab for planning help first.</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h3 class="font-semibold text-navy mb-2">Page Source</h3>
                            <p class="text-sm text-gray-600">This detail page now renders from the shared product catalog instead of a standalone static HTML file.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="<?= cms_escape(cms_localized_href('/send-a-case', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center px-8 py-4 rounded-lg font-bold text-lg">Send A Case</a>
                        <a href="<?= cms_escape(cms_localized_href('/downloads', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center border-2 border-primary text-primary px-8 py-4 rounded-lg font-bold text-lg bg-white">Preparation Guide</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($page['product']['content_html'])): ?>
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-[0.95fr_1.05fr] gap-12 items-start">
                    <div>
                        <p class="text-primary font-medium mb-3 tracking-wide">WORKFLOW NOTES</p>
                        <h2 class="text-3xl font-bold text-navy mb-6">What matters most on this product route</h2>
                    </div>
                    <div class="catalog-rich"><?= $page['product']['content_html'] ?></div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($page['related_products'])): ?>
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 fade-up">
                    <h2 class="text-3xl font-bold text-navy mb-4">Related <?= cms_escape($page['category']['name']) ?> Options</h2>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <?php foreach (array_slice($page['related_products'], 0, 3) as $index => $product): ?>
                        <a href="<?= cms_escape($product['href']) ?>" class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-shadow fade-up fade-up-delay-<?= $index + 1 ?>">
                            <img src="<?= cms_escape($product['image_path'] ?: $page['category']['image_path'] ?: '/images/content/dental-lab-1.jpg') ?>" alt="<?= cms_escape($product['name']) ?>" class="w-full h-56 object-cover">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-navy mb-2"><?= cms_escape($product['name']) ?></h3>
                                <span class="text-primary font-semibold text-sm">View Product</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</main>
