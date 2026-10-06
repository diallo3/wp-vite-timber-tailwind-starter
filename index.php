<?php

/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

use Timber\Timber;

$context          = Timber::context();
$context['posts'] = Timber::get_posts();

// Blog page: header/related components from its "Post Archive Options".
$posts_page = (int) get_option('page_for_posts');
if (is_home() && $posts_page) {
	$context['title'] = get_the_title($posts_page);
	$context['archive_fields'] = function_exists('get_fields') ? get_fields($posts_page) : [];
}
$templates        = ['pages/index/index.twig'];
Timber::render($templates, $context);
