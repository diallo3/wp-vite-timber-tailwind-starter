# Theme Audit & Roadmap

Audit date: 2026-10-06 · Branch at audit: `d2f29a2`

Installed versions at audit: Timber 2.3.3, Twig 3.22, Vite 7.3.1, Tailwind 4.1.18, DaisyUI 5.5.16, Alpine 3.15.6, Motion 12.29.2, Swup 4.8.2.

Work is grouped into phases. Phase 0 is "currently broken"; each later phase assumes the previous one is done. Every item has a checkbox so this file can double as the tracker.

## Decisions (2026-10-06)

| Topic | Decision |
| --- | --- |
| Page transitions | Remove Swup; use native CSS `@view-transition` (done in 0.2) |
| UI system | Custom Tailwind components; remove DaisyUI (2.9) |
| Existing ACF content | Dev/starter content only, so field structures can change freely; no migrations needed (2.1, 2.3) |
| CPT / taxonomy source of truth | Stay in ACF UI / `acf-json` (2.7) |
| Blocks | Not yet decided; Classic Editor assumed (2.8) |

---

## Phase 0 — Broken right now ✅ done 2026-10-06

Verified with: `npm run build` (clean apart from an upstream DaisyUI `@property` warning); `php -l` on changed PHP; a Twig parse + include-resolution check of every template; HTTP smoke test on `vite-starter.local` (home, search, 404, author, single post, `/all-page-content/`). Results: no PHP/Twig errors, no `console.log` output, new hashed assets served.

- [x] **0.1 Production build fails.** `npm run build` errors with `` `@utility` cannot be nested ``. `src/app.css` lines 92–102 declare `@utility fluid-text-*` inside `@layer base`. These are also duplicates of the ones in `src/modules/css/modules-utilities.css`. **Fix:** delete them from `app.css`. The committed `dist/` was built before this change, so the site only *looks* fine locally.
- [x] **0.2 Swup does a double request and a full reload on every link click.** *Resolution: Swup removed (`module-swup.js`, package, CSS, `#swup` id). Replaced with `@view-transition { navigation: auto; }` in `src/app.css`, wrapped in `prefers-reduced-motion: no-preference`. `<main>` is now `id="main"`.* Original findings:
  - `containers: ['#swup', '#main', 'main', '.site-main']` (`src/modules/js/module-swup.js:20`): `#main` and `.site-main` don't exist. Swup 4 requires *every* container, so it throws "Container mismatch" and falls back to `window.location.assign`.
  - `swup.on('contentReplaced', …)` is the v2/v3 API. Swup 4 has no `.on()`, so the call throws right after the instance is created. The `try/catch` swallows the error, so link interception stays on but **no re-init hooks are registered**.
  - `<main id="swup" class="… transition-fade …">` (`layout-base.twig:54`) doesn't match `animationSelector: '[class*="swup-transition-"]'`, so there is no transition either.
  - **Fix:** `containers: ['#swup']`, move to `swup.hooks.on('content:replace' | 'page:view', …)`, use the class `swup-transition-fade` on `<main>`, and drop the removed options (`debugMode`, `formSelector`). Drop `Alpine.initTree()`, because Alpine 3 auto-initialises injected DOM through its MutationObserver, so calling it can double-init. Drop the Headroom re-init too: the header sits outside the container and `window.Headroom` is never set.
- [x] **0.3 ACF data is dumped to the browser and can break the page.** `<script>console.log({{ component | json_encode | raw }})</script>` appears in `flexible-content.twig:5` and in 6 components (`section_content_post_carousel`, `section_content_key_list`, `section_featured_statistics`, `section_header_complex`, `section_contact_form`, `section_media_video`). Any WYSIWYG value containing `</script>` breaks out of the tag (XSS / broken markup), and every visitor sees the full field payload. **Fix:** remove them all; use `{{ dump() }}` behind `WP_DEBUG` if needed.
- [x] **0.4 Templates that don't resolve.** *Resolution: paths fixed and the dead `alert` block removed. `search.twig` and `author.twig` now extend `@Pages/index/index.twig`, because their `tease*.twig` includes didn't exist. Also found and fixed: `single.twig` included non-existent `comment.twig` / `comment-form.twig` (comments are disabled in admin), so that block was removed.*
  - `404.php:12` renders `'404.twig'`; the real file is `pages/404/404.twig`. The file also lacks `use Timber\Timber;`.
  - `footer.php:23` renders `'page-plugin.twig'`; the real file is `pages/page-plugin/page-plugin.twig`.
  - `pages/404`, `single-password`, `search`, `author`, `page-plugin` all `{% extends "layouts/layout-base.twig" %}`, which isn't a valid path. Should be `@Layouts/layout-base.twig`.
  - `search.twig:11` includes `components/pagination/pagination.twig` and `layout-base.twig:45` includes `components/alert/alert.twig`. Neither exists (pagination lives at `@Layouts/archives/partials/_partial-pagination-default.twig`).
  - Absolute includes `"/templates/app/…"` in `_embed-page-with-header.twig:7`, `section_content_with_image/index.twig:42` and similar. Should be namespaced (`@Layouts/…`).
