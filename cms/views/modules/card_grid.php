<?php $content = $module['content']; $cards = $content['cards'] ?? []; ?>
<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mb-12 text-center">
            <?php if ($module['kicker']): ?><p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-primary"><?= cms_escape($module['kicker']) ?></p><?php endif; ?>
            <?php if ($module['title']): ?><h2 class="text-4xl font-extrabold text-navy"><?= cms_escape($module['title']) ?></h2><?php endif; ?>
            <?php if ($module['subtitle']): ?><p class="mx-auto mt-4 max-w-3xl text-lg text-slate-600"><?= cms_escape($module['subtitle']) ?></p><?php endif; ?>
        </div>
        <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-4">
            <?php foreach ($cards as $card): ?>
                <div class="overflow-hidden rounded-3xl bg-white shadow-sm">
                    <?php if (!empty($card['image'])): ?><img class="h-56 w-full object-cover" src="<?= cms_escape($card['image']) ?>" alt="<?= cms_escape($card['title'] ?? '') ?>"><?php endif; ?>
                    <div class="p-6">
                        <h3 class="mb-3 text-xl font-bold text-navy"><?= cms_escape($card['title'] ?? '') ?></h3>
                        <p class="text-sm text-slate-600"><?= cms_escape($card['text'] ?? '') ?></p>
                        <?php if (!empty($card['href'])): ?><a class="mt-4 inline-block font-semibold text-primary" href="<?= cms_escape($card['href']) ?>"><?= cms_escape($card['cta'] ?? 'Open') ?></a><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
