# UI characteristics (inventory)

This file is the running inventory of UI tokens and reusable components discovered in `old/`, and the agreed implementation in `new/`.

## Tokens

### Colors
- **Brand / primary**
  - `--sn-dark`: `#0f172a` (top bar / dark sections)
  - `--sn-primary`: `#2563eb` (primary blue accents)
  - `--sn-light`: `#38bdf8` (gradient highlight)
- **Neutrals**
  - Background: `slate-50`
  - Text: `slate-900` / `slate-600`
  - Borders: `slate-200` / `slate-100`

### Typography
- **Font**: Instrument Sans (Bunny CDN in `home.blade.php`)
- **Style**: heavy headings (black/extrabold), uppercase micro-labels with wide tracking

### Spacing / layout
- **Max widths**
  - Nav container: `max-w-[1400px]`
  - Content sections: `max-w-7xl`, `max-w-6xl`
- **Section rhythm**
  - Major sections: `py-20` / `py-24`
  - Hero padding: `pt-16 pb-20`

### Radius / shadow
- **Radius**
  - Cards: `rounded-3xl`
  - Buttons: `rounded-full` / `rounded-2xl`
- **Shadow**
  - Elevation: `shadow-xl`, `shadow-2xl`, `shadow-lg`
  - Soft borders: `border-slate-100/200`

## Components
- **Top status bar**: dark strip with uppercase microtext (GPRS/USSD/node)
- **Glass header**: sticky nav with blurred translucent background
- **Hero**
  - badge pill + large headline + 3 stat mini-cards + 2 primary actions
  - floating visual card on right
- **Card grid**: 3-up problem/solution/why-now with consistent padding and radii
- **Footer**: 4-column grid + newsletter form

## Pages / topology notes
- **Homepage topology**: Topbar → Sticky Nav → Hero (2-col) → Problem/Solution/Why (3 cards) → Features grid → How it works (3 steps) → Readiness section (dark) → CTA (3 cards) → Footer

