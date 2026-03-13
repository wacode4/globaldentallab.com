<main>
    <div class="bg-gray-50 py-4 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="breadcrumb flex items-center gap-2 text-sm text-gray-600">
                <a href="<?= cms_escape(cms_localized_href('/', $page['language']['code'])) ?>" class="hover:text-primary">Home</a>
                <span>/</span>
                <a href="<?= cms_escape(cms_localized_href('/services', $page['language']['code'])) ?>" class="hover:text-primary">Products</a>
                <span>/</span>
                <span class="text-navy font-medium"><?= cms_escape($page['category']['name']) ?></span>
            </nav>
        </div>
    </div>

    <section class="py-20 fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-[1fr_0.95fr] gap-12 items-center">
                <div>
                    <p class="text-primary font-medium mb-3 tracking-wide">CATEGORY OVERVIEW</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-navy mb-6"><?= cms_escape($page['category']['name']) ?></h2>
                    <p class="text-lg text-gray-600 mb-6"><?= cms_escape($page['category']['summary']) ?></p>
                    <?php if (!empty($page['category']['content_html'])): ?>
                        <div class="catalog-rich mb-8"><?= $page['category']['content_html'] ?></div>
                    <?php endif; ?>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="<?= cms_escape(cms_localized_href('/send-a-case', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center px-6 py-4">SEND A CASE</a>
                        <a href="<?= cms_escape(cms_localized_href('/downloads', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center border-2 border-navy text-navy px-6 py-4 bg-white">PREPARATION GUIDE</a>
                    </div>
                </div>
                <div class="rounded-3xl overflow-hidden shadow-xl">
                    <img src="<?= cms_escape($page['category']['image_path'] ?: '/images/content/dental-lab-1.jpg') ?>" alt="<?= cms_escape($page['category']['name']) ?>" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($page['category']['products'])): ?>
        <section class="py-20 bg-gray-50 fade-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <p class="text-primary font-medium mb-3 tracking-wide">PRODUCTS IN THIS CATEGORY</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-navy">Choose the product route that fits the case</h2>
                </div>
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-8">
                    <?php foreach ($page['category']['products'] as $product): ?>
                        <a href="<?= cms_escape($product['href']) ?>" class="rounded-2xl overflow-hidden border border-gray-200 bg-white shadow-sm hover:shadow-lg transition-shadow">
                            <img src="<?= cms_escape($product['image_path'] ?: $page['category']['image_path'] ?: '/images/content/dental-lab-1.jpg') ?>" alt="<?= cms_escape($product['name']) ?>" class="w-full h-60 object-cover">
                            <div class="p-6">
                                <?php if (!empty($product['badge'])): ?>
                                    <span class="inline-block bg-accent/10 text-accent text-xs font-semibold px-3 py-1 rounded-full mb-3"><?= cms_escape($product['badge']) ?></span>
                                <?php endif; ?>
                                <h3 class="text-xl font-bold text-navy mb-2"><?= cms_escape($product['name']) ?></h3>
                                <p class="text-gray-600 mb-4"><?= cms_escape($product['short_description']) ?></p>
                                <span class="text-primary font-semibold">View Product</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="py-20 bg-navy text-white fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-[0.95fr_1.05fr] gap-12 items-center">
                <div>
                    <p class="text-primary/80 font-medium mb-3 tracking-wide">NEXT STEP</p>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">Move from category review into the right case workflow</h2>
                    <p class="text-white/75 mb-8">If the case direction is already clear, submit directly. If the material path is still open, include scans, photos, and goals so the team can help narrow the route before production.</p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="<?= cms_escape(cms_localized_href('/send-a-case', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center px-6 py-4">SEND A CASE</a>
                        <a href="<?= cms_escape(cms_localized_href('/contact', $page['language']['code'])) ?>" class="inline-flex items-center justify-center px-6 py-4 border border-white/30 text-white font-semibold hover:bg-white/10 transition-colors">ASK ABOUT MATERIAL CHOICE</a>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                        <h3 class="font-bold mb-3">Best companion page</h3>
                        <p class="text-sm text-white/75"><a href="<?= cms_escape(cms_localized_href('/downloads', $page['language']['code'])) ?>" class="text-white underline">Downloads</a> helps with prep guidance and RX forms before submission.</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 border border-white/10 p-6">
                        <h3 class="font-bold mb-3">Need pre-case input?</h3>
                        <p class="text-sm text-white/75">Use the contact page when the case still needs planning help before final submission.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
