# Flexible content

Pages and posts are built from the `page_content` flexible content field (*Flexible Content Fields*, `group_66932fca3d392`). It's edited with ACFE's modal/grid UI and has a live preview for each layout.

## How a layout renders

```
page_content (flexible content, ACFE)
└─ layout "section_featured_logos"
   └─ content (ACF Component Field → "Component Logo Carousel")
      ├─ intro    (Component Field → "Component Section Intro")
      ├─ …layout-specific fields (logo_sets)
      └─ options  (Component Field → "Component Options")
```

**Frontend:**

1. `page.php` / `single.php` call `render_acf_flexible_content($post_id)` (`lib/acf/lib-flexible-content.php`), which returns `[{component_name, component_data}]` for each row.
2. `templates/app/layouts/flexible-content.twig` loops over them. Every layout after the first is wrapped in `@Layouts/sections/_embed-section-full.twig`, which adds the outer `<section>` spacing. The first layout (usually a hero) is rendered bare.
3. Each row goes through `@Layouts/_flexible-layout.twig` with `name`, `component`, `index` (1-based) and `is_preview`.
4. That includes `@Content/<name>/index.twig`, i.e. `templates/app/components/content/<name>/index.twig`. Unknown layouts show a notice to editors only.

**ACFE preview (admin):**

- The `acfe/flexible/render/template` filter points previews at `lib/acf/layout-preview.php`. It builds the same `name` / `component` / `index` context from the current row and renders the same `_flexible-layout.twig`, with `is_preview: true`.
- `acfe/flexible/enqueue/name=page_content` loads the theme CSS and `src/preview.js` (Alpine) for previews, from the dev server when it's running and from `dist/` otherwise. Alpine components (carousel, FAQ, modals) work in previews; Motion animations don't run there.
- Layout thumbnails come from `lib/acf/images/layout-thumbnails/<name>.svg`, with `default-thumbnail.png` as the fallback.

## Inside a component template

Most components embed `@Layouts/sections/_embed-section-component-helper.twig` and fill its `component_content` block:

```twig
{% set section_class = name|replace({'_': '-'}) %}

{% embed "@Layouts/sections/_embed-section-component-helper.twig" with {
    section_id_name: section_class,
    section_class_name: section_class ~ ' relative isolate overflow-hidden',
} %}
    {% block component_content %}
        {% import "@Elements/_macro-heading.twig" as headings %}

        <div data-{{ section_class }}-content class="{{ section_class }}__content">
            …
        </div>
    {% endblock %}
{% endembed %}
```

`name` is the layout name (`section_header_simple` becomes `section-header-simple`). It is set on both the frontend and the ACFE preview. Import a macro only when this template calls it, inside `component_content`, in this order: icons, headings, badges, images, buttons, cards. Optional CSS next to the template (`*.css`) is imported by the Vite glob in `src/main.js` and `src/preview.js`; scope those rules under `section_class` so they do not apply to the rest of the page.

The helper:

- unwraps the component value (`component = component.content[0]`, because a non-repeatable component field is still an array of rows);
- calls `section_intro(component)` and exposes `accent`, `title`, `sub_title`, `entry`, `ctas` plus `has_*` flags. Each value is `null` when its toggle is off or the field is empty;
- reads `component.options[0].section` (theme, widths, padding) and the optional background image;
- renders the `<section id="<section_id_name>-<index>">` wrapper.

Macros imported outside an `embed` are not visible inside its blocks, so import them inside `component_content`.

CTAs: render `ctas` with `{% include "@Elements/_partial-ctas.twig" with { ctas: ctas } only %}`. For a CTA Group field outside the intro, use `cta_group(component.ctas)` to get the rows.

Escaping: `entry` is WYSIWYG, so print it with `{{ entry|raw }}`. See [frontend.md](frontend.md#escaping-in-twig).

## Adding a layout

1. **Component group.** In ACF, duplicate *Component Starter* (`group_670d94f8f04a6`; it already has the `intro` and `options` component fields on two tabs). Rename it *Component <Name>*, set it to **Active** (the starter itself is inactive) and add the layout's fields. Leave its location rules empty: component groups are only used through Component Fields.
2. **Layout.** In *Flexible Content Fields → page_content*, add a layout named `section_<name>` with one sub field: `content`, a Component Field pointing at the new group.
3. **Template.** Create `templates/app/components/content/section_<name>/index.twig` (start from a similar component). Optional CSS goes next to it as `*.css` and is picked up automatically.
4. **Thumbnail.** Add `lib/acf/images/layout-thumbnails/section_<name>.svg`.
5. **Check.** Run `npm run lint` (it catches missing component keys and template includes). Then add the layout to `/all-page-content/`, the reference page that contains every layout, and check both the frontend and the ACFE preview.

ACF saves field groups to `acf-json/`. Commit those files with the template. See [acf-conventions.md](acf-conventions.md).
