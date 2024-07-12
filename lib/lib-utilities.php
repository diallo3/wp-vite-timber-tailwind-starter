<?php

/**
 * Retrieves the SVG code from a given file location.
 *
 * @param string|null $file_location The path to the SVG file. Default is null.
 *
 * @return string|false The SVG code as a string if the file exists, false otherwise.
 */
function svg_code($file_location = null) {
	if (WP_DEBUG) {
		$opts = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
			)
		);
		$context = stream_context_create($opts);
		libxml_set_streams_context($context);
	}
	$return = false;
	if ($file_location) {
		$iconfile = new DOMDocument();
		$iconfile->load($file_location);
		$html = $iconfile->saveHTML($iconfile->getElementsByTagName('svg')[0]);
		$return = $html;
	}
	return $return;
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
	if (get_theme_mod('custom_logo')) {
		// get the url of the selected logo image
		$logo_url = wp_get_attachment_url(get_theme_mod('custom_logo'));

		// make it a local path so we don't have to make an http requests
		$logo_url = ltrim(parse_url($logo_url, PHP_URL_PATH), '/');

		if (str_contains($logo_url, '.svg')) {
			$logo = svg_code($logo_url);
		} else {
			$logo = '<img src="' . $logo_url . '" alt="site logo" />';
		}
	}
	return $logo;
}
