<?php
use Timber\Site;

class StarterTimber extends Site {
	/** Add timber support. */
	public function __construct() {
		add_action('after_setup_theme', array($this, 'theme_supports'));
		add_filter('timber/context', array($this, 'add_to_context'));
		add_filter('timber/twig', array($this, 'add_to_twig'));
		add_action('wp_enqueue_scripts', array($this, 'dequeue_block_styles'), 100);
		parent::__construct();
	}

	// Post types and taxonomies are registered in ACF (acf-json/post_type_*.json).

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context($context) {
		$context['site_logo'] = site_logo();
		$context['privacy_policy_url'] = get_privacy_policy_url();

		// Menus
		$menu = [];
		if (has_nav_menu('header_nav')) {
			$menu['header_nav'] = Timber::get_menu('header_nav');
		}
		if (has_nav_menu('header_buttons')) {
			$menu['header_buttons'] = Timber::get_menu('header_buttons');
		}
		if (has_nav_menu('footer_social')) {
			$menu['footer_social'] = Timber::get_menu('footer_social');
		}
		if (has_nav_menu('footer_nav')) {
			$menu['footer_nav'] = Timber::get_menu('footer_nav');
		}
		if (has_nav_menu('footer_utility')) {
			$menu['footer_utility'] = Timber::get_menu('footer_utility');
		}

		$context['menu'] = $menu;
		$context['site']  = $this;
        $context['options'] = get_fields('option');
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support('title-tag');

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support('post-thumbnails');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Custom Logo
		 *
		 * See: https://developer.wordpress.org/themes/functionality/custom-logo/
		 */
		add_theme_support('custom-logo');

		/*
		 * Enable support for specific menu locations
		 *
		 * See: https://developer.wordpress.org/themes/functionality/navigation-menus/
		 */

		add_theme_support('menus');
		register_nav_menus(
			[
				'header_nav' => esc_html__('Header Nav', 'StarterTimber'),
				'header_buttons' => esc_html__('Header Buttons', 'StarterTimber'),
				'footer_nav' => esc_html__('Footer Nav', 'StarterTimber'),
				'footer_social' => esc_html__('Social Links', 'StarterTimber'),
				'footer_utility' => esc_html__('Footer Utility', 'StarterTimber'),
			]
		);

		add_theme_support('responsive-embeds');
	}

	/** Classic Editor site: drop the block-editor CSS WordPress enqueues on the frontend. */
	public function dequeue_block_styles() {
		wp_dequeue_style('wp-block-library');
		wp_dequeue_style('wp-block-library-theme');
		wp_dequeue_style('classic-theme-styles');
		wp_dequeue_style('global-styles');
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param \Twig\Environment $twig
	 */
	public function add_to_twig($twig) {
		return $twig;
	}
}