# ForecastTrace

## Project Goal

ForecastTrace is a web application for monitoring how weather forecasts change over time and how accurate they turn out to be on the target date, compared across different weather data providers.

---

## MVP Scope (v0.1)

### 1. User Input

User provides:

-   location (city or lat/lon),
-   target date,
-   optional email address.

No authentication required in MVP.

Each submission creates a **Monitoring Request**.

---

### 2. Monitoring Registration

When a request is created:

-   store:
    -   location,
    -   target date,
    -   monitoring start date,
    -   enabled weather providers.
-   immediately fetch the **initial forecast** (baseline).

---

### 3. Weather Data Providers

MVP:

-   1–2 weather APIs (e.g. OpenWeather + alternative).
-   Unified data model:
    -   temperature (min/max),
    -   precipitation (boolean or mm),
    -   general weather conditions.

Each provider maps its response to a common DTO.

---

### 4. Periodic Forecast Collection

-   Scheduled job (cron / Laravel scheduler):
    -   runs every X hours (e.g. 6h or 12h).
-   For each active monitoring request:
    -   fetch current forecast,
    -   store as a **Forecast Snapshot** with timestamp.

---

### 5. Target Date Handling

On the target date:

-   fetch actual or nearest-available real weather data,
-   mark monitoring request as completed,
-   stop further forecast collection.

---

### 6. Result Summary

For completed requests:

-   compare:
    -   initial forecast,
    -   last forecast before target date,
    -   actual weather.
-   metrics:
    -   temperature error,
    -   condition match (yes/no),
    -   forecast drift over time.

---

### 7. Visualization (Basic)

-   chart:
    -   X-axis: time,
    -   Y-axis: temperature or selected metric,
    -   separate lines per provider.
-   summary table with accuracy metrics.

---

## Data Model (MVP)

### MonitoringRequest

-   id
-   location
-   target_date
-   email (nullable)
-   status
-   created_at

### WeatherProvider

-   id
-   name
-   configuration

### ForecastSnapshot

-   id
-   monitoring_request_id
-   provider
-   forecast_data (JSON)
-   fetched_at

### ActualWeather

-   id
-   monitoring_request_id
-   actual_data (JSON)
-   fetched_at

---

## Post-MVP Ideas

-   Email report with final summary
-   User accounts and monitoring history
-   Random simulated forecast ("alternative oracle")
-   Climate zone-based constraints
-   Provider accuracy rankings

---

## Tech Stack

-   **Backend**: Laravel 12
-   **Frontend**: Livewire 3 + Alpine.js
-   **Charts**: Chart.js
-   **Database**: MySQL
-   **Queue**: Laravel Queues
-   **Scheduler**: Laravel Scheduler

### Why Livewire?

-   Write in Blade + PHP (no separate frontend framework)
-   Real-time updates without page reload (perfect for forecast monitoring)
-   Built-in polling for auto-refresh (`wire:poll`)
-   Zero API endpoints needed
-   Forms, validation, and dynamic UI out of the box

### Why Alpine.js?

-   Micro-interactions (dropdowns, tooltips, animations)
-   Minimal JS footprint (~15kb)
-   Works seamlessly with Livewire

---

## Status

MVP – in progress
