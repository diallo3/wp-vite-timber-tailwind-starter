# CLAUDE.md

Guidance for AI agents working in this repository. Read [README.md](README.md) for setup and structure, and the files in `docs/` for detail.

## Commands

- `npm run dev`: Vite dev server. Writes `.vite-hot` while running; PHP loads assets from it only in `local`/`development` environments.
- `npm run build`: production build into `dist/` (not committed).
- `npm run lint`: `php -l`, acf-json integrity and Twig parse/include checks. Run it after any PHP, Twig or acf-json change.
- `composer install`: Timber and Twig. Composer's platform is pinned to PHP 8.1.29 (the site's PHP), so don't remove that pin.

## Stack

WordPress classic theme (Classic Editor, no blocks) · Timber 2 / Twig 3 · ACF Pro + ACF Extended Pro + ACF Component Field · Vite 7 · Tailwind CSS 4 (`@tailwindcss/vite`) · Alpine.js 3 · Motion · Headroom.

There is **no** Swup, DaisyUI, SCSS, PostCSS config or block tooling. Don't reintroduce them. Page transitions use native CSS view transitions.

## Render flow

1. Root `*.php` templates build the Timber context and render a Twig template from `templates/pages/`.
2. Pages and posts render the `page_content` flexible content field through `templates/app/layouts/flexible-content.twig`.
3. Each layout goes through `@Layouts/_flexible-layout.twig`, which includes `@Content/<layout>/index.twig`. The ACFE admin preview (`lib/acf/layout-preview.php`) uses the same partial with `is_preview: true`.
4. Components embed `@Layouts/sections/_embed-section-component-helper.twig`, which exposes `component`, the intro values (`accent`, `title`, `sub_title`, `entry`, `ctas`, `has_*`) and section options.

Details and the add-a-layout checklist: [docs/flexible-content.md](docs/flexible-content.md).

## Conventions

- **Twig namespaces:** `@Components`, `@Content`, `@Layouts`, `@Globals`, `@Elements`, `@Pages`.
- **Macros** live in `templates/app/elements/` (heading, button, card, icon, image, badge, menus). Import them *inside* `embed` blocks; imports don't carry into embeds.
- **Escaping:** autoescape is on. Use `|raw` only for editor rich text (WYSIWYG, oEmbed, post content, excerpts) and trusted markup; use `|esc_html` for WordPress-encoded titles; don't filter macro output. See [docs/frontend.md](docs/frontend.md#escaping-in-twig).
- **ACF:**
  - Fields are edited in the ACF UI and saved to `acf-json/`. When editing JSON by script, use `bin/acf-json.cjs` so the format and `modified` stay right.
  - Reuse *Component Section Intro*, *Component CTA Group* and *Component Options* instead of copying fields.
  - Component Field values are arrays of rows, so read `[0]`.
  - See [docs/acf-conventions.md](docs/acf-conventions.md).
- **Twig helpers** (`lib/timber/lib-timber-filters.php`): `section_intro(component)`, `cta_group(value)`, `template_exists(name)`.
- **CSS:**
  - Tailwind utilities in templates.
  - Tokens go in `@theme` in `src/app.css`.
  - Custom utilities go in `src/modules/css/modules-utilities.css`.
  - Tailwind only scans `templates/`, `lib/`, root `*.php` and `src/`.
- **JS:** ES modules in `src/modules/js/`, imported and started from `src/main.js`. Prefer inline Alpine `x-data` for small interactions.
- **PHP:** document functions with DocBlocks. Text domain is `wp-theme-timber-vite-acf`.
- **Content** is dev-only: restructure fields freely and rebuild `/all-page-content/` (the page with every layout) rather than writing migrations.

## Verifying changes

- `npm run lint` and `npm run build` must pass.
- For template changes, load the affected pages on the Local site (`https://vite-starter.local/`, especially `/all-page-content/`). Check for PHP warnings, escaped HTML (`&lt;p&gt;`) or double-encoded entities (`&amp;#8217;`).
- Check the ACFE preview for layout changes.

Known open items are tracked in [docs/AUDIT-ROADMAP.md](docs/AUDIT-ROADMAP.md).
