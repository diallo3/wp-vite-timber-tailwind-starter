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

## Advanced JavaScript Module Integration

### Swup Page Transitions (WordPress-Specific Implementation)

Implementing Swup in WordPress requires careful consideration of WordPress-specific features and potential conflicts. The `src/modules/js/module-swup.js` contains a comprehensive WordPress-optimized implementation.

**Critical WordPress Considerations:**

1. **Admin/Customizer Detection**: Swup must be disabled in WordPress admin areas and customizer to prevent conflicts:
   ```javascript
   // Check if Swup should be disabled
   if (document.body.classList.contains('wp-admin') ||
       document.body.classList.contains('customize-support')) {
       return;
   }
   ```

2. **Container Selectors**: WordPress themes use various main content containers:
   ```javascript
   containers: ['#swup', '#main', 'main', '.site-main']
   ```

3. **Link Exclusions**: WordPress-specific links must be excluded from Swup:
   ```javascript
   linkSelector: 'a[href]:not([data-no-swup]):not([href^="#"]):not([href*="wp-admin"]):not([href*="wp-login"]):not([target="_blank"]):not([download])'
   ```

**Module Re-initialization After Transitions:**

WordPress sites with complex JavaScript need module re-initialization after each page transition:

- **Alpine.js**: `window.Alpine.initTree(document.body)` - Re-scans for new Alpine components
- **Motion Animations**: Re-run `generalInView()` and `scrollAnimations()` for new page content
- **Headroom.js**: Re-initialize header behavior for new pages
- **WordPress Forms**: Handle Contact Form 7 and Gravity Forms re-initialization

**WordPress Plugin Integration:**

The implementation includes specific handlers for common WordPress plugins:

```javascript
// Contact Form 7 re-initialization
if (window.wpcf7) {
    const forms = document.querySelectorAll('.wpcf7 > form');
    forms.forEach(form => {
        if (window.wpcf7.initForm) {
            window.wpcf7.initForm(form);
        }
    });
}
```

**Page Class Management:**

WordPress relies heavily on body classes for styling. The implementation maintains these:

```javascript
// Remove old page classes and add new ones based on URL
document.body.className = document.body.className
    .replace(/page-id-\d+/g, '')
    .replace(/page-template-[\w-]+/g, '')
    .replace(/postid-\d+/g, '');
```

**Transition Animations:**

Custom CSS classes provide smooth transitions:
- `.swup-transition-fade` - Simple opacity transitions
- `.swup-transition-slide` - Transform-based sliding
- `.swup-transition-scale` - Scale + opacity effects
- `.is-changing` - Loading state management

**Usage in Templates:**

Add transition classes to main content containers:
```html
<main id="main" class="swup-transition-fade">
    <!-- Page content -->
</main>
```

Disable Swup on specific links:
```html
<a href="/special-page" data-no-swup>No Transition Link</a>
```

**Performance Considerations:**

- Caching is enabled for faster subsequent loads
- Animation functions are stored globally for efficient re-use
- Error handling prevents JavaScript failures from breaking navigation
- WordPress customizer events are properly triggered

**Testing Checklist for Swup Implementation:**

1. ✅ Admin area navigation works normally (no Swup interference)
2. ✅ WordPress customizer functions properly
3. ✅ Contact forms submit correctly after transitions
4. ✅ Alpine.js components initialize on new pages
5. ✅ Motion animations trigger on new content
6. ✅ Header behavior (Headroom) works across transitions
7. ✅ Page-specific CSS classes are maintained
8. ✅ WordPress admin bar positioning remains correct

This implementation represents a production-ready WordPress Swup integration that handles the complexities of WordPress's ecosystem while providing smooth page transitions.

## Tailwind CSS v4.1.13 Implementation

This project uses the latest Tailwind CSS v4.1.13 with a highly optimized architecture designed for WordPress themes with modern development workflows.

### CSS Architecture Overview

The CSS system is organized using Tailwind's layer system for optimal performance and maintainability:

