# Frontend

Vite builds three entries: `src/main.js` (frontend JS, which imports `src/app.css`), `src/admin.css` (wp-admin and ACF field styling) and `src/preview.js` (Alpine and component CSS, for ACFE layout previews). `lib/functions/lib-vite.php` enqueues them.

## Dev server vs built assets

- `npm run dev` starts Vite and writes `.vite-hot` (the dev server URL) to the theme root. It removes the file when it stops.
- PHP uses the dev server only when `.vite-hot` exists **and** `wp_get_environment_type()` is `local` or `development`. Otherwise it reads `dist/.vite/manifest.json`.
- `WPVite::enqueueStyle($source, $handle, $entry)` and `WPVite::enqueueScript($source, $handle)` follow the same rule for extra entries (admin CSS, the ACFE preview CSS and JS).
- `dist/` is not committed. Deploys must run `npm ci && npm run build`.
- `public/` is served by WordPress as-is (`/wp-content/themes/<theme>/public/...`) and is not copied into `dist/`.

`.env` is optional. Copy `.env.example` to change the output dir, entry or dev server host/port/protocol. Vite and PHP share the same defaults.

## Tailwind CSS v4

`src/app.css` is the only Tailwind entry:

- `@import "tailwindcss" source(none)` plus explicit `@source` rules for `templates/`, `lib/`, root `*.php` and `src/`. A class used anywhere else (markdown, `node_modules`) is not generated.
- `@theme` holds the tokens: the `dodger-blue-*` colour scale, `wordpress-blue`, the `font-heading` / `font-body` families, breakpoints `xs` (360px) to `3xl` (1920px) and the `text-fluid-*` sizes. Only `@theme` variables generate utilities; a variable in `:root` doesn't. The Brand Guidelines page template reads its colours and fonts from this block.
- `@plugin "@tailwindcss/typography"` provides `prose` for WYSIWYG output.
- `dark:` is class-based (`.dark` on an ancestor). Sections opt in through their theme option; nothing sets it from the OS preference.
- Component CSS: any `templates/**/*.css` file is imported by `main.js` and by `preview.js`, so the public site and the ACFE preview both get it.

CSS modules in `src/modules/css/`:

| File | Layer | Contents |
| --- | --- | --- |
| `modules-base.css` | base | `[x-cloak]`, admin bar offsets, paragraph font size, focus styles, smooth scrolling, `.no-print` |
| `modules-components.css` | components | Motion initial states (`.motion-loaded .inview-item`) |
| `modules-utilities.css` | — | `@utility` definitions below |
| `module-headroom.css` | utilities | `.headroom--pinned` / `--unpinned` header states |

### Fluid utilities

All scale with `clamp()` between two sizes and work with responsive variants (`md:fluid-text-lg`).

- **Text:** `fluid-text-{xs,sm,base,lg,xl,2xl … 9xl}`, from 12→14px up to 112→144px. The same sizes exist as theme tokens, so `text-fluid-xl` also works.
- **Line height:** `leading-fluid-{tight,snug,normal,relaxed}`.
- **Spacing:** `p-fluid-*` and `m-fluid-*` in `xs, sm, md, lg, xl, 2xl`. `space-fluid-*` is a legacy alias for margin.

## JavaScript

`src/main.js` imports and starts each module on `DOMContentLoaded`:

- **Alpine.js** (`module-alpine.js`) with the `focus` and `collapse` plugins. Components used by templates: `visibleNavHighlighter(selector)` (scroll-spy navigation) and `scrollerComponent` (the infinite logo scroller), plus the `$clipboard(text)` magic. Everything else uses inline `x-data`. ACFE previews load the same Alpine setup through `src/preview.js`, without Motion.
- **Headroom** (`module-headroom.js`) hides the `.js-header` element on scroll down and shows it on scroll up.
- **Motion** (`module-motionOne.js`):
  - `.inview-container .inview-item` fades/slides items in when they enter the viewport.
  - `.stagger-inview-container .stagger-inview-item` does the same with a stagger.
  - `data-scroll-animate="fadeUp|fadeDown|fadeLeft|fadeRight|scale|slideUp|slideDown"` animates a single element.
  - `.site-header__logo`, `.site-header__center menu > li` and `.site-header__ctas li` animate on load.
  - `html.motion-loaded` is set when Motion runs, so CSS only hides those elements when they will actually be animated in.
- **iconify-icon** provides the `<iconify-icon>` element used by the Content with Icons and Entry List components.

## Page transitions

Cross-document view transitions are enabled in CSS (`@view-transition { navigation: auto; }`, skipped under `prefers-reduced-motion`). There is no JS router, so every navigation is a normal page load and Alpine/Motion start fresh.

To animate a specific element between pages, give it the same `view-transition-name` on both pages:

```html
<img src="…" style="view-transition-name: post-{{ post.id }}">
```

## Icons and images

- Theme SVGs live in `public/icons/<set>/icon-<name>.svg` and are inlined with `icons.icon({ path, name, classes })` from `@Elements/_macro-icon.twig`.
- `@Elements/_macro-image-object.twig` renders `<img>` with a fallback (`public/icons/defaults/icon-temp-image.svg`).

## Escaping in Twig

Autoescape is on (`html`). In templates:

- Plain values: `{{ value }}`. They're escaped automatically.
- Editor rich text (WYSIWYG, oEmbed, `post.content`, excerpts): `{{ value|raw }}`.
- Strings WordPress has already entity-encoded (post, menu and site titles): `{{ value|esc_html }}`, which doesn't double-encode `&#8217;`.
- URLs in attributes: `|esc_url` or `|e('html_attr')`.
- Macro output and `{% set x %}…{% endset %}` captures are already safe; don't add filters to them.
