## Design System

Follow Notion-inspired minimalist design principles for all pages.

### Color Palette

- Background: `bg-white dark:bg-neutral-900`
- Cards/Sections: `bg-neutral-100 dark:bg-neutral-800` with `rounded-xl`
- Primary text: `text-neutral-900 dark:text-white`
- Secondary text: `text-neutral-500 dark:text-neutral-400`
- Muted labels: `text-neutral-400 dark:text-neutral-500`
- Success/Accent: `green-500`, `green-600`
- Borders: `border-neutral-300 dark:border-neutral-600`

### Typography

- Headings: `text-lg` or `text-2xl` with `font-bold` or `font-semibold`
- Body text: `text-sm` (14px) with `leading-5`
- Small text/labels: `text-xs` (12px)
- Always use `font-medium` for interactive elements

### Spacing & Layout

- Page container: `max-w-2xl mx-auto px-4 py-6`
- Section spacing: `mb-6`
- Card padding: `px-4 py-2`
- Gap in flex: `gap-2`, `gap-3`

### Interactive Elements

- Hover states: Use `hover:bg-neutral-100 dark:hover:bg-neutral-800`
- Transitions: `transition-all duration-150` or `duration-200`
- Cursor: `cursor-pointer` on clickable elements
- Completed items: `opacity-60`, `line-through` on text

### Alignment Patterns

- Checkbox + text: Wrap checkbox in `h-5 flex items-center`, use `leading-5` on text
- Baseline alignment: Use `items-baseline` for mixed font sizes
- Fixed elements: Use `flex-shrink-0`

### Component Patterns

- Cards: `bg-neutral-100 dark:bg-neutral-800 rounded-xl px-4 py-4`
- Buttons: `px-3 py-1.5 text-xs font-medium rounded-full`
- Icon buttons: `w-10 h-10 rounded-lg flex items-center justify-center`
- Empty states: Center with icon, title, and description

### Dark Mode

- Always provide dark mode variants using `dark:` prefix
- Test both modes visually
- Green shades invert: `green-600 dark:green-500`, `green-200 dark:green-900`

### Animations

- Animations must be smooth, subtle, and non-intrusive — they should enhance the user experience, not distract from it.
- Use entrance animations (`animate-fade-in-up`, `animate-stagger`) for content that appears on page load or state change.
- Use `transition-all duration-150` or `duration-200` for interactive feedback (hover, focus, toggle).
- Use Alpine `x-transition` for elements that show/hide dynamically (modals, dropdowns, banners).
- Use `x-collapse` for expandable/collapsible sections.
- Hover effects on small grid cells: `hover:scale-125` or `hover:scale-150` with `duration-150`.
- Never add animation to static informational elements that don't change (legends, table headers).
- Prefer CSS animations and Tailwind utilities over JavaScript-driven animations.
- Keep durations between 120ms–400ms. Anything longer feels sluggish.
- Always respect `prefers-reduced-motion` — use `motion-safe:` prefix when adding non-essential animations.
