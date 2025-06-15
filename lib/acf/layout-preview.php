<?php
use Timber\Timber;

global $args;

// Safely get the layout name
$layout_name = null;
if (function_exists('get_row_layout')) {
    $layout_name = get_row_layout();
} else {
    $layout_name = 'section_header_complex';
}

$context = Timber::context();
$context['component'] = [
    'content' => get_sub_field('content')
];
$context['is_preview'] = true;

$template = get_stylesheet_directory() . '/templates/app/components/content/' . $layout_name . '/index.twig';

Timber::render($template, $context);
