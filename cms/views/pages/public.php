<main>
    <?php foreach ($page['modules'] as $module): ?>
        <?= cms_render_module($module) ?>
    <?php endforeach; ?>
</main>
