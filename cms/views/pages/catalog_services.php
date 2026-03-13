<main>
    <section class="bg-gray-50 border-b border-gray-200 sticky top-[80px] z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex overflow-x-auto gap-8 py-4 scrollbar-hide text-sm">
                <?php foreach ($page['catalog'] as $category): ?>
                    <a href="#<?= cms_escape($category['slug']) ?>" class="text-navy-light hover:text-primary whitespace-nowrap font-medium transition-colors duration-200">
                        <?= cms_escape($category['nav_label'] ?: $category['name']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </section>

    <?php foreach ($page['catalog'] as $index => $category): ?>
        <?php $isMuted = $index % 2 === 1; ?>
        <section id="<?= cms_escape($category['slug']) ?>" class="<?= $isMuted ? 'bg-gray-50' : 'bg-white' ?> py-20 scroll-mt-36 fade-up">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="<?= $isMuted ? 'order-2 lg:order-1' : '' ?>">
                        <div class="inline-flex items-center gap-2 <?= $isMuted ? 'bg-accent/10 text-accent' : 'bg-primary/10 text-primary' ?> px-4 py-2 rounded-full text-sm font-medium mb-6">
                            Product Category
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-navy mb-6"><?= cms_escape($category['name']) ?></h2>
                        <p class="text-lg text-gray-600 mb-8"><?= cms_escape($category['summary']) ?></p>

                        <?php if (!empty($category['content_html'])): ?>
                            <div class="catalog-rich mb-8"><?= $category['content_html'] ?></div>
                        <?php endif; ?>

                        <?php if (!empty($category['products'])): ?>
                            <div class="grid sm:grid-cols-2 gap-4 mb-8">
                                <?php foreach ($category['products'] as $product): ?>
                                    <a href="<?= cms_escape($product['href']) ?>" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                                        <?php if (!empty($product['badge'])): ?>
                                            <span class="inline-block bg-primary/10 text-primary text-xs font-semibold px-3 py-1 rounded-full mb-3"><?= cms_escape($product['badge']) ?></span>
                                        <?php endif; ?>
                                        <h3 class="text-lg font-bold text-navy mb-2"><?= cms_escape($product['name']) ?></h3>
                                        <p class="text-sm text-gray-600"><?= cms_escape($product['short_description']) ?></p>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="<?= cms_escape($category['href'] ?: cms_localized_href('/services', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center px-6 py-3 rounded-lg font-bold text-lg">
                                VIEW CATEGORY
                            </a>
                            <a href="<?= cms_escape(cms_localized_href('/send-a-case', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center border-2 border-primary text-primary px-6 py-3 rounded-lg font-bold text-lg">
                                SEND A CASE
                            </a>
                        </div>
                    </div>
                    <div class="<?= $isMuted ? 'order-1 lg:order-2' : '' ?> relative">
                        <img src="<?= cms_escape($category['image_path'] ?: '/images/content/dental-lab-1.jpg') ?>" alt="<?= cms_escape($category['name']) ?>" class="rounded-2xl shadow-xl w-full">
                    </div>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

    <section class="py-20 bg-primary">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center fade-up">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Need Help Picking The Right Submission Route?</h2>
            <p class="text-xl text-white/90 mb-8">Use the downloads page for RX forms, the send-a-case page for platform instructions, or contact the team for edge-case workflows.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= cms_escape(cms_localized_href('/send-a-case', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center bg-white text-navy px-8 py-4 rounded-lg font-bold text-lg">SEND A CASE</a>
                <a href="<?= cms_escape(cms_localized_href('/downloads', $page['language']['code'])) ?>" class="btn-primary inline-flex items-center justify-center border-2 border-white text-white px-8 py-4 rounded-lg font-bold text-lg">DOWNLOAD FORMS</a>
            </div>
        </div>
    </section>
</main>
