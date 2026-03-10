<?php $settings = $module['settings']; ?>
<section class="<?= cms_escape($settings['section_class'] ?? 'py-20 bg-white') ?>">
    <div class="mx-auto max-w-5xl px-6">
        <?php if ($module['kicker']): ?>
            <p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-primary"><?= cms_escape($module['kicker']) ?></p>
        <?php endif; ?>
        <?php if ($module['title']): ?>
            <h2 class="mb-4 text-4xl font-extrabold text-navy"><?= cms_escape($module['title']) ?></h2>
        <?php endif; ?>
        <?php if ($module['subtitle']): ?>
            <p class="mb-8 text-lg text-slate-600"><?= cms_escape($module['subtitle']) ?></p>
        <?php endif; ?>
        <div class="prose prose-slate max-w-none"><?= $module['content_html'] ?></div>
    </div>
</section>
