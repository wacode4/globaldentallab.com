<?php

$content = $module['content'];
$settings = $module['settings'];
$items = $content['items'] ?? [];
$columns = max(1, min(4, (int) ($settings['columns'] ?? 3)));
$gridClass = match ($columns) {
    1 => 'grid gap-6',
    2 => 'grid gap-6 md:grid-cols-2',
    4 => 'grid gap-6 md:grid-cols-2 xl:grid-cols-4',
    default => 'grid gap-6 md:grid-cols-2 xl:grid-cols-3',
};
?>
<section class="<?= cms_escape($settings['section_class'] ?? 'bg-slate-50 py-20') ?>">
    <div class="mx-auto max-w-7xl px-6">
        <?php if ($module['title'] || $module['kicker'] || $module['subtitle']): ?>
            <div class="mb-12 max-w-3xl">
                <?php if ($module['kicker']): ?><p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-primary"><?= cms_escape($module['kicker']) ?></p><?php endif; ?>
                <?php if ($module['title']): ?><h2 class="text-4xl font-extrabold text-navy"><?= cms_escape($module['title']) ?></h2><?php endif; ?>
                <?php if ($module['subtitle']): ?><p class="mt-4 text-lg text-slate-600"><?= cms_escape($module['subtitle']) ?></p><?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="<?= $gridClass ?>">
            <?php foreach ($items as $item): ?>
                <article class="rounded-3xl border border-slate-200 bg-white p-7 shadow-sm">
                    <?php if (!empty($item['eyebrow'])): ?><p class="mb-3 text-xs font-bold uppercase tracking-[0.24em] text-primary"><?= cms_escape($item['eyebrow']) ?></p><?php endif; ?>
                    <?php if (!empty($item['title'])): ?><h3 class="text-2xl font-bold text-navy"><?= cms_escape($item['title']) ?></h3><?php endif; ?>
                    <?php if (!empty($item['text'])): ?><p class="mt-4 text-sm leading-7 text-slate-600"><?= cms_escape($item['text']) ?></p><?php endif; ?>
                    <?php if (!empty($item['bullets']) && is_array($item['bullets'])): ?>
                        <ul class="mt-5 space-y-3 text-sm text-slate-600">
                            <?php foreach ($item['bullets'] as $bullet): ?>
                                <li class="flex gap-3">
                                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-primary"></span>
                                    <span><?= cms_escape((string) $bullet) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if (!empty($item['meta'])): ?><p class="mt-5 text-xs font-semibold uppercase tracking-[0.22em] text-slate-400"><?= cms_escape($item['meta']) ?></p><?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
