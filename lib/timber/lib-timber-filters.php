<?php

/**
 *  Creates cleaner paths for files
 * 
 * @return void
 */
add_filter('timber/loader/loader', function($loader) {
    $loader->addPath(get_template_directory() . '/templates/app/components/', 'Components');
    $loader->addPath(get_template_directory() . '/templates/app/components/content/', 'Content');
    $loader->addPath(get_template_directory() . '/templates/app/layouts/', 'Layouts');
    $loader->addPath(get_template_directory() . '/templates/app/globals/', 'Globals');
    $loader->addPath(get_template_directory() . '/templates/app/elements/', 'Elements');
    $loader->addPath(get_template_directory() . '/templates/pages', 'Pages');
    return $loader;
});

/**
 * 
*/
add_filter('timber/twig', function ($twig) {
    // Add a custom Twig function to check if a template exists
    $twig->addFunction(new \Twig\TwigFunction('template_exists', function ($template) {
        $loader = new \Twig\Loader\FilesystemLoader(get_template_directory() . '/templates/app');
        $exists = $loader->exists($template);

        // Debug log
        error_log('Checking template: ' . $template . ' - Exists: ' . ($exists ? 'Yes' : 'No'));

        return $exists;
    }));
    return $twig;
});