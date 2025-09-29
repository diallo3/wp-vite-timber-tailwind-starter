# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

**Start development server:**
```bash
npm run dev
```
Runs Vite dev server with hot reload and watches for file changes in HTML, Twig, PHP files.

**Build for production:**
```bash
npm run build
```
Compiles and optimizes assets for production deployment.

**Build with watch mode:**
```bash
npm run build-dev
```
Builds assets and watches for changes (useful for testing production builds locally).

**Install dependencies:**
```bash
npm install
composer install
```

## Architecture Overview

This is a WordPress theme built with modern development tools, using the Timber templating system with Twig templates, Advanced Custom Fields (ACF) for content management, and Vite for asset bundling.

### Core Technology Stack
- **WordPress Theme**: Custom theme with Timber/Twig templating
- **Timber**: Separates PHP logic from HTML templates using Twig
- **ACF Pro**: Custom fields and custom blocks for the Block Editor
- **Vite**: Modern build tool for assets with hot reload
- **Tailwind CSS**: Utility-first CSS framework
- **Alpine.js**: Lightweight JavaScript framework

### Directory Structure

**Root WordPress template files** (`/`): Standard WordPress templates (index.php, page.php, single.php, etc.) act as controllers, preparing data for Twig templates.

**`/src/`**: Source files for global assets
- `main.js`: Main JavaScript entry point
- `app.css`: Global styles and Tailwind imports
- `modules/`: Additional JS modules

**`/templates/`**: Twig template files organized by function
- `app/components/`: Reusable UI components
- `app/globals/`: Global elements (headers, footers, pagination)
- `pages/`: Page-specific templates

**`/lib/`**: PHP functionality organized by purpose
- `functions/`: Utility functions and Vite integration
- `acf/`: ACF configuration and custom blocks
- `timber/`: Timber setup and custom filters

**`/dist/`**: Compiled assets (auto-generated, don't edit)

**`/acf-json/`**: ACF field group configurations (version controlled)

### Component Architecture

Components follow a three-file pattern:
1. `component-name.twig` - Template markup
2. `component-name.scss` - Component styles (auto-imported)
3. `component-name.js` - Component JavaScript (import in main.js)

Components can be registered as custom ACF blocks using Timber ACF WP Blocks by adding formatted comments at the top of Twig files.

### Vite Configuration

Vite is configured through environment variables in `.env`:
- `VITE_OUTPUT_DIR=dist` - Build output directory
- `VITE_ENTRY_POINT=src/main.js` - Main JavaScript entry
- `VITE_PORT=3000` - Dev server port
- `VITE_HOST=localhost` - Dev server host

### Development Workflow

1. Content structure is defined using ACF field groups
2. PHP templates in root prepare data and specify which Twig template to use
3. Twig templates in `/templates/` handle HTML markup and display logic
4. SCSS styles are automatically imported from template directories
5. JavaScript modules are imported and initialized in `src/main.js`
6. Vite handles asset compilation and provides hot reload during development

### Key Files to Reference

- `functions.php` - Theme initialization and includes
- `lib/timber/lib-timber.php` - Main Timber configuration class
- `lib/acf/lib-acf.php` - ACF setup and custom blocks
- `vite.config.js` - Build tool configuration
- `src/main.js` - JavaScript entry point
- `src/app.css` - Global styles and framework imports

### Custom Functions

PHP functions are documented with DocBlocks and located primarily in:
- `lib/functions/lib-utilities.php` - General utility functions
- `lib/functions/lib-vite.php` - Vite asset management

This theme is optimized for medium-to-large sites requiring both custom blocks for content editors and flexible templating for developers.