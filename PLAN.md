# Enhancement Plan: AI Suggestions, Excel Export, Data Analytics, Predictive Analysis

## Overview
4 enhancements across 10 implementation steps, building on existing ai_insights.php, report_model, ML service, and Reports views.

---

## Part 1: General AI Insights with Actionable Suggestions (Steps 1-2)

### Step 1 — Enhance `ai_insights.php` with per-sensor actionable suggestions
**File:** `application/libraries/ai_insights.php`
- Add new Rule Group 8: `_actionable_suggestions($readings, $aqi, $names)`
- Per-sensor, per-room actionable advice like:
  - High temp: "Living Room is very hot (35°C). Please turn on the air conditioning or open windows for ventilation."
  - High CO₂: "Bedroom CO₂ is high (1200 µg/m³). Open windows to improve air circulation."
  - High PM2.5: "Kitchen air quality is poor. Please turn on the air purifier or exhaust fan."
  - High humidity: "Bathroom humidity is high (78%). Turn on the exhaust fan to prevent mold."
  - Low humidity: "Bedroom air is dry (22%). Consider using a humidifier."
  - Combined: High CO₂ + high humidity → "Open windows in Kitchen to reduce CO₂ and moisture simultaneously."
- Each suggestion has `category: 'suggestion'` and a new `action` field for UI grouping
- Increase max insights from 8 → 12 to accommodate suggestions

### Step 2 — Add "Suggestions" tab/section in Dashboard AI Insights panel
**File:** `application/views/floorplan.php` + `assets_scada/js/floorplan.js`
- In floorplan.php: add tab toggle (All / Suggestions) inside the AI Insights card header
- In floorplan.js `renderFloorPlanInsights()`: filter insights by category, show suggestion items with a distinct green action-icon style
- Add CSS for `.suggestion-badge` and action styling in `scada.css`

---

## Part 2: Excel Export (Steps 3-4)

### Step 3 — Add Excel (XLSX) export endpoint
**File:** `application/controllers/reports.php`
- Add `export_excel()` method using PhpSpreadsheet (already in PHP 8+, or use simple XML-based Excel generation for no-dependency approach)
- Since we want zero dependencies: generate Excel XML (SpreadsheetML) format that opens in Excel
- Accepts same params as export(): device_id, sensor_type, from, to
- Includes: header row with styling, data rows, summary row at bottom (min/max/avg), auto-width columns
- Output with proper MIME type: `application/vnd.ms-excel`

### Step 4 — Add Excel export buttons to Summary and Trends views
**Files:** `application/views/reports/summary.php` + `application/views/reports/trends.php`
- Summary: Add "Export Excel" button next to existing "Export CSV" dropdown
- Trends: Add export button row below chart with CSV + Excel buttons that use current device/sensor/range selection

---

## Part 3: Data Analytics Page (Steps 5-7)

### Step 5 — Add Analytics API endpoint
**File:** `application/controllers/api.php` + `application/config/routes.php`
- New `GET /api/analytics?device_id=&from=&to=` endpoint
- Returns comprehensive stats per sensor: min, max, mean, median, std_dev, percentiles (25th, 75th, 95th), total readings, time in comfort zone %, peak hours, correlations between sensors

### Step 6 — Add Analytics controller method + view
**Files:** `application/controllers/reports.php` + `application/views/reports/analytics.php` (NEW)
- New `analytics()` method in reports controller
- View layout:
  - Top: Device selector + Date range picker + Load button
  - Row 1: 4 sensor stat cards (temp, humidity, co2, pm25) each showing min/max/avg/median
  - Row 2: Distribution chart (bar chart histogram) + Time-of-day heatmap (avg value by hour)
  - Row 3: Sensor correlation matrix (table showing r-values between sensor pairs)
  - Row 4: AI Interpretation panel (reuse report_insights API)
  - All charts rendered client-side via Chart.js using the analytics API data

### Step 7 — Add sidebar nav entry + route
**Files:** `application/views/templates/scada_sidebar.php` + `application/config/routes.php`
- Add "Analytics" sub-item under Reports in sidebar
- Add route

---

## Part 4: Predictive Analysis Page (Steps 8-10)

### Step 8 — Add Predictive API endpoint
**File:** `application/controllers/api.php` + `application/config/routes.php`
- New `GET /api/predictive?device_id=` endpoint
- Calls ML service `/ml/forecast` for all 4 sensors
- Also calls `/ml/anomaly` for current values
- Returns: forecasts (30min/1hr/2hr per sensor), anomaly status per sensor, risk assessment, and actionable predictions

### Step 9 — Add Predictive Analysis view
**Files:** `application/controllers/reports.php` + `application/views/reports/predictive.php` (NEW)
- New `predictive()` method in reports controller
- View layout:
  - Top: Device selector + "Analyze" button
  - Row 1: 4 predictive cards (one per sensor) — shows current value, predicted 2hr value, trend arrow, confidence interval, risk badge
  - Row 2: Forecast chart — Chart.js line chart showing historical + predicted values with confidence bands
  - Row 3: Risk Assessment panel — aggregated risk level (Low/Medium/High/Critical) with breakdown
  - Row 4: Predictive recommendations — e.g., "Temperature is predicted to reach 38°C in 2 hours. Consider turning on AC now."

### Step 10 — Sidebar nav + routes + CSS for new pages
**Files:** sidebar, routes, scada.css
- Add "Analytics" and "Predictive" sub-items under Reports in sidebar
- Add routes for both new pages
- Add CSS for analytics cards, predictive cards, risk badges, correlation matrix, heatmap cells

---

## Files Modified (Summary)
| File | Steps |
|------|-------|
| `application/libraries/ai_insights.php` | 1 |
| `application/views/floorplan.php` | 2 |
| `assets_scada/js/floorplan.js` | 2 |
| `assets_scada/css/scada.css` | 2, 10 |
| `application/controllers/reports.php` | 3, 6, 9 |
| `application/views/reports/summary.php` | 4 |
| `application/views/reports/trends.php` | 4 |
| `application/controllers/api.php` | 5, 8 |
| `application/config/routes.php` | 5, 7, 8, 10 |
| `application/views/reports/analytics.php` | 6 (NEW) |
| `application/views/reports/predictive.php` | 9 (NEW) |
| `application/views/templates/scada_sidebar.php` | 7, 10 |
