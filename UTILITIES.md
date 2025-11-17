# Custom Utilities Guide

This guide documents all custom utilities available in this WordPress theme, built with Tailwind CSS 4. All utilities support Tailwind's responsive variants (`sm:`, `md:`, `lg:`, `xl:`, `2xl:`).

## Table of Contents

- [Fluid Typography](#fluid-typography)
- [Fluid Line Heights](#fluid-line-heights)
- [Fluid Spacing](#fluid-spacing)
- [Swup Transitions](#swup-transitions)
- [Responsive Examples](#responsive-examples)
- [Best Practices](#best-practices)

---

## Fluid Typography

Fluid typography utilities automatically scale between minimum and maximum font sizes based on viewport width using CSS `clamp()`. Perfect for responsive design without media queries.

### Available Utilities

- `fluid-text-xs` - 12px → 14px
- `fluid-text-sm` - 14px → 16px
- `fluid-text-base` - 16px → 18px
- `fluid-text-lg` - 18px → 20px
- `fluid-text-xl` - 20px → 24px
- `fluid-text-2xl` - 24px → 30px
- `fluid-text-3xl` - 30px → 36px
- `fluid-text-4xl` - 36px → 44px
- `fluid-text-5xl` - 44px → 56px
- `fluid-text-6xl` - 56px → 68px
- `fluid-text-7xl` - 68px → 84px
- `fluid-text-8xl` - 84px → 112px
- `fluid-text-9xl` - 112px → 144px

### Basic Usage

```html
<h1 class="fluid-text-5xl">Large Heading</h1>
<p class="fluid-text-base">Body text that scales smoothly</p>
```

### Responsive Variants

You can override fluid sizes at different breakpoints:

```html
<!-- Starts small, grows at medium, larger at large breakpoints -->
<h1 class="fluid-text-3xl md:fluid-text-5xl lg:fluid-text-7xl">
  Responsive Heading
</h1>

<!-- Different sizes for different breakpoints -->
<p class="fluid-text-sm md:fluid-text-base lg:fluid-text-lg">
  Responsive paragraph text
</p>
```

### Real-World Example

```html
<article>
  <h1 class="fluid-text-4xl md:fluid-text-6xl lg:fluid-text-8xl">
    Article Title
  </h1>
  
  <p class="fluid-text-base md:fluid-text-lg">
    Article introduction that scales beautifully across all devices.
  </p>
  
  <div class="fluid-text-sm">
    <time>Published: January 2024</time>
  </div>
</article>
```

---

## Fluid Line Heights

Fluid line height utilities provide responsive line spacing that scales with viewport size.

### Available Utilities

- `leading-fluid-tight` - Tight line height (1.1 → 1.25)
- `leading-fluid-snug` - Snug line height (1.2 → 1.35)
- `leading-fluid-normal` - Normal line height (1.4 → 1.55)
- `leading-fluid-relaxed` - Relaxed line height (1.5 → 1.65)

### Basic Usage

```html
<p class="fluid-text-base leading-fluid-normal">
  Comfortable reading line height
</p>

<h2 class="fluid-text-3xl leading-fluid-tight">
  Tight heading line height
</h2>
```

### Responsive Variants

```html
<!-- Adjust line height at different breakpoints -->
<p class="leading-fluid-normal md:leading-fluid-relaxed">
  More relaxed spacing on larger screens
</p>

<!-- Combine with fluid typography -->
<h1 class="fluid-text-4xl leading-fluid-tight md:leading-fluid-normal lg:leading-fluid-relaxed">
  Heading with responsive line height
</h1>
```

### Typography Pairing Examples

```html
<!-- Headings typically use tighter line heights -->
<h1 class="fluid-text-5xl leading-fluid-tight">
  Main Heading
</h1>

<!-- Body text uses normal or relaxed -->
<p class="fluid-text-base leading-fluid-normal">
  Body text with comfortable line spacing for readability.
</p>

<!-- Long-form content benefits from relaxed spacing -->
<article class="fluid-text-lg leading-fluid-relaxed">
  Long-form article content that's easy to read...
</article>
```

---

## Fluid Spacing

Fluid spacing utilities provide margin and padding that scales smoothly with viewport width. Available in both margin (`m-fluid-*`) and padding (`p-fluid-*`) variants.

### Available Utilities

#### Margin Utilities
- `m-fluid-xs` - Extra small margin
- `m-fluid-sm` - Small margin
- `m-fluid-md` - Medium margin
- `m-fluid-lg` - Large margin
- `m-fluid-xl` - Extra large margin
- `m-fluid-2xl` - 2X large margin

#### Padding Utilities
- `p-fluid-xs` - Extra small padding
- `p-fluid-sm` - Small padding
- `p-fluid-md` - Medium padding
- `p-fluid-lg` - Large padding
- `p-fluid-xl` - Extra large padding
- `p-fluid-2xl` - 2X large padding

#### Legacy Utilities (Backward Compatibility)
- `space-fluid-xs` through `space-fluid-2xl` - Applies margin (use `m-fluid-*` for clarity)

### Basic Usage

```html
<!-- Padding -->
<section class="p-fluid-md">
  Content with fluid padding
</section>

<!-- Margin -->
<div class="m-fluid-lg">
  Element with fluid margin
</div>
```

### Responsive Variants

```html
<!-- Increase spacing at larger breakpoints -->
<section class="p-fluid-sm md:p-fluid-md lg:p-fluid-lg">
  Responsive padding that grows with screen size
</section>

<!-- Different spacing for different breakpoints -->
<div class="m-fluid-xs md:m-fluid-lg lg:m-fluid-xl">
  Responsive margin
</div>
```

### Directional Variants

Combine with Tailwind's directional utilities:

```html
<!-- Top padding -->
<div class="pt-fluid-md md:pt-fluid-lg">
  Top padding only
</div>

<!-- Horizontal padding -->
<section class="px-fluid-sm md:px-fluid-md lg:px-fluid-lg">
  Horizontal padding
</section>

<!-- Vertical margin -->
<div class="my-fluid-md md:my-fluid-xl">
  Vertical margin
</div>
```

### Real-World Examples

```html
<!-- Card component -->
<div class="p-fluid-md md:p-fluid-lg lg:p-fluid-xl">
  <h2 class="fluid-text-3xl mb-fluid-sm">Card Title</h2>
  <p class="fluid-text-base">Card content</p>
</div>

<!-- Section spacing -->
<section class="py-fluid-lg md:py-fluid-xl lg:py-fluid-2xl">
  <div class="container mx-auto px-fluid-sm md:px-fluid-md">
    Section content with responsive spacing
  </div>
</section>

<!-- Grid gaps -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-fluid-md md:gap-fluid-lg">
  <div>Item 1</div>
  <div>Item 2</div>
</div>
```

---

## Swup Transitions

Swup transition utilities provide smooth page transition effects when using the Swup library for page navigation.

### Available Utilities

- `swup-transition-fade` - Fade opacity transition
- `swup-transition-slide` - Slide transform transition
- `swup-transition-scale` - Scale and fade transition

### Basic Usage

```html
<!-- Apply to main content container -->
<main class="swup-transition-fade">
  Page content that fades during transitions
</main>

<!-- Slide effect -->
<div class="swup-transition-slide">
  Content that slides during page transitions
</div>

<!-- Scale effect -->
<article class="swup-transition-scale">
  Content that scales during transitions
</article>
```

### Responsive Variants

```html
<!-- Different transitions at different breakpoints -->
<main class="swup-transition-fade md:swup-transition-slide lg:swup-transition-scale">
  Responsive transition effects
</main>
```

### Loading States

The utilities automatically work with Swup's loading states:

```html
<!-- When Swup adds .is-changing class, transitions activate -->
<div id="swup" class="swup-transition-fade">
  <!-- Content fades to 70% opacity during transition -->
</div>
```

### Complete Example

```html
<body>
  <div id="swup" class="swup-transition-fade">
    <header class="swup-transition-slide">
      Header content
    </header>
    
    <main class="swup-transition-scale">
      Main content
    </main>
    
    <footer class="swup-transition-fade">
      Footer content
    </footer>
  </div>
</body>
```

---

## Responsive Examples

### Complete Component Example

```html
<!-- Hero Section -->
<section class="py-fluid-xl md:py-fluid-2xl">
  <div class="container mx-auto px-fluid-md md:px-fluid-lg">
    <h1 class="fluid-text-4xl md:fluid-text-6xl lg:fluid-text-8xl leading-fluid-tight mb-fluid-md">
      Hero Title
    </h1>
    <p class="fluid-text-lg md:fluid-text-xl leading-fluid-normal mb-fluid-lg">
      Hero description text
    </p>
    <a href="#" class="inline-block p-fluid-sm md:p-fluid-md">
      Call to Action
    </a>
  </div>
</section>
```

### Card Grid Example

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-fluid-md md:gap-fluid-lg">
  <article class="p-fluid-md md:p-fluid-lg">
    <h2 class="fluid-text-2xl md:fluid-text-3xl leading-fluid-tight mb-fluid-sm">
      Card Title
    </h2>
    <p class="fluid-text-sm md:fluid-text-base leading-fluid-normal">
      Card content
    </p>
  </article>
  <!-- More cards... -->
</div>
```

### Typography Scale Example

```html
<article class="max-w-4xl mx-auto p-fluid-lg">
  <h1 class="fluid-text-4xl md:fluid-text-6xl lg:fluid-text-7xl leading-fluid-tight mb-fluid-md">
    Article Title
  </h1>
  
  <p class="fluid-text-base md:fluid-text-lg leading-fluid-normal mb-fluid-lg">
    Article introduction paragraph.
  </p>
  
  <h2 class="fluid-text-2xl md:fluid-text-3xl lg:fluid-text-4xl leading-fluid-tight mb-fluid-sm mt-fluid-xl">
    Section Heading
  </h2>
  
  <p class="fluid-text-sm md:fluid-text-base leading-fluid-normal">
    Body text for the article section.
  </p>
</article>
```

---

## Best Practices

### 1. Start Mobile-First

Always define the base (mobile) size first, then add responsive variants:

```html
<!-- ✅ Good -->
<h1 class="fluid-text-3xl md:fluid-text-5xl lg:fluid-text-7xl">

<!-- ❌ Avoid -->
<h1 class="fluid-text-7xl lg:fluid-text-3xl">
```

### 2. Combine Utilities Thoughtfully

Pair fluid typography with appropriate line heights:

```html
<!-- ✅ Good - Tight for headings -->
<h1 class="fluid-text-5xl leading-fluid-tight">

<!-- ✅ Good - Normal/relaxed for body -->
<p class="fluid-text-base leading-fluid-normal">
```

### 3. Use Consistent Spacing Scale

Maintain visual rhythm by using consistent spacing utilities:

```html
<!-- ✅ Good - Consistent scale -->
<section class="py-fluid-lg">
  <div class="mb-fluid-md">...</div>
  <div class="mb-fluid-lg">...</div>
</section>
```

### 4. Leverage Responsive Variants

Use responsive variants to create truly fluid designs:

```html
<!-- ✅ Good - Progressive enhancement -->
<div class="p-fluid-sm md:p-fluid-md lg:p-fluid-lg xl:p-fluid-xl">
```

### 5. Test Across Breakpoints

Always test your utilities across all breakpoints:
- Mobile (< 640px)
- Tablet (640px - 1024px)
- Desktop (1024px+)
- Large Desktop (1280px+)

### 6. Performance Considerations

- Fluid utilities use CSS `clamp()` which is highly performant
- No JavaScript required for responsive behavior
- Works seamlessly with Tailwind's purge/JIT system

---

## Breakpoint Reference

Tailwind's default breakpoints (used with responsive variants):

- `sm:` - 640px and up
- `md:` - 768px and up
- `lg:` - 1024px and up
- `xl:` - 1280px and up
- `2xl:` - 1536px and up

Custom breakpoints defined in this theme:

- `xs:` - 360px (if configured)
- `3xl:` - 1920px (if configured)

---

## Troubleshooting

### Utilities Not Working?

1. **Check Build Process**: Ensure Vite/build process has run
2. **Verify Import**: Confirm `modules-utilities.css` is imported in `app.css`
3. **Check Layer**: Utilities should be in `layer(utilities)`
4. **Browser Support**: Ensure browser supports CSS `clamp()` (all modern browsers)

### Responsive Variants Not Applying?

1. **Syntax Check**: Ensure correct format: `md:fluid-text-lg` (not `md-fluid-text-lg`)
2. **Build Cache**: Clear build cache and rebuild
3. **Specificity**: Check for conflicting styles with higher specificity

### Need Help?

- Check Tailwind CSS 4 documentation: https://tailwindcss.com/docs
- Review `src/app.css` for theme configuration
- Check `src/modules/css/modules-utilities.css` for utility definitions

---

## Summary

All custom utilities in this theme:

✅ Support Tailwind's responsive variants (`sm:`, `md:`, `lg:`, `xl:`, `2xl:`)  
✅ Use CSS `clamp()` for smooth, performant scaling  
✅ Work seamlessly with Tailwind's utility system  
✅ Are fully compatible with Tailwind CSS 4  

Happy coding! 🎨

