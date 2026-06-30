---
name: OpenHabit
description: The anti-streak habit tracker for people who quit habit trackers.
colors:
  primary-deep: "#14532d"
  primary: "#16a34a"
  primary-bright: "#22c55e"
  welcome-bg: "#fafaf7"
  welcome-bg-dark: "#0c0d0c"
  surface: "#ffffff"
  surface-muted: "#f5f5f5"
  surface-dark: "#171717"
  surface-muted-dark: "#262626"
  ink: "#171717"
  ink-inverse: "#ffffff"
  text-secondary: "#737373"
  text-muted: "#a3a3a3"
  border: "#d4d4d4"
  border-dark: "#525252"
typography:
  display:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "clamp(2.75rem, 8vw, 5rem)"
    fontWeight: 700
    lineHeight: 0.95
    letterSpacing: "-0.035em"
  headline:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "-0.02em"
  title:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.35
    letterSpacing: "-0.01em"
  body:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.43
  label:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 500
    lineHeight: 1.33
rounded:
  sm: "8px"
  md: "12px"
  lg: "16px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "24px"
  page-x: "16px"
  page-y: "24px"
components:
  button-primary:
    backgroundColor: "{colors.ink}"
    textColor: "{colors.ink-inverse}"
    rounded: "{rounded.full}"
    padding: "6px 12px"
  button-primary-dark:
    backgroundColor: "{colors.ink-inverse}"
    textColor: "{colors.ink}"
    rounded: "{rounded.full}"
    padding: "6px 12px"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.text-secondary}"
    rounded: "{rounded.full}"
    padding: "6px 12px"
  card-surface:
    backgroundColor: "{colors.surface-muted}"
    textColor: "{colors.ink}"
    rounded: "{rounded.md}"
    padding: "16px"
  habit-row:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    rounded: "{rounded.md}"
    padding: "8px 16px"
---

# Design System: OpenHabit

## 1. Overview

**Creative North Star: "The Quiet Return"**

OpenHabit's visual system should feel like an app that keeps the door open. The public landing page may carry the brand story, but the brand is still restrained: warm off-white in light mode, near-black green-tinted darkness in dark mode, compact typography, and a rare green accent that marks progress without shouting about it.

The core product UI is quieter than the marketing page. It uses familiar mobile-first app patterns, rounded rows, tab navigation, compact controls, and tonal surfaces. The landing page can use larger type and more generous pacing, but it must not drift into generic SaaS spectacle. The shared tone is calm, forgiving, and open.

The system rejects streak-pressure apps, generic SaaS landing templates, and gamified dopamine UI. If a design makes a missed day look like failure, it is off-brand. If the surface looks like a template hero with metrics, gradients, and repeated feature cards, it is off-brand. If it rewards simple actions with casino-bright celebration, it is off-brand.

**Key Characteristics:**

- Restrained neutral surfaces with one green progress accent.
- Compact, readable sans typography using Instrument Sans throughout.
- Rounded, touch-friendly controls with minimal decoration.
- Tonal layering over heavy shadows.
- Motion that confirms state and respects reduced-motion preferences.

## 2. Colors

The palette is a restrained neutral system with a green accent reserved for completion, progress, active navigation, and brand marks.

### Primary

- **Return Green**: The main action and progress accent. Use it for completed states, progress rings, active mobile navigation, selected settings, and the logo grid.
- **Deep Return Green**: The grounded dark mark color. Use it where the brand needs weight: logo cells, deep accents, and dark tonal ramps.
- **Bright Return Green**: The lightest brand accent. Use sparingly for logo detail, success emphasis, and small positive highlights.

### Neutral

- **Quiet Welcome Surface**: The landing page light background. It is slightly warmer than pure white without becoming cream-first design.
- **Night Welcome Surface**: The landing page dark background. It is darker and greener than neutral-900, giving the public page a calmer nocturnal feel.
- **App Surface**: The main product surface. Use pure white in light mode for task clarity.
- **Muted App Surface**: The product's card, nav rail, and section fill. Use it for contained groups and hover surfaces.
- **Dark App Surface**: The main dark product background.
- **Muted Dark Surface**: The dark-mode panel and card fill.
- **Ink**: Primary text and primary light-mode button background.
- **Inverse Ink**: Dark-mode button text and light-on-dark text.
- **Secondary Text**: Supporting copy, inactive nav, and descriptive labels.
- **Muted Text**: Completed items, helper labels, and low-priority metadata.
- **Neutral Border**: Light-mode dividers, checkbox borders, and menu panels.
- **Dark Border**: Dark-mode dividers and control strokes.

### Named Rules

**The Green Earns Its Place Rule.** Green is state, progress, or brand, not decoration. If a component uses green but does not signal completion, selection, success, or OpenHabit identity, remove it.

**The No Shame Red Rule.** Avoid red as a default missed-day color. Errors can be red when they are true form or system errors, but missed habits are context, not failure.

## 3. Typography

**Display Font:** Instrument Sans (with ui-sans-serif, system-ui, sans-serif fallback)
**Body Font:** Instrument Sans (with ui-sans-serif, system-ui, sans-serif fallback)
**Label/Mono Font:** No separate label or mono family is part of the system.

**Character:** The type system is single-family, compact, and practical. It should feel more like a careful product than a publication, even when the landing page uses larger display sizing.