- [x] **0.5 Possible fatal on CPT registration.** `post_type_archives()` (`lib/functions/lib-utilities.php:67-77`) runs `in_array($post_type, get_field('has_post_type_archive','option'))`. When the option is empty, `get_field` returns `null`, which throws a `TypeError` on PHP 8. This filter also runs for every post type, possibly before ACF is ready. **Fix:** `(array) get_option(...)` / `is_array()` guard, cached once.
- [x] **0.6 ACFE frontend render filter returns `null`.** `acfe/flexible/render/template` (`lib/acf/lib-flexible-content.php:66-73`) returns nothing when `$is_preview` is false. **Fix:** `return $template;` in the else branch.
- [x] **0.7 (found during fixes) Invalid selector `.acf-input, {`** in `src/modules/css/modules-acf.css:61` made the CSS optimiser drop that rule. Fixed.

### Found during Phase 0 verification (not yet fixed)
- [ ] **0.8 Stale flexible rows on `/company/`.** The page has saved rows whose layout names no longer exist in the field group. ACFE Pro warns (`compatibility-acf-6.5.php:219`) and the theme prints "Unknown layout:". This was already happening before the Phase 0 changes. Since content is dev-only, re-save or rebuild the page. 1.12 will hide the message on production.
- [ ] **0.9 Single posts ignore their flexible content.** `Post Content Fields` attaches `page_content` to posts, but `single.twig` renders only `post.content`. Address together with 2.1 / 2.2.

## Phase 1 — Correctness bugs

### PHP / WordPress
- [ ] **1.1 Vite dev detection runs a blocking cURL (10 s timeout) on every request**, including admin, AJAX, REST and production (`lib/functions/lib-vite.php:22-61`). It also reads `.env` with `parse_ini_file` on every request. **Fix:** only check when `wp_get_environment_type() === 'local'`, or switch to a "hot file" written by a small Vite plugin (Laravel-Vite style) and `file_exists()` it. Add `type="module"` to the built script (filter `script_loader_tag`), and also inject `@vite/client` in dev. `editor_css` is never populated, so `enqueueEditorStyles()` is a no-op.
- [ ] **1.2 `site_logo()` image path is broken on nested URLs.** It outputs `<img src="wp-content/uploads/…">` (relative, no leading slash) (`lib-utilities.php:51-62`). It also uses a hardcoded `alt`. **Fix:** use `wp_get_attachment_image()`, and inline SVG via `get_attached_file()`.
- [ ] **1.3 `svg_code()` turns off SSL verification globally for libxml when `WP_DEBUG` is on.** Load the local file path instead; no stream context needed.
- [ ] **1.4 Duplicate `<title>`.** `add_theme_support('title-tag')` is enabled *and* `layout-base.twig:6` hardcodes `<title>`. The hardcoded `meta description` is the site tagline on every page. Remove both and let WP/SEO plugin handle them. Hardcoded `lang="en"` should be `{{ site.language_attributes }}`.
- [ ] **1.5 Menu locations mismatch.** `footer_utility` is read into context (`lib-timber.php:46`) but never registered (`:122-129`).
- [ ] **1.6 Debug/demo leftovers.** `die('ADMIN CONTEXT FROM …')` in `index.php`/`page.php`; `foo/stuff/notes` context vars and `myfoo` filter (`lib-timber.php`); `x-init="console.log('Im initing')"` on `<body>`; `error_log()` on every `template_exists()` call (`lib/timber/lib-timber-filters.php:28`); `console.log` in all JS modules; `templates/test.twig`; `Timber::$dirname` includes non-existent `views`.
- [ ] **1.7 `template_exists()` builds a new `FilesystemLoader` rooted at `templates/app` per call**, which disagrees with the real Timber loader and namespaces. **Fix:** use `$twig->getLoader()->exists('@Content/'.$name.'/index.twig')`.
- [ ] **1.8 Raw `post.meta('css')` / `post.meta('js')` injected into `<head>`/footer** (`layout-base.twig:30-32, 66-68`). If kept, restrict to an ACF field gated by `unfiltered_html`; otherwise remove. Note: Timber's Twig `autoescape` is **off** (`vendor/timber/timber/src/Loader.php:329`), so every `{{ }}` prints raw. Consider enabling it (`Timber::$autoescape = 'html'`) and marking WYSIWYG output `|raw` explicitly.
- [ ] **1.9 Theme Options are mostly unused.** `tracking.tracking_codes` (header/footer scripts) is never output; `general_call_to_action.hubspot_*` is unused. Either wire them up (`wp_head`/`wp_footer` hooks) or remove the fields.

