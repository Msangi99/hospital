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

- **Admin facility management topology**: Context hero (title + KPI pills + primary action) → Collapsible create form → KPI cards (4-up) → Operations toolbar (search + column visibility) → Data table with moderation actions → Detail modal.

## Migration catalog updates
- **Pattern**: Admin management workspace shell for dense operational pages.
- **Where seen in old**: Facility management flow in legacy admin dashboard (hospital moderation + table controls).
- **Implemented in new**:
  - `resources/views/role/admin/facilities.blade.php`
  - Uses shared token families already present (`slate` neutrals, `blue/emerald/amber/rose` semantic accents, rounded 2xl/3xl surfaces).
- **Reusable components implied**:
  - Hero header block with action CTA.
  - KPI stat card strip.
  - Search + column-picker toolbar.
  - Moderation table row action group.

## Main admin UI standard
- **Canonical reusable components**
  - `resources/views/components/admin/hero.blade.php`
  - `resources/views/components/admin/stat-card.blade.php`
- **Current adopters**
  - `resources/views/role/admin/facilities.blade.php`
  - `resources/views/role/admin/users.blade.php`
- **Rule for new admin pages**
  - Use `x-admin.hero` for top context/action band.
  - Use `x-admin.stat-card` for KPI tiles.
  - Keep toolbar/table surfaces light (`bg-white` + `border-slate-200`) and reserve dark gradients for top-level hero emphasis.

