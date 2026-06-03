# FraudShield.bd Integration — Design Spec

**Date:** 2026-06-03
**Status:** Approved
**Goal:** Replace Hoorin fraud checking API with FraudShield.bd API

## Scope

Replace the existing Hoorin courier fraud check integration with FraudShield.bd, keeping the same frontend UI (table + pie chart). No new UI features or JS changes needed.

## Changes

### 1. Admin Settings — API Key Management

- **Location:** Add "FraudShield" link to the existing "Third Party" inline menu (`third-party-inline-menu.blade.php`)
- **View:** New file `resources/views/admin-views/business-settings/fraudshield-index.blade.php`
- **Storage:** `business_settings` table, key `fraudshield_api_key`
- **Controller methods** in `BusinessSettingsController.php`:
  - `fraudshield()` — show the settings view
  - `fraudshield_update(Request)` — save API key via `BusinessSetting::updateOrInsert()`
- **Routes** in `routes/admin.php`:
  - `GET admin/business-settings/fraudshield` → `BusinessSettingsController@fraudshield`
  - `POST admin/business-settings/fraudshield-update` → `BusinessSettingsController@fraudshield_update`

### 2. Controller — FraudReputationController

**File:** `app/Http/Controllers/Admin/CustomerReputationController.php`

Current behavior: hardcoded Hoorin API key + GET request

New behavior:
- Read API key from DB: `Helpers::get_business_settings('fraudshield_api_key')`
- POST to `https://fraudshield.bd/api/customer/check`
- Auth header: `Authorization: Bearer {key}`
- Request body: `{"phone": "017XXXXXXXX"}`
- Map response to Hoorin-compatible shape:

```
FraudShield courierData.{courier} → Hoorin Summaries["CourierName"]
  total_parcel                    → "Total Parcels"
  success_parcel                  → "Delivered Parcels"
  cancelled_parcel                → "Canceled Parcels"
```

- Remove dead method `aacheckCustomerReputationStatus()`

### 3. Files That Do Not Change

| File | Reason |
|------|--------|
| `routes/admin.php` | Route `check-customer-reputation/{phone}` stays identical |
| `order-details.blade.php` | Modal HTML, JavaScript AJAX/Chart.js — all unchanged (response mapped server-side) |
| Any other files | No ripple effects |

## Data Flow

```
[Frontend] checkCustomerReputation(phone)
    → GET /admin/orders/check-customer-reputation/{phone}
    → [Controller] reads fraudshield_api_key from DB
    → POST https://fraudshield.bd/api/customer/check (Auth: Bearer)
    → [FraudShield] returns { courierData: {...}, fraudRiskScore: {...}, reviews: [...] }
    → [Controller] maps to { Summaries: { "Steadfast": { "Total Parcels": 25, ... }, ... } }
    → [Frontend] receives same shape → renders table + pie chart (unchanged)
```

## API Reference (FraudShield.bd)

**Base URL:** `https://fraudshield.bd`

**Auth:** `Authorization: Bearer YOUR_API_KEY`

**Endpoint:** `POST /api/customer/check`
- Body: `{"phone": "017XXXXXXXX"}`
- Phone regex: `^01[3-9]\d{8}$`
- Success response includes `courierData` (summary + per-courier: steadfast, pathao, redx, paperfly, carrybee, parceldex), `fraudRiskScore`, `reviews`

**Errors:** 400 (validation), 401 (auth), 429 (rate limit), 502 (upstream error), 503 (network error)
