# Header styling based on first section

This document describes how the site header changes its appearance based on the **first section** of the page (flexible content).

## Overview

- **When the first section is "Section Header Complex" (hero):** The header starts **transparent** with white nav text and transitions to a **white** header with dark text after the user scrolls.
- **When the first section is anything else (or there is no flexible content):** The header is **white** with dark text from the start.

## Implementation

### 1. First-section detection

**File:** `templates/app/globals/headers/_global-header-main.twig`

The header decides whether to start transparent using the current page’s flexible content:

```twig
{% set is_header_transparent = flexible_content is defined and flexible_content|length > 0 and flexible_content[0].component_name == 'section_header_complex' %}
```

- `flexible_content` is provided by the page context (e.g. from `page.php` for the main page template).
- The **first block** is `flexible_content[0]`; its layout name is in `component_name`.
- The ACF layout name for “Section Header Complex” is `section_header_complex`.

So:

- **Transparent initial state:** Only when the first block is `section_header_complex`.
- **White initial state:** Any other first section, or when `flexible_content` is missing/empty (e.g. 404, archives, custom templates that don’t set it).

### 2. Alpine.js state

The header uses Alpine for scroll and “over hero” state:

- **`isHeaderTransparent`** — Set once from Twig (`is_header_transparent`). True only when the first section is Section Header Complex.
- **`scrolledFromTop`** — True when `window.pageYOffset >= 50`, i.e. user has scrolled down a bit.

These drive background, logo visibility, and nav colors.

### 3. Header background

- **Over hero (transparent first section, not scrolled):**  
  `bg-transparent` and class `site-header--over-hero`.
- **Solid header (any other case or after scroll):**  
  `bg-white`.

So the header is white when either:

- The first section is **not** Section Header Complex, or  
- The first section **is** Section Header Complex but the user has scrolled (`scrolledFromTop` is true).

A `transition-colors duration-300` class is used so the background change is smooth.

### 4. Logo

- **Over hero:** Logo is visible at the top; it is hidden (opacity 0) once the user has scrolled (`scrolledFromTop`).
- **Non-hero first section:** Logo is always visible (no scroll-based hiding).

This is done with an Alpine `:class` on the logo wrapper based on `isHeaderTransparent` and `scrolledFromTop`.

### 5. Center navigation (mega menu)

- **Over hero:** Nav uses **white** text (`text-white`). Link hover uses a semi-transparent tiber background (`bg-tiber-500/30`).
- **Solid header:** Nav uses **dark** text (`text-gray-900`). Link hover uses `bg-gray-200`.

Text color is toggled via Alpine `:class` on the nav so it stays in sync with scroll. Hover styles are defined in CSS so they respect hero vs solid state.

### 6. CSS for nav link hover

**File:** `src/modules/css/modules-base.css`

- **Over hero:** `.site-header--over-hero .site-header__center a:hover span` → `bg-tiber-500/30`.
- **Solid header:** `.site-header:not(.site-header--over-hero) .site-header__center a:hover span` → `bg-gray-200`.

Nav link spans also have `transition-colors duration-300` for smooth hover.

## Data flow

1. **Page template (e.g. `page.php`)** calls `render_acf_flexible_content($post_id)` and sets `$context['flexible_content']`.
2. **Layout** (`layout-base.twig`) is rendered with that context and includes `_global-header-main.twig` without overriding the context.
3. **Header** template therefore receives `flexible_content` and computes `is_header_transparent` from the first block’s `component_name`.
4. **Alpine** gets `isHeaderTransparent` from Twig and uses it together with `scrolledFromTop` to apply the correct header and nav styling.

On templates that do not set `flexible_content` (e.g. 404, archive, custom page templates), `flexible_content` is undefined or empty, so `is_header_transparent` is false and the header always uses the solid (white) style.

## Files touched

| File | Role |
|------|------|
| `templates/app/globals/headers/_global-header-main.twig` | First-section check, Alpine state, conditional header/nav classes, logo visibility. |
| `src/modules/css/modules-base.css` | Nav link hover styles for hero vs solid header. |

## Extending

To make another section type trigger the transparent header (e.g. “Section Header Simple”):

- Extend the Twig condition, e.g.  
  `flexible_content[0].component_name in ['section_header_complex', 'section_header_simple']`.
- Or move the list of “hero” layout names into a Twig variable or config for easier maintenance.

To change the scroll threshold (currently 50px), update the Alpine expressions that set `scrolledFromTop` (e.g. `window.pageYOffset >= 50`) in `_global-header-main.twig`.
