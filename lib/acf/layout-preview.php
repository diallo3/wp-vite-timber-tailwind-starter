<?php
/**
 * ACFE Dynamic Render preview for `page_content` layouts.
 * Loaded via the `acfe/flexible/render/template` filter in lib-flexible-content.php,
 * inside ACF's have_rows() loop, so get_row_layout()/get_sub_field() refer to the row being previewed.
 */

use Timber\Timber;

$context = Timber::context();
$context['name'] = get_row_layout();
$context['component'] = ['content' => get_sub_field('content')];
$context['index'] = get_row_index();
$context['is_preview'] = true;

Timber::render('@Layouts/_flexible-layout.twig', $context);
