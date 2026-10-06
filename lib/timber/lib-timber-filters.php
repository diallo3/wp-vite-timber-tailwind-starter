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
 * Autoescape every `{{ }}` as HTML. Editor rich text (WYSIWYG, oEmbed, post
 * content) is printed with `|raw`; strings WordPress has already
 * entity-encoded (post, menu and site titles) with `|esc_html`, which
 * doesn't double-encode.
 */
add_filter('timber/twig/environment/options', function ($options) {
    $options['autoescape'] = 'html';
    return $options;
});

/**
 * Twig function `template_exists(name)` — checks a template against Timber's
 * own loader, so namespaces like `@Content/...` resolve.
 */
add_filter('timber/twig', function ($twig) {
    $twig->addFunction(new \Twig\TwigFunction('template_exists', function ($template) use ($twig) {
        return $twig->getLoader()->exists($template);
    }));
    $twig->addFunction(new \Twig\TwigFunction('section_intro', 'theme_section_intro'));
    $twig->addFunction(new \Twig\TwigFunction('cta_group', 'theme_cta_group'));
    return $twig;
});

/**
 * First row of a non-repeatable ACF Component Field value.
 */
function theme_component_row($value): array {
    return is_array($value) && isset($value[0]) && is_array($value[0]) ? $value[0] : [];
}

/**
 * Call-to-action rows from a "Component CTA Group" field value.
 */
function theme_cta_group($value): array {
    $rows = theme_component_row($value)['call_to_actions'] ?? [];
    return array_values(array_filter((array) $rows, fn($row) => !empty($row['link']['url'])));
}

/**
 * Twig function `section_intro(component)` — normalises a component's
 * "Component Section Intro" field. Each part is only returned when its
 * toggle is on and it has content, so templates can test `intro.title` etc.
 */
function theme_section_intro($component): array {
    $intro = theme_component_row(is_array($component) ? ($component['intro'] ?? null) : null);
    $on = $intro['sections'] ?? [];
    $part = fn($name) => !empty($on[$name]) && !empty($intro[$name]) ? $intro[$name] : null;
    $ctas = !empty($on['ctas']) ? theme_cta_group($intro['ctas'] ?? null) : [];

    return [
        'accent'    => $part('accent'),
        'title'     => $part('title'),
        'sub_title' => $part('sub_title'),
        'entry'     => $part('entry'),
        'ctas'      => $ctas,
    ];
}