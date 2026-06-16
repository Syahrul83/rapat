---
name: Civic Trust System
colors:
  surface: '#fbf8ff'
  surface-dim: '#d7d8f4'
  surface-bright: '#fbf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f4f2ff'
  surface-container: '#edecff'
  surface-container-high: '#e6e6ff'
  surface-container-highest: '#e0e0fc'
  on-surface: '#181a2e'
  on-surface-variant: '#3e494a'
  inverse-surface: '#2d2f44'
  inverse-on-surface: '#f1efff'
  outline: '#6f797a'
  outline-variant: '#bec8ca'
  surface-tint: '#006972'
  primary: '#00535b'
  on-primary: '#ffffff'
  primary-container: '#006d77'
  on-primary-container: '#9becf7'
  inverse-primary: '#82d3de'
  secondary: '#236863'
  on-secondary: '#ffffff'
  secondary-container: '#a9ece5'
  on-secondary-container: '#286d67'
  tertiary: '#434c4f'
  on-tertiary: '#ffffff'
  tertiary-container: '#5b6467'
  on-tertiary-container: '#d8e1e4'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#9ff0fb'
  primary-fixed-dim: '#82d3de'
  on-primary-fixed: '#001f23'
  on-primary-fixed-variant: '#004f56'
  secondary-fixed: '#acefe7'
  secondary-fixed-dim: '#90d3cb'
  on-secondary-fixed: '#00201e'
  on-secondary-fixed-variant: '#00504b'
  tertiary-fixed: '#dbe4e7'
  tertiary-fixed-dim: '#bfc8cb'
  on-tertiary-fixed: '#141d1f'
  on-tertiary-fixed-variant: '#3f484b'
  background: '#fbf8ff'
  on-background: '#181a2e'
  surface-variant: '#e0e0fc'
typography:
  display-lg:
    fontFamily: Public Sans
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Public Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Public Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  headline-md:
    fontFamily: Public Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Public Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Public Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Public Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '500'
    lineHeight: 14px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  2xl: 48px
  container-max: 1200px
  gutter: 16px
  margin-mobile: 16px
  margin-desktop: 32px
---

## Brand & Style
The design system is engineered for public service and governmental transparency. It prioritizes clarity, accessibility, and institutional reliability. The brand personality is authoritative yet approachable, evoking a sense of stability and organized efficiency. 

The aesthetic is **Corporate Modern** with a focus on structured information density. It avoids unnecessary flourishes in favor of high-contrast legibility and functional layouts. The visual language utilizes a mix of clean surfaces and subtle tonal shifts to guide the citizen through complex workflows without friction.

## Colors
The palette is grounded in a deep teal (`primary`), symbolizing professionalism and trust. A soft light blue (`secondary`) provides a calming secondary accent, while an off-white/ice-blue (`tertiary`) serves as the foundation for page backgrounds and container fills. 

- **Primary:** Used for key actions, active states, and header accents.
- **Secondary:** Used for progress indicators, secondary buttons, and soft highlight backgrounds.
- **Tertiary:** Used for the main canvas background to reduce eye strain compared to pure white.
- **Neutral:** A deep charcoal used for body text and high-level headings to ensure maximum contrast.

## Typography
This design system uses **Public Sans** as the primary typeface due to its institutional clarity and excellent Indonesian language support (handling diacritics and character spacing efficiently). **Inter** is used for functional labels and micro-copy to maintain precision at small sizes.

Headlines should use tight letter-spacing to appear more authoritative. For Indonesian text, which can often be longer than English counterparts, always ensure `body-md` has sufficient line height (1.5x) to maintain readability in dense paragraphs.

## Layout & Spacing
The layout follows a **Fixed Grid** system for desktop and a **Fluid Grid** for mobile devices. 
- **Desktop:** 12-column grid with a 1200px max-width, 24px gutters.
- **Tablet:** 8-column grid with 16px gutters and 24px side margins.
- **Mobile:** 4-column fluid grid with 16px gutters and 16px side margins.

Spacing follows a 4px base unit. Card-based layouts should use `lg` (24px) padding for internal content to maintain an airy, professional feel, while using `md` (16px) for tighter list-based information.

## Elevation & Depth
Depth is communicated through **Tonal Layers** and extremely subtle **Ambient Shadows**. This design system avoids heavy drop shadows to prevent a "cluttered" look.

- **Level 0 (Base):** `tertiary_color_hex` (#EDF6F9).
- **Level 1 (Cards/Containers):** Pure White (#FFFFFF) with a 1px border in a lighter shade of the secondary color.
- **Level 2 (Interactive/Floating):** Pure White with a 10% opacity primary-tinted shadow (0px 4px 12px).

Use "Ghost Borders" (0.5px or 1px strokes with 10-15% opacity) for input fields and card separators rather than heavy shadows.

## Shapes
The shape language is **Soft**. It strikes a balance between the rigidness of traditional government forms and the friendliness of modern digital services. 

- Standard components (Buttons, Inputs, Cards): 0.25rem (4px).
- Large containers and modal sheets: 0.5rem (8px).
- Small elements like Tags/Chips: 1rem (Pill-shaped) to distinguish them from actionable buttons.

## Components

### Buttons
- **Primary:** Solid `primary_color_hex` with white text. High-contrast, 4px corner radius.
- **Secondary:** Outlined with `primary_color_hex` or a soft tint of `secondary_color_hex`.
- **States:** Hover states should be 10% darker; disabled states use a medium-gray tint with 40% opacity.

### Cards
Cards are the primary vehicle for information. They must feature a white background, 4px radius, and a subtle 1px border. For governmental data, cards should include a "header" area with a `label-md` category indicator.

### Input Fields
Inputs must have clear, persistent labels using `label-md`. The focus state uses a 2px `primary_color_hex` border. Error states must include both a red border and a trailing icon for accessibility.

### Chips & Badges
Used for status (e.g., "Selesai", "Proses"). Use the status color palette with a 10% background opacity and 100% text opacity for high legibility.

### Lists
Information-heavy lists should use `body-sm` for metadata and `headline-md` for titles, separated by subtle 1px dividers in the `tertiary` shade.