```css
@import "tailwindcss";
@import "./modules/css/modules-base.css" layer(base);
@import "./modules/css/modules-components.css" layer(components);
@import "./modules/css/modules-utilities.css" layer(utilities);
@import "./modules/css/modules-acf.css" layer(utilities);
@import "./modules/css/module-headroom.css" layer(utilities);
```

### Key Features & Optimizations

**Enhanced Fluid Typography System:**
- Modern clamp() functions with refined viewport scaling
- Better mathematical formulas for natural scaling across all device sizes
- Fluid line heights for improved readability: `leading-fluid-tight`, `leading-fluid-snug`, etc.
- Fluid spacing system: `space-fluid-xs` through `space-fluid-2xl`

```css
/* Example fluid typography */
--text-fluid-xl: clamp(1.25rem, 1.1rem + 0.75vw, 1.5rem); /* 20px → 24px */
--leading-fluid-normal: clamp(1.4, 1.35 + 0.25vw, 1.55);
--space-fluid-lg: clamp(1.5rem, 1.2rem + 1.5vw, 2.25rem);
```

**WordPress-Specific Optimizations:**
- Admin bar compatibility with responsive breakpoints
- Alpine.js cloak directive support
- Enhanced accessibility focus styles
- Print media support with `.no-print` utility

**Modern Swup Integration:**
- CSS-based transition utilities instead of JavaScript-generated styles
- Better performance through proper layer organization
- Integrated loading states and animation controls

### CSS Module Structure

**modules-base.css** (Layer: base)
- WordPress admin bar responsive support
- Alpine.js [x-cloak] directive
- Accessibility enhancements
- Motion preference respect
- Print media styles

**modules-utilities.css** (Layer: utilities)
- Modern Swup transition classes
- Broken image styling system
- WordPress admin bar positioning
- Loading state management

**modules-acf.css** (Layer: utilities)
- Advanced Custom Fields styling
- Radio and checkbox list improvements
- Section choice styling
- WordPress blue theme integration

### Available Utility Classes

**Fluid Typography:**
```html
<h1 class="fluid-text-5xl leading-fluid-tight">Responsive Heading</h1>
<p class="fluid-text-base leading-fluid-normal">Body text</p>
```

**Fluid Spacing:**
```html
<section class="space-fluid-lg">Responsive spacing</section>
```

**Swup Transitions:**
```html
<main class="swup-transition-fade">Page content</main>
<div class="swup-transition-slide">Sliding content</div>
<article class="swup-transition-scale">Scaling content</article>
```

### Theme Configuration

**Custom Theme Colors:**
```css
@theme {
  --color-wordpress-blue: #0d99d5;
  --color-blue-wordpress: #0d99d5; /* Alternative naming */
}
```

**Enhanced Responsive Breakpoints:**
```css
--breakpoint-xs: 360px;   /* Extra small devices */
--breakpoint-sm: 640px;   /* Small devices */
--breakpoint-md: 768px;   /* Medium devices */
--breakpoint-lg: 1024px;  /* Large devices */
--breakpoint-xl: 1280px;  /* Extra large */
--breakpoint-2xl: 1536px; /* 2X large */
--breakpoint-3xl: 1920px; /* 3X large */
```

### Performance Considerations

1. **Layer Organization**: Proper CSS cascade with explicit layers
2. **Reduced JavaScript**: Transition styles moved from JS to CSS
3. **Eliminated Duplicates**: Consolidated duplicate styles across modules
4. **Optimized Clamp Functions**: More efficient viewport scaling formulas
5. **WordPress Integration**: Native support for WordPress features

### Development Workflow

When working with styles:

1. **Base styles** → `modules-base.css` (resets, WordPress compatibility)
2. **Component styles** → `modules-components.css` (reusable components)
3. **Utility overrides** → `modules-utilities.css` (custom utilities)
4. **ACF-specific** → `modules-acf.css` (admin field styling)
5. **Plugin styles** → Individual module files (e.g., `module-headroom.css`)

