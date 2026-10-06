# ACF conventions

Required plugins: **ACF Pro**, **ACF Extended Pro** (flexible content UI, previews, extra field types), **ACF Component Field** and **Classic Editor**. The block editor isn't used.

## JSON sync

- Field groups, post types and options live in `acf-json/` and are version-controlled. `lib/acf/lib-acf.php` sets it as the save point.
- After pulling changes, go to *ACF → Field Groups*, open the **Sync available** filter and sync. Until you do, the database copy wins in the admin.
- When a group is deleted from `acf-json/`, its database copy stays until you trash it in the admin.
- `npm run lint` checks every file: valid JSON, unique group and field keys, and that every Component Field and conditional-logic rule points at something that exists.
- For scripted edits use `bin/acf-json.cjs` (`read`, `write`, `groups`). It writes in ACF's own format (4-space indent, escaped `/`) and bumps `modified`, so ACF offers the change for sync.

## Field groups

| Kind | Naming | Location |
| --- | --- | --- |
| Flexible content | *Flexible Content Fields* (`page_content`) | pages (not the posts page) and posts |
| Components | *Component <Name>* | none, only used through Component Fields |
| Post type fields | *<Type> Fields* (Team Member, Testimonial) | `post_type == <type>` |
| Options | *Theme Options Fields* | the ACF options page |
| Other | *Mega Menu*, *Post Archive Options* | menu items in `header_nav`; the posts page and the `solution` archive |

Flexible layouts are named `section_<name>` and match `templates/app/components/content/section_<name>/`. Each layout has a single `content` sub field (a Component Field). See [flexible-content.md](flexible-content.md).

## Shared components

Every section component reuses three shared groups through Component Fields rather than copying fields:

- **Component Section Intro** (`group_6ac52e5600005`), field `intro`. A `sections` toggle group (accent, title, sub_title, entry, ctas) plus the matching fields. `entry` is WYSIWYG and `ctas` is a CTA Group. In Twig use `section_intro(component)`, which returns only the parts that are toggled on and filled in.
- **Component CTA Group** (`group_6ac52e5600004`). A `call_to_actions` repeater of `link` (ACF link), `style` (solid / outline / white / link) and `size` (small / default / large). In Twig use `cta_group(value)` and `@Elements/_partial-ctas.twig`. Also used directly by Customer Story and the How it Works steps (`ctas`).
- **Component Options** (`group_670c46d117551`), field `options`. A `section` group with layout, `theme` (default / light / dark), `content_width`, `padding_block` and `padding_inline`.

*Component Starter* (`group_670d94f8f04a6`, inactive) is the scaffold for new components: it has `intro` and `options` already on their tabs.

Component Field values are arrays of rows even when the field isn't repeatable, so templates read `component.content[0]`, `component.options[0]` and so on. `theme_component_row()` in `lib/timber/lib-timber-filters.php` does the same in PHP.

## Post types

Post types are defined in ACF (`acf-json/post_type_*.json`), not in PHP:

| Post type | Public URLs | Used for |
| --- | --- | --- |
| `solution` | yes (archive) | no templates yet |
| `team-member` | no | Featured Team cards (bio modal) |
| `testimonial` | no | Featured Testimonials |

The last two aren't publicly queryable, are excluded from search and are hidden from nav menus. They're only shown through relationship fields. There are no custom taxonomies.

## Theme options

*Theme Options Fields* is on the ACF options page and is available in Twig as `site_options` (not `options`, which the section helper uses for a component's own options):

- **General:** company title, summary, address, phone/email/hours repeaters. The Contact Form section shows them under its intro when *Show company info* is on.
- **Tracking:** `tracking.tracking_codes` rows (name, script, `in_header`). They are printed unescaped in `wp_head` or `wp_footer` by `theme_print_tracking_codes()`, so only admins should have access to the options page.

## Forms

The Contact Form section (*Component Form Info*, `group_671beb0ae9d9e`) has two form types:

- **Shortcode:** the shortcode without brackets (for example `contact-form-7 id="12"`). It's rendered with `do_shortcode`.
- **HubSpot:** portal ID, form ID, region (`na1` / `eu1`) and an optional thank-you message. The page loads HubSpot's `embed/v2.js` and calls `hbspt.forms.create()`. ACFE previews show a placeholder instead of the form.

## Archive headers

*Post Archive Options* (`group_6692d86597f6c`) adds a Header Simple `content` component and a Featured Posts `related` component to the posts page (edit the page set as "Posts page") and to the `solution` archive (ACFE archive page; `solution` must be ticked in Theme Options → "Has Post Type Archive"). `_embed-archive-with-header.twig` renders them with the same templates as the flexible layouts and falls back to an `<h1>` with the archive title.

## Content notes

- Content is dev-only. After a field restructure, rebuild the affected pages instead of migrating. `/all-page-content/` is the reference page with every layout.
- Meta keys follow the field nesting (for example `…_content_0_intro_0_title`). Renaming a field or a Component Field changes the keys and orphans existing values.
