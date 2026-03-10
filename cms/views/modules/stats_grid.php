<?php $content = $module['content']; $items = $content['items'] ?? []; ?>
<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-6">
        <?php if ($module['title']): ?>
            <div class="mb-10 text-center">
                <?php if ($module['kicker']): ?><p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-primary"><?= cms_escape($module['kicker']) ?></p><?php endif; ?>
                <h2 class="text-4xl font-extrabold text-navy"><?= cms_escape($module['title']) ?></h2>
                <?php if ($module['subtitle']): ?><p class="mx-auto mt-4 max-w-3xl text-lg text-slate-600"><?= cms_escape($module['subtitle']) ?></p><?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="grid gap-6 md:grid-cols-3">
            <?php foreach ($items as $item): ?>
                <div class="rounded-3xl bg-slate-50 p-8 text-center shadow-sm">
                    <p class="text-4xl font-extrabold text-primary"><?= cms_escape($item['value'] ?? '') ?></p>
                    <p class="mt-3 text-sm uppercase tracking-[0.2em] text-slate-500"><?= cms_escape($item['label'] ?? '') ?></p>
                    <?php if (!empty($item['description'])): ?><p class="mt-3 text-sm text-slate-600"><?= cms_escape($item['description']) ?></p><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