### Maintenance Notes

- **Tailwind v4.1.13** uses native CSS features extensively
- **No PostCSS plugins required** for basic functionality
- **@utility directive** provides custom utility creation
- **CSS Custom Properties** are preferred over Sass variables
- **Layer system** ensures predictable cascade behavior

This implementation provides a robust foundation for scalable WordPress themes with modern CSS architecture and optimal performance.

## Alpine.js v3.15.0 Implementation

This project uses Alpine.js v3.15.0 as the primary JavaScript framework for interactive UI components. The implementation is optimized for WordPress themes with comprehensive component library and global state management.

### Alpine.js Architecture

**Module Structure:** `src/modules/js/module-alpine.js`
- Component registration system
- Global stores for state management
- Custom magic helpers and directives
- WordPress-specific optimizations

**Installed Plugins:**
- `@alpinejs/persist@3.15.0` - Persistent data across page loads
- `@alpinejs/focus@3.15.0` - Advanced focus management
- `@alpinejs/collapse@3.15.0` - Smooth collapse/expand animations

### Available Alpine Components

**Navigation Highlighter:** `x-data="visibleNavHighlighter('h2, h3, h4')"`
- Automatically highlights navigation items based on scroll position
- Smooth scrolling to sections
- Customizable heading selectors

**Enhanced Scroller:** `x-data="scrollerComponent"`
- Performance-optimized infinite scroll with Intersection Observer
- Respects motion preferences (`prefers-reduced-motion`)
- Automatic item duplication for seamless loops

```html
<div x-data="scrollerComponent" x-ref="scroller" class="scroller">
    <div x-ref="scrollerInner" class="scroller-inner">
        <div>Item 1</div>
        <div>Item 2</div>
        <div>Item 3</div>
    </div>
</div>
```

**Modal Component:** `x-data="modalComponent"`
- Accessible modal with focus management
- Body scroll prevention
- Escape key handling
- Focus restoration on close

```html
<div x-data="modalComponent">
    <button @click="open = true">Open Modal</button>
    <div x-show="open" @keydown.escape="handleEscape" x-trap="open">
        <div class="modal-content">
            <!-- Modal content -->
            <button @click="open = false">Close</button>
        </div>
    </div>
</div>
```

**Dropdown Component:** `x-data="dropdownComponent"`
- Keyboard navigation with arrow keys
- ARIA-compliant menu behavior
- Auto-focus management

```html
<div x-data="dropdownComponent" @keydown="handleKeydown">
    <button @click="toggle()" :aria-expanded="open">Menu</button>
    <ul x-show="open" x-ref="menu" role="menu">
        <li role="menuitem" tabindex="0">Item 1</li>
        <li role="menuitem" tabindex="0">Item 2</li>
    </ul>
</div>
```

**Toast Notifications:** `x-data="toastComponent"`
- Persistent notifications with localStorage
- Auto-dismissal with configurable duration
- Multiple notification types (info, success, warning, error)

```html
<div x-data="toastComponent">
    <button @click="addNotification('Success!', 'success')">Show Toast</button>
    <div class="toast-container">
        <template x-for="notification in notifications" :key="notification.id">
            <div class="toast" :class="'toast-' + notification.type">
                <span x-text="notification.message"></span>
                <button @click="removeNotification(notification.id)">×</button>
            </div>
        </template>
    </div>
</div>
```

**Tabs Component:** `x-data="tabsComponent"`
- URL hash integration for bookmarkable tabs
- Keyboard navigation with arrow keys
- ARIA accessibility support

```html
<div x-data="tabsComponent">
    <div x-ref="tabs" role="tablist">
        <button @click="setActiveTab(0)" :class="{ 'active': activeTab === 0 }"
                data-tab="overview" role="tab">Overview</button>
        <button @click="setActiveTab(1)" :class="{ 'active': activeTab === 1 }"
                data-tab="details" role="tab">Details</button>
    </div>
    <div x-show="activeTab === 0" role="tabpanel">Overview content</div>
    <div x-show="activeTab === 1" role="tabpanel">Details content</div>
</div>
```

