<?php

use Timber\Timber;

function render_acf_flexible_content($post_id) {
    // Get the flexible content field
    $flexible_content = get_field('page_content', $post_id);

    $content = [];

    if ($flexible_content) {
        foreach ($flexible_content as $acf_fc_layout) {
            $component_name = $acf_fc_layout['acf_fc_layout'];
            $content[] = [
                'component_name' => $component_name,
                'component_data' => $acf_fc_layout,
            ];
        }
    }

    return $content;
}
