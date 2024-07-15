<?php

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

// Ensure the ACFContext class is available
require_once get_template_directory() . '/lib/classes/ACFContext.php';

// Define the ACF Flexible Content field name
$field_name = 'page_content';

$context = Timber::context();

$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

// Collect flexible content data
$context['flexible_content'] = render_acf_flexible_content($timber_post->ID);

$templates = [
	'pages/page-' . $timber_post->post_name . '/page-' . $timber_post->post_name . '.twig',
	'pages/page/page.twig'
];
Timber::render($templates, $context);