### Twig
- [ ] **1.10 `_embed-section-full.twig:23-28`:** `padding_inline` is looked up using itself before it's set, so it is always `px-8` and the editor's "padding inline" choice is ignored. Should read `section.padding_inline`.
- [ ] **1.11 `_embed-section-component-helper.twig`** builds `section_classes` twice (lines 30-36 are dead), and uses `loop.index` outside a loop for IDs (66, 68). Pass an `index` in from `flexible-content.twig`.
- [ ] **1.12 `flexible-content.twig:25`** prints `{{ layout }}` (undefined) instead of `{{ name }}` for unknown layouts. Hide that message on production.
- [ ] **1.13 Header:** duplicate `id="headerActions"` (`_global-header-main.twig:35,37`); `aria-controls="tkMobileNav"` points at nothing; a stray `border` class; header scroll state is reimplemented in Alpine *and* Headroom. Pick one.
- [ ] **1.14 Dynamic Tailwind classes never get generated:** `bg-{{ options.color }}-950 text-{{ options.color }}-600` in `_macro-badge.twig:22`; `text-dodger-blue-600` uses a colour that isn't defined in `@theme`. Use a lookup map of full class names.

### ACF / ACFE
- [ ] **1.15 Post flexible content is out of sync with Pages.** In `group_672065bd6ea21` (Post Content Fields), layout `section_header_complex` names its sub-field `header_complex`. Every template reads `component.content`, so that layout renders empty on posts and in previews.
- [ ] **1.16 Inconsistent sub-field names break the shared helper:** `options_copy` (Customer Story, `group_671e7986625eb`) and `sections_copy2` (Auto Columns). The section helper reads `component.options` / `component.sections`.
- [ ] **1.17 ACFE preview (`lib/acf/layout-preview.php`)** only passes `content` and hardcodes a fallback layout. It loads CSS from the *first* manifest entry and no JS, so Alpine-driven layouts (carousel, FAQ, modals) are dead in preview. The dev script runs `vite build --watch` alongside the dev server just to keep that CSS fresh. Also unify this with the frontend renderer (see 2.2).

### JS
- [ ] **1.18 Motion 12 option names:** `easing` → `ease` (`module-motionOne.js`). `generalInView`'s exit callback animates opacity `[0,1]` (no-op); `staggerInView` fades the *container* out but not the items. `navHeader()` targets `.site-header__logo`, `.menu-header-nav li`, `.site-header__cta`, none of which exist in the header markup.
- [ ] **1.19 DaisyUI themes `wordpress` / `wordpress-dark` are never defined**, so the theme store sets `data-theme` to a non-existent theme. `@utility menu { @apply p-0 }` (`modules-utilities.css:221`) overrides DaisyUI's `.menu` component. `toastComponent` persists notifications to localStorage while their `setTimeout`s are lost on reload, so toasts can get stuck.
- [ ] **1.20 Admin-only ACF styles ship in the public bundle.** `modules-acf.css` styles `ul.acf-checkbox-list` etc. but is imported in `app.css` (frontend). Meanwhile the admin enqueue (`custom_admin_styles`) is commented out and `lib/acf/css/admin.css` (210 lines) is unused. Iconify is loaded in admin from a CDN *and* bundled for the frontend.