### Hierarchy

- **Display** (700, clamp scale, tight line-height): Landing hero headlines and public-page narrative moments only. Keep max size at or below 5rem and letter-spacing no tighter than -0.035em.
- **Headline** (700, 1.5rem, 1.2): Page-level section headings, major landing blocks, and high-priority product states.
- **Title** (600, 1.125rem, 1.35): Card titles, settings groups, modal headings, and compact page titles.
- **Body** (400, 0.875rem, 1.43): Habit descriptions, landing copy, settings explanations, and everyday interface text. Keep long prose under 75ch.
- **Label** (500, 0.75rem, 1.33): Navigation labels, button text, metadata, and control labels. Use uppercase only when the label is genuinely structural and short.

### Named Rules

**The Product Sans Rule.** Do not introduce display serifs, mono labels, or decorative type for routine UI. OpenHabit's trust comes from clarity, not typographic costume.

## 4. Elevation

OpenHabit is tonal and mostly flat. Depth comes from neutral surface changes, borders, subtle menu shadows, and state transitions rather than persistent drop shadows. Shadows are allowed for floating menus and modals because those elements must detach from the page; cards and list rows should remain flat at rest.

### Shadow Vocabulary

- **Menu Shadow** (`0 10px 15px -3px rgb(0 0 0 / 0.10), 0 4px 6px -4px rgb(0 0 0 / 0.10)`): Floating language menus and small popovers.
- **Button Shadow** (`0 1px 2px 0 rgb(0 0 0 / 0.05)`): Active desktop tab and rare compact controls that need tactile separation.
- **Modal Shadow** (`0 20px 25px -5px rgb(0 0 0 / 0.10), 0 8px 10px -6px rgb(0 0 0 / 0.10)`): Confirmation and premium modals.

### Named Rules

**The Flat Until Floating Rule.** Static content is flat. Floating UI may cast a shadow only when it must visually escape the page layer.

## 5. Components

OpenHabit components are quiet and tactile: compact enough for daily repetition, rounded enough for touch, and direct enough that users understand the task immediately.

### Buttons

- **Shape:** Pill buttons for primary CTAs and nav controls (9999px radius), rounded rectangles for square icon buttons (8px to 12px radius).
- **Primary:** Neutral ink background with inverse text, 12px horizontal padding, 6px vertical padding, 12px label, medium weight.
- **Hover / Focus:** Shift neutral shade rather than adding decoration. Focus must be visible and high-contrast.
- **Secondary / Ghost:** Transparent background, secondary text, tonal hover surface. Ghost buttons must not look disabled.

### Chips

- **Style:** Compact rounded pills with neutral or green-tinted fills. Selected chips use green text and a subtle green-tinted background.
- **State:** Selected, inactive, and hover states should be distinguishable by text weight, background tint, and icon presence, not color alone.

### Cards / Containers

- **Corner Style:** Soft rounded containers (12px to 16px radius).
- **Background:** Muted neutral surfaces in the app, warm welcome surface on the landing page, dark neutral surfaces in dark mode.
- **Shadow Strategy:** Flat by default. Use borders and tonal contrast first.
- **Border:** Hairline neutral borders for menus, dividers, and inputs.
- **Internal Padding:** Product cards use 16px padding. Habit rows are tighter: 8px vertical, 16px horizontal.

### Inputs / Fields

- **Style:** Neutral background, rounded edges, readable 14px text, and enough vertical padding for touch.
- **Focus:** Clear border or ring change using the green accent or strong neutral focus treatment.
- **Error / Disabled:** Error copy should be direct and non-punitive. Disabled controls should reduce contrast without becoming unreadable.

### Navigation

- **Style, typography, default/hover/active states, mobile treatment.** Desktop uses a rounded segmented tab bar on a muted surface. Active tabs become a raised white or dark-neutral segment with medium text. Mobile uses a fixed bottom tab bar with 24px icons, 12px labels, and green active state. The mobile bar must respect safe-area padding.

### Habit Row

Habit rows are the signature product primitive. The row is a rounded hover surface with an 18px checkbox, 14px medium habit text, optional 12px description, and a green completed state. Completed items use muted text and line-through, but they should still remain readable.

### Activity Grid

Activity grids show the long view without moralizing it. Cells can scale subtly on hover and use green intensity to indicate activity, but empty or missed cells stay neutral rather than punitive.

## 6. Do's and Don'ts

### Do:

- **Do** use green for completed, selected, success, and progress states only.
- **Do** keep product screens compact: 14px body text, 12px labels, 16px page padding, and rounded 12px containers.
- **Do** preserve dark mode and RTL behavior whenever adding UI.
- **Do** respect reduced motion with CSS media queries and JS animation gates.
- **Do** make missed days feel like neutral history, not failure.

### Don't:

- **Don't** use streak-pressure app patterns: fire streaks, guilt copy, punishment loops, or reset anxiety.
- **Don't** use generic SaaS landing-page patterns: hero metric templates, gradient hype, repeated feature-card grids, or corporate productivity language.
- **Don't** use gamified dopamine UI: badges, confetti-first feedback, casino-bright colors, or over-rewarding simple actions.
- **Don't** add gradient text, colored side-stripe card borders, or glassmorphism as decoration.
- **Don't** introduce decorative fonts or mono labels to make routine UI feel more designed.
