# Multiple Flash Deals on Home Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Display all active flash deals stacked vertically on the home page — each with its own banner, countdown timer, title, and product carousel.

**Architecture:** Change backend queries from `->first()` to `->get()` to return all active deals as a Collection. Loop through the collection in each theme's home page view, passing individual deals to existing partials. Fix CSS/JS selector uniqueness per deal instance.

**Tech Stack:** Laravel 8, Blade, Swiper.js, Owl Carousel, jQuery

---

### Task 1: Backend — fetch all flash deals in AppServiceProvider

**Files:**
- Modify: `app/Providers/AppServiceProvider.php:95-102`

- [ ] **Step 1: Change `->first()` to `->get()`**

Change from:
```php
->whereDate('end_date', '>=', date('Y-m-d'))
    ->first();
```

To:
```php
->whereDate('end_date', '>=', date('Y-m-d'))
    ->get();
```

- [ ] **Step 2: Verify the change**

Run: `php artisan tinker --execute="echo App\Model\FlashDeal::where(['deal_type'=>'flash_deal','status'=>1])->whereDate('start_date','<=',date('Y-m-d'))->whereDate('end_date','>=',date('Y-m-d'))->get()->count();"`
Expected: prints a number (0 or more)

---

### Task 2: Backend — fetch all flash deals in HomeController (theme_aster)

**Files:**
- Modify: `app/Http/Controllers/Web/HomeController.php:233-245`

- [ ] **Step 1: Change `->first()` to `->get()`**

Line 245: change `->first();` to `->get();`

- [ ] **Step 2: Verify the change**

```bash
grep -n '->first()' app/Http/Controllers/Web/HomeController.php
```
Expected: No more `->first()` in the `$flash_deals` query (should only be in the old location if any remains).

---

### Task 3: Frontend — theme_aster home.blade.php loop

**Files:**
- Modify: `resources/themes/theme_aster/theme-views/home.blade.php:23-26`

- [ ] **Step 1: Replace single include with loop**

Old code:
```blade
        <!-- Flash Deal -->
        @if ($web_config['flash_deals'])
            @include('theme-views.partials._flash-deals')
        @endif
```

New code:
```blade
        <!-- Flash Deal -->
        @if ($flash_deals && $flash_deals->count() > 0)
            @foreach($flash_deals as $flash_deal)
                @include('theme-views.partials._flash-deals')
            @endforeach
        @endif
```

Note: Inside the `@foreach`, `$flash_deals` becomes the individual deal (the loop variable overrides the controller-passed collection). The partial references `$flash_deals` throughout — it will now receive the individual deal.

---

### Task 4: Frontend — theme_aster flash deal partial unique swiper navigation

**Files:**
- Modify: `resources/themes/theme_aster/theme-views/partials/_flash-deals.blade.php:15,42-43`

- [ ] **Step 1: Make swiper navigation selectors unique per deal**

Line 15 — change data attributes:
```blade
data-swiper-navigation-next=".swiper-button-next--flash-deal-{{ $loop->index }}" data-swiper-navigation-prev=".swiper-button-prev--flash-deal-{{ $loop->index }}"
```

Line 42-43 — change button classes:
```blade
<div class="swiper-button-next swiper-button-next--flash-deal-{{ $loop->index }}"></div>
<div class="swiper-button-prev swiper-button-prev--flash-deal-{{ $loop->index }}"></div>
```

---

### Task 5: Frontend — default theme home.blade.php loop + JS refactor

**Files:**
- Modify: `resources/themes/default/web-views/home.blade.php:266-268` (include)
- Modify: same file `517-530` (countdown JS)
- Modify: same file `537-622` (owl carousel init)

- [ ] **Step 1: Loop through flash deals**

Old lines 266-268:
```blade
    <!--flash deal-->
    @if ($web_config['flash_deals'] && count($web_config['flash_deals']->products) > 0)
        @include('web-views.partials._flash-deal')
    @endif
```

New:
```blade
    <!--flash deal-->
    @if ($web_config['flash_deals']->count() > 0)
        @foreach($web_config['flash_deals'] as $flash_deal)
            @include('web-views.partials._flash-deal')
        @endforeach
    @endif
```

- [ ] **Step 2: Move countdown progress JS inside loop (lines 517-530)**

Wrap the progress bar script in the loop so each deal gets its own. Replace the old script block with:
```blade
@push('script')
@foreach($web_config['flash_deals'] as $flash_deal)
<script>
    (function(){
        const current_time_stamp = new Date().getTime();
        const start_date = new Date('{{$flash_deal['start_date'] ?? ''}}').getTime();
        const container = document.getElementById('flash-deal-{{ $loop->index }}');
        const countdownElement = container.querySelector('.cz-countdown');
        if (!countdownElement) return;
        const get_end_time = countdownElement.getAttribute('data-countdown');
        const end_time = new Date(get_end_time).getTime();
        let time_progress = ((current_time_stamp - start_date) / (end_time - start_date)) * 100;
        const progress_bar = container.querySelector('.flash-deal-progress-bar');
        if (progress_bar) progress_bar.style.width = time_progress + '%';

        function updateProgress() {
            const now = new Date().getTime();
            const progress = ((now - start_date) / (end_time - start_date)) * 100;
            if (progress_bar) progress_bar.style.width = progress + '%';
        }
        setInterval(updateProgress, 10000);
    })();
</script>
@endforeach
@endpush
```

