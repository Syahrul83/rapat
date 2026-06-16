---
name: Administrative Excellence System
colors:
  surface: '#f8faf9'
  surface-dim: '#d8dada'
  surface-bright: '#f8faf9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f4'
  surface-container: '#eceeee'
  surface-container-high: '#e6e8e8'
  surface-container-highest: '#e1e3e3'
  on-surface: '#191c1c'
  on-surface-variant: '#3f4849'
  inverse-surface: '#2e3131'
  inverse-on-surface: '#eff1f1'
  outline: '#6f7979'
  outline-variant: '#bfc8c9'
  surface-tint: '#21686c'
  primary: '#003c3f'
  on-primary: '#ffffff'
  primary-container: '#005559'
  on-primary-container: '#86c8cc'
  inverse-primary: '#90d2d6'
  secondary: '#536068'
  on-secondary: '#ffffff'
  secondary-container: '#d4e2eb'
  on-secondary-container: '#57656c'
  tertiary: '#333638'
  on-tertiary: '#ffffff'
  tertiary-container: '#494d4f'
  on-tertiary-container: '#bbbdbf'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#aceef2'
  primary-fixed-dim: '#90d2d6'
  on-primary-fixed: '#002021'
  on-primary-fixed-variant: '#004f53'
  secondary-fixed: '#d7e5ed'
  secondary-fixed-dim: '#bbc9d1'
  on-secondary-fixed: '#101d24'
  on-secondary-fixed-variant: '#3c4950'
  tertiary-fixed: '#e0e3e5'
  tertiary-fixed-dim: '#c4c7c9'
  on-tertiary-fixed: '#191c1e'
  on-tertiary-fixed-variant: '#444749'
  background: '#f8faf9'
  on-background: '#191c1c'
  surface-variant: '#e1e3e3'
typography:
  display-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 36px
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.01em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  base: 4px
  container-padding: 24px
  gutter: 16px
  section-gap: 32px
  sidebar-width: 260px
---

## Brand & Style

The design system is engineered for **PANRB Digital Services**, focusing on governmental efficiency, transparency, and high-trust administration. The brand personality is authoritative yet modern, bridging the gap between traditional institutional reliability and contemporary digital agility.

The visual style follows a **Corporate / Modern** aesthetic with subtle **Glassmorphism** influences for data overlays. It prioritizes clarity and information density, utilizing a structured layout inspired by professional dashboard frameworks like shadcn/ui. The emotional response should be one of "controlled precision"—where complex data feels manageable and actions feel consequential.

- **Minimalism:** Use of white space to separate complex data modules.
- **Precision:** Sharp alignment and consistent internal padding.
- **Trust:** A heavy reliance on the primary Deep Teal to anchor the user experience.

## Colors

The palette is anchored by **Deep Teal**, a color that evokes stability and governmental authority. This is balanced by **Light Blue** (Secondary) and a range of slate-based neutrals to maintain a clean, high-contrast environment suitable for long-duration administrative tasks.

### Status Tokens
Specific semantic colors are defined for workflow tracking:
- **Ongoing (Green):** Represents active processes, signifying "in-progress" or "live."
- **Upcoming (Blue):** Indicates scheduled or queued items.
- **Completed (Gray):** Used for historical or finalized records to reduce visual noise.

The default mode is **Light**, optimized for legibility in professional office environments, though the neutral palette is constructed to allow for future high-contrast dark mode adaptation.

## Typography

This design system uses a dual-font strategy. **Plus Jakarta Sans** is used for headings to provide a modern, slightly approachable geometric feel that softens the institutional nature of the app. **Inter** is utilized for all functional text, data tables, and labels, chosen for its exceptional legibility and neutral, systematic character.

- **Hierarchy:** Maintain a clear distinction between data labels (Muted/Small) and user-generated content (Dark/Medium).
- **Numbers:** Tabular figures should be enabled for all data-heavy tables to ensure vertical alignment of numerical values.

## Layout & Spacing

The system utilizes a **Fluid-Fixed Hybrid Grid**. The sidebar remains fixed at 260px, while the main content area expands dynamically. Within the content area, a 12-column grid is employed with 16px gutters to accommodate complex dashboard widgets.

### Responsive Rules
- **Desktop (1024px+):** Full 12-column visibility, persistent sidebar.
- **Tablet (640px - 1023px):** 6-column layout, sidebar collapses into a hamburger menu or narrow icon-rail.
- **Mobile (<640px):** 1-column stack, 16px horizontal margins, bottom-sheet patterns for filters.

Spacing follows a strict 4px base unit to ensure rhythmic consistency across all component dimensions.

## Elevation & Depth

Visual hierarchy is established primarily through **Tonal Layers** and **Low-Contrast Outlines**, avoiding heavy drop shadows to maintain a clean, modern interface.

- **Level 0 (Background):** `Neutral-Background` (#F8FAFC).
- **Level 1 (Cards/Surface):** White (#FFFFFF) with a 1px border in `Neutral-Border` (#E2E8F0).
- **Level 2 (Popovers/Modals):** White with a soft, ambient shadow (Blur 12px, 5% opacity, tinted with Deep Teal).
- **Focus States:** 2px solid stroke using a semi-transparent Deep Teal or Secondary Light Blue to indicate active interaction without shifting layout.

## Shapes

The design system adopts a **Soft** shape language. This provides a balance between the "strict" sharp corners of legacy enterprise software and the "playful" fully rounded corners of consumer apps.

- **Standard Elements (Buttons, Inputs):** 0.25rem (4px).
- **Containers (Cards, Sections):** 0.5rem (8px).
- **Feature Elements (Modals, Large Hero Cards):** 0.75rem (12px).
- **Status Chips:** Use a pill-shape (full radius) to distinguish them from actionable buttons.

## Components

### Buttons
- **Primary:** Deep Teal background, white text. No gradient. 
- **Secondary:** Light Blue background, Deep Teal text.
- **Ghost:** No background, Deep Teal border/text.
- **State:** 10% black overlay on hover; 20% black overlay on active.

### Input Fields
- **Default:** White background, 1px Gray-200 border, Inter Body-md.
- **Focus:** Border changes to Deep Teal with a subtle 2px outer glow in Secondary Blue.
- **Error:** Border changes to Crimson, accompanied by a label-sm error message below.

### Status Chips (Badges)
- Small text, uppercase, bold.
- **Ongoing:** Light Green background, Dark Green text.
- **Upcoming:** Light Blue background, Dark Blue text.
- **Completed:** Light Gray background, Slate text.

### Cards
- White background, 1px neutral border. 
- Header section should have a subtle 1px bottom border to separate titles from content.

### Tables
- Header: Light gray background, uppercase label-sm typography.
- Rows: White background, subtle hover state (background change to #F1F5F9).
- Density: Use "Comfortable" padding (12px vertical) for standard admin views.