## Phase 2 — Consolidation (largest long-term win)

- [ ] **2.1 One flexible content definition.** Pages (`group_66932fca3d392`) and Posts (`group_672065bd6ea21`) each define the same 22 layouts and have already drifted (1.15). **Plan:** keep one group, add an OR location rule for `post_type == post`, delete the other. Content is dev-only, so no migration is needed; re-save or re-seed affected posts.
- [ ] **2.2 One render path for frontend + ACFE preview.** Replace `render_acf_flexible_content()` + `flexible-content.twig` + `layout-preview.php` with a single `render_layout($name, $data, $is_preview, $index)` helper used by both. Use ACFE's `acfe/flexible/render/template` and `acfe/flexible/render/enqueue` hooks with `@Content/{layout}/index.twig`.
- [ ] **2.3 Extract repeated ACF field sets into Components.** ~20 component groups each copy-paste the same `sections` toggles + `accent/title/entry` + `call_to_actions` repeater (`link/style/size`). Create two components alongside the existing **Component Options** (`group_670c46d117551`): **Section Intro** and **CTA Group**. Then nest them. This changes meta keys (`content_0_title` → `content_0_intro_0_title`). Content is dev-only, so restructure freely and rebuild `/all-page-content/` as the reference page.
- [ ] **2.4 Shared Twig partials.** Each of 4 components has its own `_partial-section-header.twig` and `_partial-section-ctas.twig` (all 8 files differ slightly). Every component re-imports the same 5–6 macros. Make one `@Elements/section/_intro.twig` + `_ctas.twig`, and import macros once in the section helper (macros don't inherit into embeds, so pass them or use a global Twig function).
- [ ] **2.5 Cards: finish the migration.** `_macro-card.twig` (422 lines) was meant to replace `components/cards/*`, but 5 components still include the old card files. `_card-migration-examples.twig` (270 lines) and `_base-with-macros.twig` are docs living in `templates/`. Pick the macro, migrate the 5 call sites, delete the old cards and move examples to docs.
- [ ] **2.6 Remove orphaned ACF groups.** These are referenced by nothing: *Component Posts Types* (`group_66987552a6ada`, service/modality fields from another project), *Component Auto Columns* (`group_6699aaf990dfa`), *Component Contact with Form* (`group_6697d7abbda70`, superseded by Form Info), *Block – Testimonial* (`group_6560eb8c9b985`, block on a Classic Editor site), inactive *Section Options* (`group_66be4df988b01`). Keep *Component Starter* only if documented as the scaffold. Template `section_action_button` has no layout.
- [ ] **2.7 Move CPTs/taxonomies into code (optional but recommended).** The 4 post types (`solution`, `team-member`, `testimonial`, `call-to-action`) live only in ACF JSON. `solution` has an archive but no templates; `call-to-action` is public with no single template (should be `publicly_queryable: false`). There are no custom taxonomies yet. `StarterTimber::register_post_types/taxonomies` are empty stubs. **Decision: ACF stays the source of truth.** Delete the empty stubs and fix the CPT settings in the ACF UI. Also add Timber class maps (`timber/post/classmap`) for each CPT so templates get typed helpers.
- [ ] **2.8 Classic Editor cleanup.** Classic Editor is in use, so these are dead: `palmiak/timber-acf-wp-blocks` (dev-master, its template dir `templates/components` doesn't exist), `block_categories_all` filter, `wp-block-styles`/`align-*` supports, unused `post-formats`. Remove, or decide to support blocks.
- [ ] **2.9 One UI system.** DaisyUI, hand-rolled Tailwind components, `@tailwindplus/elements` (imported in `main.js`, unused in templates) and `tailwindcss-fluid-type` (a v3 plugin, unused) all overlap. Brand tokens are hardcoded in `page-brand-guidelines.php` and not tied to `@theme`. **Decision: custom Tailwind.** Remove `@plugin "daisyui"`, the theme-selector component and the DaisyUI theme logic in the Alpine `theme` store. Replace DaisyUI classes (`btn`, `card`, `label-text`, `menu`, …; mostly in `page-brand-guidelines.twig` and `_macro-card.twig`'s `daisyui_card`). Define brand colours/fonts once in `@theme` and have the brand page read from them.
- [ ] **2.10 Header/nav state.** `$store.navigation` exists but the header uses local `mobileNavOpen`. Pick one (store is better if anything else needs to close the menu).

## Phase 3 — Dependencies, build & tooling

- [ ] **3.1 Pin versions & commit the lockfile.** `vite`, `sass`, `dotenv`, `vite-plugin-*`, `baselayer.css`, `js-cookie` are `"latest"`, and `package-lock.json` is in `.gitignore`. Builds aren't reproducible. Remove from `.gitignore`, pin ranges.
- [ ] **3.2 Remove unused packages:** `baselayer.css`, `js-cookie`, `tailwindcss-fluid-type`, `@iconify/tailwind`, `sass` (no `.scss` files exist), `@tailwindplus/elements` (unless adopted in 2.9). Composer: `palmiak/timber-acf-wp-blocks`; `twig/twig` is redundant (Timber requires it).
- [ ] **3.3 Use `@tailwindcss/vite`** instead of the PostCSS plugin (faster, official for Vite) and drop `postcss.config.js`. Add `@source` rules for `templates/**/*.twig` and `lib/**/*.php` so class detection is explicit.
- [ ] **3.4 Fix `npm run dev`.** `vite & vite build --watch` leaves an orphaned watcher on Ctrl-C and writes `dist/` constantly. Once 1.17 is fixed (preview loads from the dev server), `dev` should just be `vite`. Also: `server.origin` is hardcoded `http://` while the protocol is `https` (`vite.config.js:36`).
- [ ] **3.5 `dist/` hygiene.** `dist/.vite/manifest.json` and `dist/icons/*` are committed but `dist/assets/` is ignored, so a fresh clone references files that don't exist. Either ignore all of `dist/` and build in deploy, or commit all of it. Move icons to be imported/copied by Vite from `public/` (already happens) and stop committing the copy.
- [ ] **3.6 `.env` → `.env.example`** (commit the example, ignore the real one). `WP_ENQUEUE_ID=bourbon` is a leftover from another project. `style.css` has an empty `Text Domain` while PHP uses `'StarterTimber'`; pick one slug. Also delete `.DS_Store` and decide whether `.idea/` and `.vscode/` belong in git.
- [ ] **3.7 Add guardrails:** a CI step (`npm run build` + `composer validate` + `php -l`), and optionally PHPStan / PHPCS-WordPress and Prettier + `prettier-plugin-twig`. Phase 0 issue 0.1 would have been caught by a build check. `phpunit.xml` exists with no tests; remove it or add a smoke test.

## Phase 4 — Documentation

- [ ] **4.1 Rewrite `CLAUDE.md` (29 KB) to match reality.** It documents things that don't exist or are wrong: a "three-file SCSS" component pattern (no SCSS files), the DaisyUI integration (being removed, 2.9), the entire Swup section (Swup was removed in 0.2), fixed version numbers, and sample component APIs that aren't used in templates. Keep it short: commands, directory map, render flow, conventions, and links to the docs below.
- [ ] **4.2 `README.md`** is still the upstream boilerplate (author `deepspacerobots`, SCSS section). Replace it with project-specific setup (Local WP, ACF Pro/ACFE Pro/Component Field licences, `.env`).
- [ ] **4.3 New docs:**
  - `docs/flexible-content.md`: how a layout flows from ACF field group → component field → Twig template → ACFE preview; checklist for adding a new layout (field group, thumbnail SVG in `lib/acf/images/layout-thumbnails/`, `@Content/{name}/index.twig`).
  - `docs/acf-conventions.md`: naming (`section_*`, `content`, `options`), shared components (Intro, CTA Group, Options), JSON sync workflow, migration notes for 2.1/2.3.
  - `docs/frontend.md`: Tailwind tokens, UI system decision (2.9), Alpine components actually in use, Motion classes (`inview-*`, `data-scroll-animate`), native view transitions (how to name elements with `view-transition-name`).
  - Fold `UTILITIES.md` into `docs/frontend.md`.

---

## Open decisions

- **Blocks:** stay Classic Editor only (2.8)? Every other decision is recorded at the top of this file.
