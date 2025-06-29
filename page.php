<?php

if (is_admin()) {
    die('ADMIN CONTEXT FROM PAGE.PHP');
}

/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/templates/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

use Timber\Timber;

// Define the ACF Flexible Content field name
$field_name = 'page_content';

$context = Timber::context();

$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

// Only build context and render on the frontend
if (!is_admin() && !wp_doing_ajax()) {
    $context['flexible_content'] = render_acf_flexible_content($timber_post->ID);

    $templates = [
        'pages/page-' . $timber_post->post_name . '/page-' . $timber_post->post_name . '.twig',
        'pages/page/page.twig'
    ];
    Timber::render($templates, $context);
}