- [ ] **Step 3: Move Owl Carousel init inside loop (lines 537-622)**

Replace the existing owl carousel init with:
```blade
@push('script')
@foreach($web_config['flash_deals'] as $flash_deal)
<script>
    (function(){
        const container = document.getElementById('flash-deal-{{ $loop->index }}');
        const slider = container.querySelector('.flash-deal-slider');
        const sliderMobile = container.querySelector('.flash-deal-slider-mobile');
        if (slider) {
            $(slider).owlCarousel({
                loop: false, autoplay: true, center: false, margin: 10,
                nav: true,
                navText: ["<i class='czi-arrow-left'></i>","<i class='czi-arrow-right'></i>"],
                dots: false, autoplayHoverPause: true,
                responsive: {
                    0: { items: 1.1 }, 360: { items: 1.2 }, 375: { items: 1.4 },
                    480: { items: 1.8 }, 576: { items: 2 }, 768: { items: 3 },
                    992: { items: 4 }, 1200: { items: 4 }
                }
            });
        }
        if (sliderMobile) {
            $(sliderMobile).owlCarousel({
                loop: false, autoplay: true, center: true, margin: 10,
                nav: true,
                navText: ["<i class='czi-arrow-left'></i>","<i class='czi-arrow-right'></i>"],
                dots: false, autoplayHoverPause: true,
                responsive: {
                    0: { items: 1.1 }, 360: { items: 1.2 }, 375: { items: 1.4 },
                    480: { items: 1.8 }, 576: { items: 2 },
                }
            });
        }
    })();
</script>
@endforeach
@endpush
```

---

### Task 6: Frontend — default theme flash deal partial variable rename

**Files:**
- Modify: `resources/themes/default/web-views/partials/_flash-deal.blade.php:38,46,71-72,81-82,84,91`

- [ ] **Step 1: Replace all `$web_config['flash_deals']` with `$flash_deal`**

Changes in the partial:
| Old | New |
|-----|-----|
| `$web_config['flash_deals']->title` (line 38) | `$flash_deal->title` |
| `$web_config['flash_deals']` ... `end_date` (line 46) | `$flash_deal->end_date` |
| `count($web_config['flash_deals']->products)` (line 70) | `count($flash_deal->products)` |
| `$web_config['flash_deals']['id']` (line 72) | `$flash_deal['id']` |
| `$web_config['flash_deals']['start_date']` (not used in partial; only in JS which is now moved) | N/A |
| `$web_config['flash_deals']->banner` (line 81) | `$flash_deal->banner` |
| `$web_config['flash_deals']->products` in foreach (line 91) | `$flash_deal->products` |

Also wrap the entire section in a div with a unique ID:
```blade
<section class="overflow-hidden" id="flash-deal-{{ $loop->index }}">
```
(Change the opening `<section>` tag)

And ensure the countdown timer uses `$flash_deal`:
```blade
<span class="cz-countdown d-flex ..."
      data-countdown="{{$flash_deal?date('m/d/Y',strtotime($flash_deal['end_date'])):''}} 23:59:00">
```

---

### Task 7: Frontend — default theme home.blade.php remove old JS

**Files:**
- Modify: `resources/themes/default/web-views/home.blade.php:517-622`

- [ ] **Step 1: Remove the old countdown progress JS block (old lines 517-530)**

Delete the entire block from `/*--flash deal Progressbar --*/` through `/*-- end flash deal Progressbar --*/`.

- [ ] **Step 2: Remove the old Owl Carousel flash-deal init block (old lines 537-622)**

Delete the blocks for `$('.flash-deal-slider').owlCarousel({...})` and `$('.flash-deal-slider-mobile').owlCarousel({...})`.

---

### Verification

- [ ] **Step 1: Check syntax**

Run: `php artisan view:clear && php artisan route:list | head -5`
Expected: No Blade compilation errors.

- [ ] **Step 2: Load home page (theme_aster)**

Visit the home page in a browser. With 2+ active flash deals in the admin panel, verify:
- Each deal section appears stacked vertically
- Each has its own title, countdown timer, banner image
- Each has its own product swiper
- Navigation buttons (prev/next) work independently per deal

- [ ] **Step 3: Load home page (default theme)**

Switch to default theme and reload. Verify same behavior.

- [ ] **Step 4: Edge case — zero flash deals**

Deactivate all flash deals in admin. Verify home page renders without errors (no flash deal section shown).

- [ ] **Step 5: Edge case — single flash deal**

Activate only one flash deal. Verify it renders identically to the original behavior.
