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

// Brand Guidelines Data Structure
$context['brand'] = [
    'name' => get_bloginfo('name'),
    'tagline' => get_bloginfo('description'),

    // Colors
    'colors' => [
        'primary' => [
            ['name' => 'Primary', 'hex' => '#0d99d5', 'rgb' => 'rgb(13, 153, 213)', 'usage' => 'Main brand color, primary CTAs'],
            ['name' => 'Primary Dark', 'hex' => '#0b7ab0', 'rgb' => 'rgb(11, 122, 176)', 'usage' => 'Hover states, emphasis'],
            ['name' => 'Primary Light', 'hex' => '#3db3e3', 'rgb' => 'rgb(61, 179, 227)', 'usage' => 'Backgrounds, accents'],
        ],
        'secondary' => [
            ['name' => 'Secondary', 'hex' => '#6366f1', 'rgb' => 'rgb(99, 102, 241)', 'usage' => 'Secondary actions, accents'],
            ['name' => 'Accent', 'hex' => '#f59e0b', 'rgb' => 'rgb(245, 158, 11)', 'usage' => 'Highlights, warnings'],
        ],
        'neutral' => [
            ['name' => 'Black', 'hex' => '#111827', 'rgb' => 'rgb(17, 24, 39)', 'usage' => 'Primary text, headers'],
            ['name' => 'Gray Dark', 'hex' => '#374151', 'rgb' => 'rgb(55, 65, 81)', 'usage' => 'Body text, borders'],
            ['name' => 'Gray', 'hex' => '#6b7280', 'rgb' => 'rgb(107, 114, 128)', 'usage' => 'Secondary text'],
            ['name' => 'Gray Light', 'hex' => '#d1d5db', 'rgb' => 'rgb(209, 213, 219)', 'usage' => 'Borders, dividers'],
            ['name' => 'Gray Lighter', 'hex' => '#f3f4f6', 'rgb' => 'rgb(243, 244, 246)', 'usage' => 'Backgrounds, cards'],
            ['name' => 'White', 'hex' => '#ffffff', 'rgb' => 'rgb(255, 255, 255)', 'usage' => 'Page backgrounds'],
        ],
        'semantic' => [
            ['name' => 'Success', 'hex' => '#10b981', 'rgb' => 'rgb(16, 185, 129)', 'usage' => 'Success messages, positive states'],
            ['name' => 'Warning', 'hex' => '#f59e0b', 'rgb' => 'rgb(245, 158, 11)', 'usage' => 'Warning messages, caution'],
            ['name' => 'Error', 'hex' => '#ef4444', 'rgb' => 'rgb(239, 68, 68)', 'usage' => 'Error messages, destructive actions'],
            ['name' => 'Info', 'hex' => '#3b82f6', 'rgb' => 'rgb(59, 130, 246)', 'usage' => 'Informational messages'],
        ],
    ],

    // Typography
    'typography' => [
        'families' => [
            ['name' => 'Headings', 'font' => 'System UI, -apple-system, sans-serif', 'weights' => '400, 500, 600, 700, 800, 900'],
            ['name' => 'Body', 'font' => 'System UI, -apple-system, sans-serif', 'weights' => '400, 500, 600'],
            ['name' => 'Monospace', 'font' => 'ui-monospace, SFMono-Regular, monospace', 'weights' => '400, 500, 600'],
        ],
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
