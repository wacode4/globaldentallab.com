<?php $content = $module['content']; ?>
<section class="bg-navy py-20 text-white">
    <div class="mx-auto max-w-4xl px-6 text-center">
        <?php if ($module['kicker']): ?><p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-accent"><?= cms_escape($module['kicker']) ?></p><?php endif; ?>
        <?php if ($module['title']): ?><h2 class="text-4xl font-extrabold"><?= cms_escape($module['title']) ?></h2><?php endif; ?>
        <?php if ($module['subtitle']): ?><p class="mt-4 text-lg text-slate-200"><?= cms_escape($module['subtitle']) ?></p><?php endif; ?>
        <?php if (!empty($content['buttons']) && is_array($content['buttons'])): ?>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <?php foreach ($content['buttons'] as $button): ?>
                    <a class="rounded-xl <?= ($button['style'] ?? 'primary') === 'secondary' ? 'border border-white/30 bg-white/10 text-white' : 'bg-accent text-navy' ?> px-6 py-3 font-bold" href="<?= cms_escape(cms_localized_href($button['href'] ?? '#', $module['language_code'] ?? 'en')) ?>">
                        <?= cms_escape($button['text'] ?? 'Open') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
