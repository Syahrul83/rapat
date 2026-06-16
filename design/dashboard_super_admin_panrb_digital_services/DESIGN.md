---
name: Civic Authority
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#43474e'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#74777f'
  outline-variant: '#c4c6cf'
  surface-tint: '#455f88'
  primary: '#002045'
  on-primary: '#ffffff'
  primary-container: '#1a365d'
  on-primary-container: '#86a0cd'
  inverse-primary: '#adc7f7'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d0e1fb'
  on-secondary-container: '#54647a'
  tertiary: '#182033'
  on-tertiary: '#ffffff'
  tertiary-container: '#2d354a'
  on-tertiary-container: '#969eb7'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d6e3ff'
  primary-fixed-dim: '#adc7f7'
  on-primary-fixed: '#001b3c'
  on-primary-fixed-variant: '#2d476f'
  secondary-fixed: '#d3e4fe'
  secondary-fixed-dim: '#b7c8e1'
  on-secondary-fixed: '#0b1c30'
  on-secondary-fixed-variant: '#38485d'
  tertiary-fixed: '#dae2fd'
  tertiary-fixed-dim: '#bec6e0'
  on-tertiary-fixed: '#131b2e'
  on-tertiary-fixed-variant: '#3f465c'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
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
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Public Sans
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
  headline-md:
    fontFamily: Public Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
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
  label-md:
    fontFamily: Public Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Public Sans
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.02em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  xs: 0.5rem
  sm: 1rem
  md: 1.5rem
  lg: 2rem
  xl: 3rem
  gutter: 1.5rem
  margin-mobile: 1rem
  margin-desktop: 2.5rem
  max-width: 1280px
---

## Brand & Style
The design system is engineered for public sector excellence, prioritizing stability, transparency, and institutional trust. The brand personality is **authoritative yet accessible**, designed to guide citizens through complex information with confidence and clarity.

The visual style follows a **Modern Corporate** aesthetic with a lean toward **Minimalism**. It avoids unnecessary ornamentation, focusing instead on high-quality typography, a structured grid, and a purposeful use of the deep navy primary color to signify reliability. The interface evokes an emotional response of calm competence, ensuring that users feel the weight of official business is handled with modern efficiency.

## Colors
The palette is rooted in a **Deep Navy Blue (#1a365d)**, serving as the primary anchor for headers, primary actions, and brand identification. This is supported by a **Secondary Slate (#64748b)** used for subtle UI elements and secondary information hierarchies.

Backgrounds utilize **Light Slate (#f1f5f9)** to create a soft contrast against **Crisp White (#ffffff)** surfaces. This "card-on-canvas" approach ensures that content areas are clearly defined. Semantic colors (Success, Warning, Error) must be adjusted for high accessibility, ensuring they meet WCAG AA standards against both white and light slate backgrounds.

## Typography
**Public Sans** is the exclusive typeface for the design system. As a font family designed for government use, it offers exceptional legibility across all weights. 

Headlines use bold weights and tighter letter-spacing to command attention and establish authority. Body text is set with generous line heights (1.5x) to facilitate long-form reading of policy documents and instructions. Labels and captions use medium to semi-bold weights with slight letter-spacing increases to ensure clarity at small scales, particularly in data-heavy forms or dashboards.

## Layout & Spacing
The layout follows a **Fixed-Fluid Hybrid** model. Content is contained within a 1280px maximum width to maintain readability, but structural elements like headers and footers span the full viewport width.

A **12-column grid** is used for desktop (breakpoints > 1024px), transitioning to a **2-column or single-column layout** for mobile (breakpoints < 768px). Spacing is strictly based on a 4px baseline grid. Use "md" (1.5rem) for standard padding within containers and "xl" (3rem) for vertical section spacing to ensure the interface feels airy and organized rather than cramped and bureaucratic.

## Elevation & Depth
Depth is primarily conveyed through **Tonal Layers** and **Low-Contrast Outlines**. Instead of heavy shadows, the design system utilizes subtle 1px borders (#e2e8f0) to define surfaces.

When physical separation is required (e.g., for modals or floating notifications), use **Ambient Shadows**. These shadows should be extremely diffused, using the primary navy color at a very low opacity (5-8%) to maintain a cohesive color temperature. 
- **Surface 1 (Base):** Light Slate background.
- **Surface 2 (Content):** Pure White with a 1px border.
- **Surface 3 (Overlay):** Pure White with a soft, deep-blue tinted shadow.

## Shapes
The shape language is **Soft (0.25rem)**. This subtle rounding of corners balances the "hard" authority of the navy palette with a touch of modern user-friendliness. 

Buttons, input fields, and small cards use the base 4px (0.25rem) radius. Larger containers, such as hero sections or main content cards, may use the `rounded-lg` (8px) tokens to create a more distinct visual container. Consistent corner radii are critical to maintaining the professional, "engineered" feel of the system.

## Components
- **Buttons:** Primary buttons use the Deep Navy (#1a365d) with white text. Secondary buttons use a slate border with navy text. Focus states must be highly visible, utilizing a 2px offset ring in a bright blue.
- **Input Fields:** Maintain a classic professional look. Use a 1px border (#cbd5e1) that darkens to the primary navy on focus. Labels must always be visible (never placeholder-only).
- **Cards:** Cards should be white with a 1px border, sitting on the light slate background. No shadows are used for standard cards.
- **Chips/Status Tags:** Use tinted backgrounds (e.g., light green for 'Success') with dark text to signify status without being overly loud.
- **Data Tables:** High priority for government apps. Use subtle zebra striping with the light slate and a semi-bold header row in Deep Navy.
- **Alerts:** Full-width banners or boxed components using high-contrast borders on the left edge to denote severity.