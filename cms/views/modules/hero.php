<?php $content = $module['content']; $settings = $module['settings']; ?>
<section class="bg-navy py-24 text-white">
    <div class="mx-auto max-w-7xl px-6">
        <?php if (!empty($content['label'])): ?>
            <p class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-accent"><?= cms_escape($content['label']) ?></p>
        <?php endif; ?>
        <h1 class="max-w-4xl text-5xl font-extrabold leading-tight"><?= $content['title_html'] ?? cms_escape($module['title']) ?></h1>
        <?php if (!empty($content['subtitle_html']) || !empty($module['subtitle'])): ?>
            <div class="mt-6 max-w-3xl text-lg text-slate-200"><?= $content['subtitle_html'] ?? nl2br(cms_escape($module['subtitle'])) ?></div>
        <?php endif; ?>
        <?php if (!empty($content['buttons']) && is_array($content['buttons'])): ?>
            <div class="mt-8 flex flex-wrap gap-4">
                <?php foreach ($content['buttons'] as $button): ?>
                    <a class="rounded-xl <?= ($button['style'] ?? 'primary') === 'secondary' ? 'border border-white/30 bg-white/10 text-white' : 'bg-accent text-navy' ?> px-6 py-3 font-bold" href="<?= cms_escape($button['href'] ?? '#') ?>">
                        <?= cms_escape($button['text'] ?? 'Open') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
