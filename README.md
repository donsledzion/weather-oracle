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

## Current Status

### ✅ Implemented

1. **User Input** ✅
   - Form for creating monitoring requests (location, target date, email)
   - Livewire component with validation
   - Auto-refresh list after creation

2. **Monitoring Registration** ✅
   - MonitoringRequest model and database
   - Stores all required fields
   - Initial forecast fetch from all active providers

3. **Weather Data Providers** ✅
   - **3 Active Providers:**
     - **OpenWeather** (5 days, 1000 calls/day)
     - **Open-Meteo** (16 days, unlimited, FREE!)
     - **Visual Crossing** (15 days, 1000 calls/day)
   - WeatherProviderInterface + Factory pattern
   - Unified data model across all providers
   - Provider seeder with configuration

4. **Forecast Storage** ✅
   - ForecastSnapshot model with relations
   - Stores forecast_date from API (validates if within range)
   - Only saves snapshots when target date is within provider's range
   - Links to MonitoringRequest and WeatherProvider

5. **UI/UX** ✅
   - Dashboard with monitoring form
   - List of all monitoring requests
   - Detail page showing forecast snapshots per provider
   - **Localization (PL/EN)** with flag switcher 🇵🇱🇬🇧
   - Language preference saved in session
   - Tailwind CSS styling

6. **Periodic Forecast Collection** ✅
   - Laravel Command `forecasts:fetch`
   - Scheduler runs every 6 hours
   - Fetches forecasts from **all active providers**
   - Stores new snapshots with validation

7. **Target Date Handling** ✅
   - Laravel Command `targets:check`
   - Runs daily via scheduler
   - Fetches actual weather when target date reached
   - Marks requests as completed
   - Stores actual weather data

8. **Visualization** ✅
   - Chart.js integration
   - **Provider Comparison Chart** (separate lines per provider)
   - Temperature trends over time
   - Color-coded: OpenWeather (blue), Open-Meteo (green), Visual Crossing (orange)
   - Shows forecast changes and provider differences

9. **Extended Weather Data** ✅
   - Temperature (min/max/avg/feels_like)
   - Conditions & description
   - Precipitation probability
   - Humidity, pressure, wind speed/direction
   - Cloud coverage, visibility

### 🚧 Planned Features

See [TODO.md](TODO.md) for detailed roadmap with implementation phases:

**Phase 5:** Email verification + unsubscribe system
**Phase 6:** Optional user accounts + rate limiting
**Phase 7:** Email notifications (first snapshot, daily summaries)
**Phase 8:** UI enhancements (map picker)

---

## Configuration

### Required Environment Variables

**Local & Production:**

```env
# Weather API Keys
OPENWEATHER_API_KEY=your_openweather_key_here
VISUAL_CROSSING_API_KEY=your_visualcrossing_key_here
# Open-Meteo: No API key needed - completely free!

# Database (MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=weather_oracle
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

**Get API Keys:**

1. **OpenWeather** (required):
   - Sign up at https://openweathermap.org/api
   - Navigate to API Keys section
   - Free tier: 1000 requests/day, 60 requests/minute, 5 days forecast

2. **Visual Crossing** (required):
   - Sign up at https://www.visualcrossing.com/
   - Get free API key
   - Free tier: 1000 requests/day, 15 days forecast

3. **Open-Meteo** (no key needed):
   - Completely free, no registration
   - 16 days forecast, unlimited requests (fair use)
   - Works out of the box!
