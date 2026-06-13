# Multiple Flash Deals on Home Page

## Problem
Currently, the home page only shows a single flash deal (`->first()`). The system supports creating multiple flash deals via the admin panel, but only the first active one is displayed.

## Goal
Display all active flash deals on the home page, stacked vertically — each with its own banner, countdown timer, title, and product carousel.

## Changes

### 1. Backend

#### `app/Providers/AppServiceProvider.php`
- **Line 95-102:** Change `->first()` to `->get()` to fetch all active flash deals as a Collection.
- This provides `$web_config['flash_deals']` as a Collection to all frontend views.

#### `app/Http/Controllers/Web/HomeController.php`
- **theme_aster (line 233-245):** Change `->first()` to `->get()`. The `$flash_deals` variable is already passed via `compact()`.

### 2. Frontend — theme_aster (active theme)

#### `resources/themes/theme_aster/theme-views/home.blade.php`
- `@if ($web_config['flash_deals'])` → `@if ($flash_deals && $flash_deals->count() > 0)`
- Wrap the `@include` inside `@foreach($flash_deals as $flash_deal)`

#### `resources/themes/theme_aster/theme-views/partials/_flash-deals.blade.php`
- The partial uses `$flash_deals` which inside the `@foreach` becomes the individual deal — no variable name changes needed.
- **Navigation fix:** Use `$loop->index` for unique swiper navigation classes:
  - `data-swiper-navigation-next=".swiper-button-next--flash-deal-{{ $loop->index }}"`
  - `data-swiper-navigation-prev=".swiper-button-prev--flash-deal-{{ $loop->index }}"`

### 3. Frontend — default theme

#### `resources/themes/default/web-views/home.blade.php`
- Loop through `$web_config['flash_deals']` with `@foreach`
- Move inline JS (lines 517-622) inside the loop, use `$loop->index` for unique IDs

#### `resources/themes/default/web-views/partials/_flash-deal.blade.php`
- Change all `$web_config['flash_deals']` references to `$flash_deal` (loop variable).

### 4. No Admin Changes
The admin panel already supports multiple flash deals — no changes needed.

## Files to Modify
1. `app/Providers/AppServiceProvider.php` — `->first()` → `->get()`
2. `app/Http/Controllers/Web/HomeController.php` — `->first()` → `->get()` in `theme_aster()`
3. `resources/themes/theme_aster/theme-views/home.blade.php` — loop
4. `resources/themes/theme_aster/theme-views/partials/_flash-deals.blade.php` — unique nav classes
5. `resources/themes/default/web-views/home.blade.php` — loop + JS refactor
6. `resources/themes/default/web-views/partials/_flash-deal.blade.php` — variable rename

## Edge Cases
- **Empty collection:** `->count() > 0` check prevents rendering empty sections.
- **Single flash deal:** Works identical to current behavior.
- **No products in a deal:** Existing `@if($deal->product)` checks handle this.
