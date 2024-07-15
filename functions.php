<?php

use Timber\Timber;

/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 */

// Load Composer dependencies.
require_once __DIR__ . '/vendor/autoload.php';


// load classes
// require_once get_template_directory() . '/lib/classes/ACFContext.php';

// Set up Vite
require_once get_template_directory() . '/lib/functions/lib-vite.php';
new WPVite(isChild: false);

// Get Utility functions
require_once get_template_directory() . '/lib/functions/lib-utilities.php';

// Get ACF and ACF Blocks defaults
require_once get_template_directory() . '/lib/functions/lib-acf.php';


// 
require_once get_template_directory() . '/lib/functions/lib-flexible-content.php';

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 */

require_once get_template_directory() . '/lib/timber/lib-timber.php';
require_once get_template_directory() . '/lib/timber/lib-timber-filters.php';

Timber::init();

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = ['templates', 'views'];

new StarterTimber();

