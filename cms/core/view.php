<?php

declare(strict_types=1);

function cms_view_path(string $template): string
{
    return CMS_BASE_PATH . '/views/' . $template . '.php';
}

function cms_render_view(string $template, array $data = [], ?string $layout = null): string
{
    $templatePath = cms_view_path($template);

    if (!file_exists($templatePath)) {
        throw new RuntimeException('View not found: ' . $template);
    }

    extract($data, EXTR_SKIP);

    ob_start();
    require $templatePath;
    $content = (string) ob_get_clean();

    if ($layout === null) {
        return $content;
    }

    $layoutPath = cms_view_path('layouts/' . $layout);
    if (!file_exists($layoutPath)) {
        throw new RuntimeException('Layout not found: ' . $layout);
    }

    ob_start();
    require $layoutPath;
    return (string) ob_get_clean();
}

function cms_render_module(array $module): string
{
    $template = 'modules/' . $module['module_type'];

    if (!file_exists(cms_view_path($template))) {
        $template = 'modules/rich_text';
    }

    return cms_render_view($template, ['module' => $module], null);
}
