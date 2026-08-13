# Project conventions

## Frontend styling

- Put all styles in external CSS files. Never add `<style>` blocks or `style=""` attributes to Blade templates.
- Use page-specific stylesheets under `public/front-theme/styles/pages/` and load them from the Blade view with a versioned `<link>`.
- Express animation delays and visual variants through reusable CSS classes, not inline custom properties.
- Reuse the shared typography scale, font families, colors, spacing tokens, and component classes wherever possible.
- Before adding a page-specific font-size override, compare the same heading or text role on the homepage and the established internal reference pages, especially `O nama`.
- Keep equivalent headings, body copy, links, buttons, and CTA components visually consistent across pages and breakpoints.

## Building a new frontend page

- Before writing markup, identify the closest existing page by purpose and layout. Treat the homepage and `O nama` as the primary visual references unless the task names another page.
- Inspect the shared layout, component markup, global CSS, and the reference page's page-specific CSS before creating new selectors.
- Reuse existing Blade components, HTML structure, utility classes, design tokens, and component classes. Do not recreate an established header, title band, card, CTA, newsletter, footer, button, or link pattern.
- Keep Blade focused on structure and data. Put page styling in an external page stylesheet and page behavior in an external page script under `public/front-theme/scripts/`.
- Load page CSS and JavaScript with `asset(...)` and `filemtime(...)` versioning.
- Prefer a small page-specific override over duplicating a complete shared component.

## Visual system

- Use the existing typography roles instead of inventing sizes per page. Equivalent page titles, section titles, card titles, body copy, labels, links, and buttons must share the same font family, size range, weight, line-height, and letter spacing.
- Use `Instrument Sans Variable` for established sans-serif roles and `Bodoni Moda Variable` only for the existing editorial/accent roles.
- Use project color variables and existing navy, cream, and gold values. Do not introduce near-duplicate colors.
- Follow the established content width and centered container system. On desktop, split intro sections 50/50 when they use the central divider pattern, with the divider positioned at the true viewport center.
- For every two-column intro with a central vertical divider, use two equal columns with no grid gap so the divider lands exactly at `50vw`. Create horizontal breathing room with padding inside the right column, never by shifting the column boundary or adding a grid gap.
- Maintain a deliberate vertical rhythm. Spacing above and below comparable content should be visually balanced; avoid a small top gap paired with a large bottom gap.
- Reuse the established asymmetric corner treatment where rounded corners are appropriate. Do not add rounded ends or card containers to elements that are flat in the reference design.
- Keep imagery consistent with the reference page: matching aspect ratio, crop, `object-position`, border treatment, and responsive behavior.

## Components and interaction

- Reuse the standard dark `contact-cta` design for CTA sections. Do not create a page-specific CTA appearance when the standard component fits.
- Do not restyle the global newsletter, header, or footer from a page stylesheet unless the task explicitly requests a global change.
- Use Font Awesome Pro classes for interface and social icons. Match the icon family and weight used by the reference component; do not insert one-off inline SVG icons.
- Use the shared hover-line treatment for editorial text links such as `Opširnije`.
- Keep arrow direction and hover behavior consistent with the same link or button elsewhere on the site. Expansion state alone must not rotate an arrow unless that is already the project-wide pattern.
- Long copy may use an `Opširnije` / `Prikaži manje` disclosure, but the initially visible text must end at a complete sentence when requested by the design.
- Preserve keyboard focus states, semantic headings, labels, meaningful alt text, and reduced-motion behavior.

## Motion

- Match the homepage scroll-reveal behavior: content should reveal when it enters the viewport, not all at once on initial page load.
- Reveal the photo and its related text as one coordinated composition, with a small and uniform stagger between neighboring items.
- Use shared reveal classes and data attributes when possible. Put additional observer logic in an external page script.
- Avoid dark image reveal backgrounds when the page uses the cream editorial surface; use the matching surface color for the reveal curtain.

## Responsive quality

- Design and verify desktop, tablet, and mobile states. At minimum, check a wide desktop, a 1280px desktop, and a narrow mobile viewport.
- Preserve content hierarchy and tap target sizes when a two-column layout collapses to one column.
- Do not solve desktop spacing with fixed values that create excessive gaps or cramped content on mobile. Prefer shared clamps and existing breakpoints.
- Verify that long Croatian names, professional titles, and translated copy wrap without collisions or overflow.

## Verification workflow

- After implementation, reload the local page and compare it visually with its chosen reference page.
- Verify the initial state, scroll reveals, hover/focus states, expandable copy, and responsive layout.
- Run `php -l` for edited Blade/PHP files, `node --check` for edited JavaScript, `git diff --check`, and the narrowest relevant automated test.
- Inspect the final diff for duplicated CSS, inline styles, accidental global overrides, and unrelated generated files.

## Git and generated files

- Preserve unrelated worktree changes. Stage only files intentionally changed for the task.
- Never commit `storage/framework/` generated views or cache files, temporary screenshots, archives, or unrelated ZIP files unless the user explicitly requests them.
