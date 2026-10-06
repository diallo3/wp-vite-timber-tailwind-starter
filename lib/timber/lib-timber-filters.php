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
 * Twig function `template_exists(name)` — checks a template against Timber's
 * own loader, so namespaces like `@Content/...` resolve.
 */
add_filter('timber/twig', function ($twig) {
    $twig->addFunction(new \Twig\TwigFunction('template_exists', function ($template) use ($twig) {
        return $twig->getLoader()->exists($template);
    }));
    return $twig;
});