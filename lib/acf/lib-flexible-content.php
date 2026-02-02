<?php

use Timber\Timber;

/**
 * ACF Flexible Content
 */
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

/**
 *  
 *  @param $layouts
 *  Function to register filters dynamically based on layouts
 * 
 */ 
add_filter('acfe/flexible/thumbnail', 'render_acf_flexible_thumbnail', 10, 4);

function render_acf_flexible_thumbnail($thumbnail, $field, $layout) {
    // Define the path to your thumbnails for the URL
    $thumbnail_url_path = get_template_directory_uri() . '/lib/acf/images/layout-thumbnails/';
    
    // Define the server path to your thumbnails for file_exists check
    $thumbnail_file_path = get_template_directory() . '/lib/acf/images/layout-thumbnails/';
    
    // Define the default thumbnail URL
    $default_thumbnail_url = $thumbnail_url_path . 'default-thumbnail.png';
    
    // Construct the thumbnail file name based on the layout name
    $thumbnail_name = $layout['name'] . '.svg';
    
    // Construct both the URL and the file path
    $thumbnail_url = $thumbnail_url_path . $thumbnail_name;
    $thumbnail_file = $thumbnail_file_path . $thumbnail_name;
    
    // Check if the thumbnail file exists on the server
    if (file_exists($thumbnail_file)) {
        return $thumbnail_url;
    }
    
    // Return the default thumbnail if the file does not exist
    return $default_thumbnail_url;
}

/**
 * @param $template layout template
 * Layout Render Template.
 */

add_filter('acfe/flexible/render/template', function($template, $field, $layout, $is_preview) {
    if ($is_preview) {
        // Return the path to your PHP preview file
        return get_stylesheet_directory() . '/lib/acf/layout-preview.php';
    }
    // For frontend, use your normal logic
    // return get_stylesheet_directory() . '/templates/app/layouts/flexible-content.twig';
}, 50, 4);

/**
 * @param $layouts layout
 * Layout Render Template Style.
 */

add_action('acfe/flexible/enqueue/name=page_content', 'render_acf_flexible_enqueue', 20, 10);
function render_acf_flexible_enqueue($field, $is_preview){
    if ($is_preview) {
        // Enqueue Google Fonts for ACF preview
        wp_enqueue_style(
            'google-fonts-acf-preview',
            'https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap',
            array(),
            null
        );

        // Get the main CSS file from the Vite manifest
        $css_file = get_main_vite_css_file();

        if ($css_file) {
            wp_enqueue_style('page_content', $css_file, ['google-fonts-acf-preview'], null);
        }
    }
}

// Function to get the main CSS file from the Vite manifest
function get_main_vite_css_file() {
    $manifestPath = get_stylesheet_directory() . '/dist/.vite/manifest.json';
    $manifestUri = get_stylesheet_directory_uri() . '/dist/';

    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        // Assuming 'app.css' is the key for your main CSS file in the manifest
        foreach ($manifest as $key => $entry) {
            if (isset($entry['css'])) {
                return $manifestUri . $entry['css'][0];
            }
        }
    }

    return null;
}