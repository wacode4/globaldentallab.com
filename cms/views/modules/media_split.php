<?php

$content = $module['content'];
$settings = $module['settings'];
$imagePosition = ($settings['image_position'] ?? 'right') === 'left' ? 'left' : 'right';
$wrapperClass = $imagePosition === 'left'
    ? 'mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-[0.95fr_1.05fr]'
    : 'mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-[1.05fr_0.95fr]';
?>
<section class="<?= cms_escape($settings['section_class'] ?? 'bg-white py-20') ?>">
    <div class="<?= $wrapperClass ?>">
        <?php if ($imagePosition === 'left'): ?>
            <div>
                <?php if (!empty($content['image'])): ?><img class="h-full w-full rounded-[2rem] object-cover shadow-lg" src="<?= cms_escape($content['image']) ?>" alt="<?= cms_escape($content['image_alt'] ?? ($module['title'] ?? '')) ?>"><?php endif; ?>
            </div>
        <?php endif; ?>
        <div>
            <?php if ($module['kicker']): ?><p class="mb-3 text-xs font-bold uppercase tracking-[0.3em] text-primary"><?= cms_escape($module['kicker']) ?></p><?php endif; ?>
            <?php if ($module['title']): ?><h2 class="text-4xl font-extrabold text-navy"><?= cms_escape($module['title']) ?></h2><?php endif; ?>
            <?php if ($module['subtitle']): ?><p class="mt-4 text-lg text-slate-600"><?= cms_escape($module['subtitle']) ?></p><?php endif; ?>
            <?php if ($module['content_html']): ?><div class="prose prose-slate mt-8 max-w-none"><?= $module['content_html'] ?></div><?php endif; ?>
            <?php if (!empty($content['buttons']) && is_array($content['buttons'])): ?>
                <div class="mt-8 flex flex-wrap gap-4">
                    <?php foreach ($content['buttons'] as $button): ?>
                        <a class="rounded-xl <?= ($button['style'] ?? 'primary') === 'secondary' ? 'border border-slate-300 bg-white text-navy' : 'bg-primary text-white' ?> px-6 py-3 font-bold" href="<?= cms_escape($button['href'] ?? '#') ?>">
                            <?= cms_escape($button['text'] ?? 'Open') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if ($imagePosition === 'right'): ?>
            <div>
                <?php if (!empty($content['image'])): ?><img class="h-full w-full rounded-[2rem] object-cover shadow-lg" src="<?= cms_escape($content['image']) ?>" alt="<?= cms_escape($content['image_alt'] ?? ($module['title'] ?? '')) ?>"><?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
