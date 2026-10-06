<?php

/**
 * Retrieves the SVG code from a given file location.
 *
 * @param string|null $file_location Absolute filesystem path to the SVG file. Default is null.
 *
 * @return string|false The SVG code as a string if the file exists, false otherwise.
 */
function svg_code($file_location = null) {
	if (!$file_location || !is_readable($file_location)) {
		return false;
	}

	$iconfile = new DOMDocument();
	if (!@$iconfile->load($file_location)) {
		return false;
	}

	$svg = $iconfile->getElementsByTagName('svg')->item(0);
	return $svg ? $iconfile->saveHTML($svg) : false;
}

/**
 * 
 * @param string Remove Comments from Admin Bar
 *
 */
add_action( 'admin_menu', 'remove_comments' );
function remove_comments(){
    remove_menu_page( 'edit-comments.php' );
}


/**
 * Retrieves the site logo as either SVG code or an image, depending on the file type.
 *
 * @return string The site logo represented as SVG code or an HTML image tag.
 */
function site_logo() {
	$logo = '<svg class="inline-block transition size-full hi-mini hi-cube-transparent" xmlns="http://www.w3.org/2000/svg" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M9.638 1.093a.75.75 0 01.724 0l2 1.104a.75.75 0 11-.724 1.313L10 2.607l-1.638.903a.75.75 0 11-.724-1.313l2-1.104zM5.403 4.287a.75.75 0 01-.295 1.019l-.805.444.805.444a.75.75 0 01-.724 1.314L3.5 7.02v.73a.75.75 0 01-1.5 0v-2a.75.75 0 01.388-.657l1.996-1.1a.75.75 0 011.019.294zm9.194 0a.75.75 0 011.02-.295l1.995 1.101A.75.75 0 0118 5.75v2a.75.75 0 01-1.5 0v-.73l-.884.488a.75.75 0 11-.724-1.314l.806-.444-.806-.444a.75.75 0 01-.295-1.02zM7.343 8.284a.75.75 0 011.02-.294L10 8.893l1.638-.903a.75.75 0 11.724 1.313l-1.612.89v1.557a.75.75 0 01-1.5 0v-1.557l-1.612-.89a.75.75 0 01-.295-1.019zM2.75 11.5a.75.75 0 01.75.75v1.557l1.608.887a.75.75 0 01-.724 1.314l-1.996-1.101A.75.75 0 012 14.25v-2a.75.75 0 01.75-.75zm14.5 0a.75.75 0 01.75.75v2a.75.75 0 01-.388.657l-1.996 1.1a.75.75 0 11-.724-1.313l1.608-.887V12.25a.75.75 0 01.75-.75zm-7.25 4a.75.75 0 01.75.75v.73l.888-.49a.75.75 0 01.724 1.313l-2 1.104a.75.75 0 01-.724 0l-2-1.104a.75.75 0 11.724-1.313l.888.49v-.73a.75.75 0 01.75-.75z" clip-rule="evenodd"/>
    </svg>';
	$logo_id = get_theme_mod('custom_logo');
	if ($logo_id) {
		$file = get_attached_file($logo_id);

		if ($file && str_ends_with(strtolower($file), '.svg')) {
			$logo = svg_code($file) ?: $logo;
		} else {
			$logo = wp_get_attachment_image($logo_id, 'full', false, [
				'alt' => get_bloginfo('name'),
				'class' => 'size-full object-contain',
			]) ?: $logo;
		}
	}
	return $logo;
}

add_filter('register_post_type_args', 'post_type_archives', 10, 2);
function post_type_archives($args, $post_type) {
    $post_types = function_exists('get_field') ? get_field('has_post_type_archive', 'option') : [];
    $post_types = is_array($post_types) ? $post_types : [];

    if ($post_type && in_array($post_type, $post_types, true)) {
        // Enable ACF extended admin archive
        $args['acfe_admin_archive'] = true;
    }
    
    return $args;
}

/**
 * Output tracking codes from Theme Options (Tracking tab).
 *
 * Each row's `in_header` flag decides between wp_head and wp_footer.
 * Scripts are printed as entered; the options page is admin-only.
 *
 * @param bool $in_header Whether to print header (true) or footer (false) codes.
 */
function theme_print_tracking_codes($in_header) {
    if (!function_exists('get_field') || is_admin()) {
        return;
    }

    $tracking = get_field('tracking', 'option');
    $codes = is_array($tracking) && !empty($tracking['tracking_codes']) ? $tracking['tracking_codes'] : [];

    foreach ($codes as $code) {
        if ((bool) ($code['in_header'] ?? false) === $in_header && !empty($code['script'])) {
            echo "\n" . $code['script'] . "\n";
        }
    }
}
add_action('wp_head', fn() => theme_print_tracking_codes(true), 99);
add_action('wp_footer', fn() => theme_print_tracking_codes(false), 99);

/**
 * Enqueue the admin stylesheet (src/admin.css).
 */
function custom_admin_styles() {
    WPVite::$instance?->enqueueStyle('src/admin.css', 'theme-admin');
}
add_action('admin_enqueue_scripts', 'custom_admin_styles');


/**
 * 
 * @param string Enqueue Iconify script for admin
 *
 */

function enqueue_iconify_for_admin() {
    
    wp_enqueue_script(
        'iconify', 
        'https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js', 
        [], 
        null, 
        true // Load in footer
    );
}
add_action('admin_enqueue_scripts', 'enqueue_iconify_for_admin');