### Alpine Stores (Global State)

**Theme Store:** `$store.theme`
```html
<!-- Theme toggle -->
<button @click="$store.theme.toggle()"
        x-text="$store.theme.mode === 'dark' ? 'Light Mode' : 'Dark Mode'">
</button>

<!-- Current theme display -->
<div x-text="'Current theme: ' + $store.theme.mode"></div>
```

**Navigation Store:** `$store.navigation`
```html
<!-- Mobile menu toggle -->
<button @click="$store.navigation.toggleMobileMenu()"
        :aria-expanded="$store.navigation.mobileMenuOpen">
    Menu
</button>

<!-- Mobile menu -->
<nav x-show="$store.navigation.mobileMenuOpen"
     x-transition class="mobile-menu">
    <!-- Navigation items -->
</nav>
```

### Custom Magic Helpers

**Clipboard Magic:** `$clipboard(text)`
```html
<button @click="$clipboard('Text to copy').then(() => $toast('Copied!', 'success'))">
    Copy Text
</button>
```

**Toast Magic:** `$toast(message, type, duration)`
```html
<button @click="$toast('Hello World!', 'info', 3000)">Show Toast</button>
<button @click="$toast('Error occurred', 'error')">Show Error</button>
```

### WordPress Integration Features

**Swup Re-initialization:**
- Automatic Alpine component re-initialization after page transitions
- Maintains state across page changes
- Compatible with WordPress admin and customizer

**Performance Optimizations:**
- Intersection Observer for scroll-based components
- Motion preference respect throughout
- Lazy component initialization
- Efficient DOM manipulation

**Accessibility Features:**
- WCAG 2.1 AA compliant components
- Proper ARIA attributes and roles
- Keyboard navigation support
- Focus management and restoration

### Usage Patterns

**Component Declaration:**
```html
<!-- Single component -->
<div x-data="modalComponent">

<!-- Component with parameters -->
<div x-data="visibleNavHighlighter('h2, h3')">

<!-- Multiple components (not recommended) -->
<div x-data="{ ...modalComponent(), ...dropdownComponent() }">
```

**State Management:**
```html
<!-- Local state -->
<div x-data="{ count: 0 }">
    <span x-text="count"></span>
    <button @click="count++">Increment</button>
</div>

<!-- Global state -->
<div x-data>
    <span x-text="$store.theme.mode"></span>
    <button @click="$store.theme.toggle()">Toggle Theme</button>
</div>
```

**Event Handling:**
```html
<!-- Basic events -->
<button @click="doSomething()">Click me</button>

<!-- Event modifiers -->
<form @submit.prevent="handleSubmit">
<input @keydown.enter="submit" @keydown.escape="cancel">

<!-- Custom events -->
<div @toast.window="handleToast($event.detail)">
```

### Performance Considerations

1. **Component Initialization**: Components are lazily initialized only when needed
2. **Intersection Observer**: Scroll-based animations use efficient observer patterns
3. **Memory Management**: Event listeners are properly cleaned up
4. **State Persistence**: Uses localStorage efficiently with Alpine.persist
5. **DOM Updates**: Minimal DOM manipulation with Alpine's reactivity

### Development Guidelines

- **Component Naming**: Use camelCase for component names (e.g., `modalComponent`)
- **Store Organization**: Keep global state minimal and organized by feature
- **Event Naming**: Use descriptive event names with proper namespacing
- **Accessibility**: Always include proper ARIA attributes and keyboard support
- **Performance**: Use Intersection Observer for scroll-based interactions
- **WordPress Compatibility**: Test components work with Swup transitions

This Alpine.js implementation provides a solid foundation for building interactive WordPress themes with modern JavaScript patterns, excellent accessibility, and optimal performance.