<?php
/**
 * Template Name: Brand Guidelines
 * Description: A comprehensive brand guidelines page template with sections for colors, typography, logos, and UI elements
 *
 * @package  WordPress
 * @subpackage  Timber
 */

use Timber\Timber;

$context = Timber::context();

$timber_post     = Timber::get_post();
$context['post'] = $timber_post;

/**
 * Colour and font tokens from the `@theme` block in src/app.css, so this page
 * shows exactly what Tailwind generates.
 *
 * Shade scales (`brand-600`, `azure-500`) stay in the palette grid. Role
 * colours (`primary`, `ink`, `surface`) are one group.
 *
 * @return array{colors: array<string, array>, fonts: array<string, string>}
 *     Colours grouped by scale, plus a `roles` group. Fonts by name (`heading`).
 */
function brand_theme_tokens(): array {
    $tokens = ['colors' => [], 'fonts' => []];
    $css = (string) @file_get_contents(get_template_directory() . '/src/app.css');

    if (!preg_match('/@theme\s*\{(.*?)\n\}/s', $css, $theme)) {
        return $tokens;
    }

    $roles = [
        'primary',
        'primary-hover',
        'primary-soft',
        'on-primary',
        'ink',
        'muted',
        'surface',
        'surface-inverse',
        'line',
    ];

    preg_match_all('/--(color|font)-([a-z0-9-]+):\s*([^;]+);/', $theme[1], $matches, PREG_SET_ORDER);

    foreach ($matches as [, $type, $name, $value]) {
        $value = trim($value);

        if ($type === 'font') {
            $tokens['fonts'][$name] = $value;
            continue;
        }

        $rgb = sscanf(ltrim($value, '#'), '%02x%02x%02x');
        $swatch = [
            'name' => $name,
            'hex' => $value,
            'rgb' => count(array_filter($rgb, 'is_int')) === 3 ? 'rgb(' . implode(', ', $rgb) . ')' : '',
            'usage' => '',
        ];

        if (in_array($name, $roles, true)) {
            $tokens['colors']['roles'][] = $swatch;
            continue;
        }

        $group = preg_replace('/-\d+$/', '', $name);
        $tokens['colors'][$group][] = $swatch;
    }

    return $tokens;
}

$tokens = brand_theme_tokens();
$font_weights = ['heading' => '400, 500, 600, 700, 800, 900', 'body' => '400, 500, 600, 700'];

// Brand Guidelines Data Structure
$context['brand'] = [
    'name' => get_bloginfo('name'),
    'tagline' => get_bloginfo('description'),

    // Colors: from @theme in src/app.css
    'colors' => $tokens['colors'],

    // Typography
    'typography' => [
        // Font families: from @theme in src/app.css
        'families' => array_map(
            fn($name, $font) => [
                'name' => ucfirst($name) . " (font-{$name})",
                'font' => $font,
                'weights' => $font_weights[$name] ?? '',
            ],
            array_keys($tokens['fonts']),
            $tokens['fonts']
        ),
        'scale' => [
            ['name' => 'Display XL', 'size' => '4.5rem / 72px', 'lineHeight' => '1.1', 'usage' => 'Hero headlines'],
            ['name' => 'Display L', 'size' => '3.75rem / 60px', 'lineHeight' => '1.1', 'usage' => 'Page headers'],
            ['name' => 'Display M', 'size' => '3rem / 48px', 'lineHeight' => '1.2', 'usage' => 'Section headers'],
            ['name' => 'Heading 1', 'size' => '2.25rem / 36px', 'lineHeight' => '1.2', 'usage' => 'H1 elements'],
            ['name' => 'Heading 2', 'size' => '1.875rem / 30px', 'lineHeight' => '1.3', 'usage' => 'H2 elements'],
            ['name' => 'Heading 3', 'size' => '1.5rem / 24px', 'lineHeight' => '1.3', 'usage' => 'H3 elements'],
            ['name' => 'Heading 4', 'size' => '1.25rem / 20px', 'lineHeight' => '1.4', 'usage' => 'H4 elements'],
            ['name' => 'Body Large', 'size' => '1.125rem / 18px', 'lineHeight' => '1.6', 'usage' => 'Lead paragraphs'],
            ['name' => 'Body', 'size' => '1rem / 16px', 'lineHeight' => '1.6', 'usage' => 'Body text'],
            ['name' => 'Body Small', 'size' => '0.875rem / 14px', 'lineHeight' => '1.5', 'usage' => 'Captions, labels'],
            ['name' => 'Body XSmall', 'size' => '0.75rem / 12px', 'lineHeight' => '1.5', 'usage' => 'Fine print'],
        ],
    ],

    // Spacing
    'spacing' => [
        ['name' => 'XS', 'value' => '0.25rem / 4px', 'usage' => 'Tight spacing'],
        ['name' => 'S', 'value' => '0.5rem / 8px', 'usage' => 'Small gaps'],
        ['name' => 'M', 'value' => '1rem / 16px', 'usage' => 'Default spacing'],
        ['name' => 'L', 'value' => '1.5rem / 24px', 'usage' => 'Medium spacing'],
        ['name' => 'XL', 'value' => '2rem / 32px', 'usage' => 'Large spacing'],
        ['name' => '2XL', 'value' => '3rem / 48px', 'usage' => 'Extra large spacing'],
        ['name' => '3XL', 'value' => '4rem / 64px', 'usage' => 'Section spacing'],
    ],

    // Border Radius
    'radius' => [
        ['name' => 'None', 'value' => '0', 'usage' => 'Sharp corners'],
        ['name' => 'Small', 'value' => '0.25rem / 4px', 'usage' => 'Subtle rounding'],
        ['name' => 'Medium', 'value' => '0.5rem / 8px', 'usage' => 'Default buttons, cards'],
        ['name' => 'Large', 'value' => '1rem / 16px', 'usage' => 'Prominent elements'],
        ['name' => 'Full', 'value' => '9999px', 'usage' => 'Pills, avatars'],
    ],

    // Shadows
    'shadows' => [
        ['name' => 'Small', 'value' => '0 1px 2px rgba(0,0,0,0.05)', 'usage' => 'Subtle elevation'],
        ['name' => 'Medium', 'value' => '0 4px 6px rgba(0,0,0,0.1)', 'usage' => 'Cards, dropdowns'],
        ['name' => 'Large', 'value' => '0 10px 15px rgba(0,0,0,0.1)', 'usage' => 'Modals, popovers'],
        ['name' => 'XLarge', 'value' => '0 20px 25px rgba(0,0,0,0.15)', 'usage' => 'Floating elements'],
    ],
];

// Allow filtering/customization via WordPress filters
$context['brand'] = apply_filters('brand_guidelines_data', $context['brand']);

$templates = ['pages/page-brand-guidelines/page-brand-guidelines.twig'];
Timber::render($templates, $context);
