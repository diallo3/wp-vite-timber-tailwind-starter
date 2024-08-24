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
    // Define the path to your thumbnails
    $thumbnail_path = get_template_directory_uri() . '/lib/acf/images/layout-thumbnails/';
    
    // Define the default thumbnail URL
    $default_thumbnail_url = $thumbnail_path . 'default-thumbnail.png';
    
    // Construct the thumbnail file name based on the layout name
    $thumbnail_name = $layout['name'] . '.png';
    
    // Set the thumbnail URL
    $thumbnail_url = $thumbnail_path . $thumbnail_name;
    
    // Check if the thumbnail file exists
    if (file_exists(get_template_directory() . $thumbnail_path . $thumbnail_name)) {
        return $thumbnail_url;
    }
    
    // Return the default thumbnail if the file does not exist
    return $default_thumbnail_url;
}

/**
 * @param $template layout template
 * Layout Render Template.
 */

 add_filter('acfe/flexible/render/template', 'render_acfe_flexible_template', 50, 4);
 function render_acfe_flexible_template($template, $field, $layout, $is_preview) {
     // Define the context object
 
     $context = Timber::context();
     // Check if preview mode
     if ($is_preview) {
        wp_dequeue_style( 'global-styles' );
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
        // Get the component fields
        $page_content = get_field('page_content')[0];
        $context['component'] = $page_content;

        // Set preview template path
        $template = get_stylesheet_directory() . '/templates/app/components/content/' . $layout['name'] . '/index.twig';

         // Add preview context
         $context['is_preview'] = true;
         $context['preview_data'] = 'Additional data for preview';
     } else {
         // Set normal template path
        $template = get_stylesheet_directory() . '/templates/app/layouts/flexible-content.twig';
        
         // Add regular context
        $context['is_preview'] = false;
        $context['regular_data'] = 'Additional data for regular view';
     }
 
     // Render the template with Timber
     Timber::render($template, $context);
 
     // Return false to prevent default rendering
     return false;
 }

/**
 * @param $layouts layout
 * Layout Render Template Style.
 */

add_action('acfe/flexible/enqueue/name=page_content', 'render_acf_flexible_enqueue', 20, 10);
function render_acf_flexible_enqueue($field, $is_preview){
    if ($is_preview) {
        // Get the main CSS file from the Vite manifest
        $css_file = get_main_vite_css_file();
        
        if ($css_file) {
            wp_enqueue_style('page_content', $css_file, [], null);
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





