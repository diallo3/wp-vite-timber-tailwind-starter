# WP Theme: Timber + Vite + ACF

A classic WordPress theme. Twig templates through [Timber 2](https://timber.github.io/docs/v2/), pages built from ACF flexible content layouts, [Vite](https://vite.dev/) for assets, [Tailwind CSS v4](https://tailwindcss.com/), [Alpine.js](https://alpinejs.dev/) and [Motion](https://motion.dev/).

## Requirements

- PHP 8.1+ (Composer resolves dependencies for 8.1.29, the Local site's version) and [Composer](https://getcomposer.org/)
- Node 20.19+ and npm
- [Local](https://localwp.com/) or any WordPress install
- Plugins (licences required for the Pro ones):
  - [ACF Pro](https://www.advancedcustomfields.com/pro/)
  - [ACF Extended Pro](https://www.acf-extended.com/)
  - [ACF Component Field](https://acf-component-field.gummi.io/)
  - [Classic Editor](https://wordpress.org/plugins/classic-editor/)

## Setup

1. Clone into `wp-content/themes/wp-theme-timber-vite-acf`.
2. Install dependencies:
   ```bash
   composer install
   npm ci
   ```
3. Optional: `cp .env.example .env` to change the dev server host/port/protocol or output dir. The defaults are `https://localhost:3000` and `dist/`.
4. Make sure the site's environment type is `local` or `development` (Local sets this) so the theme uses the dev server.
5. Activate the theme and the plugins, then sync the field groups under *ACF → Field Groups → Sync available*.

## Commands

| Command | What it does |
| --- | --- |
| `npm run dev` | Vite dev server with HMR; Twig/PHP changes reload the page |
| `npm run build` | Production build into `dist/` |
| `npm run build:watch` | Rebuild on change (for testing built assets) |
| `npm run lint` | `php -l`, acf-json integrity and Twig syntax/include checks |

While `npm run dev` is running it writes `.vite-hot`, and the theme loads assets from the dev server. When it isn't running, the theme falls back to `dist/`, so run `npm run build` at least once.

## Deploying

`dist/` and `vendor/` are not committed. A deploy must run:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
```

Don't deploy `.vite-hot` or `.env`. CI (`.github/workflows/ci.yml`) runs the same build plus `npm run lint` on pull requests and pushes to `main`.

## Structure

```
*.php                  WordPress templates: prepare context, pick a Twig template
lib/acf/               ACF setup, flexible content hooks, ACFE preview, layout thumbnails
lib/functions/         Vite integration (lib-vite.php), utilities
lib/timber/            Timber site class, Twig loader namespaces and functions
templates/
  app/components/content/<layout>/   one folder per flexible layout
  app/elements/        macros: heading, button, card, icon, image, badge, menus
  app/globals/         header, footer
  app/layouts/         base layout, section wrappers, flexible content loop
  pages/               page, single, archive, author, search, 404, brand guidelines,
                       page-plugin (wraps plugin output from header.php/footer.php)
src/                   main.js, app.css (Tailwind), admin.css, modules/
public/icons/          SVG icons, served as-is
acf-json/              ACF field groups and post types (synced)
bin/                   lint and acf-json helpers
docs/                  project docs
```

Twig namespaces: `@Components`, `@Content`, `@Layouts`, `@Globals`, `@Elements` and `@Pages` (defined in `lib/timber/lib-timber-filters.php`).

## Docs

- [docs/flexible-content.md](docs/flexible-content.md): how layouts render, and how to add one
- [docs/acf-conventions.md](docs/acf-conventions.md): field groups, shared components, JSON sync, post types
- [docs/frontend.md](docs/frontend.md): Vite, Tailwind tokens and utilities, Alpine, Motion, view transitions, escaping
- [docs/AUDIT-ROADMAP.md](docs/AUDIT-ROADMAP.md): audit findings and what's been fixed
