# TODO - Weather Oracle Development Roadmap

> **Status aktualny**: Faza 10 zakończona ✅
> **Następny krok**: Faza 11 - UX & Chart Enhancements

---

## 🔥 PRIORYTET 1: Critical Fixes & UX Improvements

### Faza 11: UX & Chart Enhancements

#### 11.1: Terminologia - "Snapshoty" → "Odczyty prognozy" ⏳
**Problem**: Zbyt techniczne pojęcie dla użytkowników końcowych

**Zadania**:
- [ ] Zmienić nazwę w kodzie: `ForecastSnapshot` → zachować (model), ale UI: "Forecast Reading"
- [ ] Zaktualizować tłumaczenia:
  - `lang/pl/app.php`: `forecast_snapshots` → `forecast_readings`, `snapshot` → `reading`
  - `lang/en/app.php`: `forecast_snapshots` → `forecast_readings`
- [ ] Zaktualizować wszystkie blade templates (dashboard, guest-dashboard, request-details)
- [ ] Zaktualizować email templates (FirstSnapshot → FirstReading)
- [ ] Zaktualizować NavigationMenu i nagłówki

**Efekt**: Użytkownik widzi "Odczyty prognozy" zamiast "Snapshoty"

---

#### 11.2: Weather Icons & Categorization ⏳
**Problem**: Brak ikonek pogodowych, warunki są tekstowe

**Zadania**:
- [ ] Utworzyć `app/Helpers/WeatherIconMapper.php`:
  - Metoda `getIcon(string $conditions, string $provider): string` - zwraca emoji/unicode icon
  - Metoda `getCategory(string $conditions, string $provider): string` - zwraca kategorię (clear, cloudy, rain, storm, snow, fog)
  - Mapowanie per provider (OpenWeather, Open-Meteo, Visual Crossing)
- [ ] Utworzyć mapowania dla kategorii:
  - **clear**: ☀️ (Clear, Sunny)
  - **partly_cloudy**: 🌤️ (Partly cloudy, Few clouds)
  - **cloudy**: ☁️ (Cloudy, Overcast)
  - **rain**: 🌧️ (Rain, Light rain, Drizzle)
  - **heavy_rain**: ⛈️ (Heavy rain, Thunderstorm)
  - **snow**: ❄️ (Snow, Light snow)
  - **sleet**: 🌨️ (Sleet, Freezing rain)
  - **fog**: 🌫️ (Fog, Mist, Haze)
  - **wind**: 💨 (Windy)
- [ ] Dodać fallback: jeśli brak mapowania → `🌍` (generic)
- [ ] Zaktualizować blade templates aby używały `WeatherIconMapper::getIcon()`
- [ ] Dodać ikonę obok opisu warunków w:
  - Lista odczytów (monitoring-request-details.blade.php)
  - Email templates (first-snapshot, daily-summary, final-summary)
  - Dashboard cards (jeśli pokazujemy najnowszy odczyt)

**Efekt**: Użytkownik widzi ikonkę pogody obok opisu (np. "☀️ Clear sky")

---

#### 11.3: Collapsible/Accordion Forecast Readings ⏳
**Problem**: Lista odczytów jest bardzo długa i trudna do przeglądania przy wielu danych

**Zadania**:
- [ ] Przeprojektować UI odczytów w `monitoring-request-details.blade.php`:
  - Domyślnie zwinięte (pokazuje: provider, ikona, temp, data/czas fetchu)
  - Kliknięcie rozwija pełne szczegóły (wilgotność, ciśnienie, wiatr, chmury, feels_like)
- [ ] Implementacja z Alpine.js (już dostępny przez Livewire):
  ```blade
  <div x-data="{ open: false }">
      <div @click="open = !open" class="cursor-pointer">
          <!-- Compact view: icon, provider, temp, time -->
      </div>
      <div x-show="open" x-collapse>
          <!-- Full details -->
      </div>
  </div>
  ```
- [ ] Opcjonalnie: grupowanie per provider z licznikiem odczytów
- [ ] Dodać przycisk "Rozwiń wszystkie" / "Zwiń wszystkie" na górze listy
- [ ] Responsywny layout dla mobile

**Efekt**: Lista odczytów jest kompaktowa, użytkownik klika aby zobaczyć szczegóły

---

#### 11.4: Multi-metric Chart (Tabs) ⏳
**Problem**: Wykres pokazuje tylko temperaturę, brak porównania innych metryk

**Zadania**:
- [ ] Dodać tabs/zakładki nad wykresem w `monitoring-request-details.blade.php`:
  - 🌡️ Temperatura (°C)
  - 🌧️ Opady (mm lub %)
  - ☁️ Zachmurzenie (%)
  - 🔽 Ciśnienie (hPa)
  - 💨 Wiatr (m/s)
  - 💧 Wilgotność (%)
- [ ] Implementacja z Alpine.js:
  ```blade
  <div x-data="{ activeTab: 'temperature' }">
      <!-- Tabs buttons -->
      <div class="tabs">
          <button @click="activeTab = 'temperature'">🌡️ Temperatura</button>
          <button @click="activeTab = 'precipitation'">🌧️ Opady</button>
          <!-- etc -->
      </div>

      <!-- Chart container -->
      <div class="chart-wrapper">
          <canvas id="weatherChart"></canvas>
      </div>
  </div>
  ```
- [ ] Refaktoryzacja Chart.js logic:
  - Jedna instancja Chart.js, dynamicznie updateowana przy zmianie zakładki
  - `chart.data.datasets = getDataForMetric(activeTab); chart.update();`
- [ ] Dodać metody pomocnicze w JS:
  - `getDataForMetric(metric)` - zwraca datasets dla danej metryki
  - `getYAxisConfig(metric)` - zwraca konfigurację osi Y (label, unit)
- [ ] Zachować porównanie między providerami (osobne linie per provider)
- [ ] Adaptacyjne ustawienia punktów (jak obecnie dla temperatury)

**Efekt**: Użytkownik może porównywać różne metryki pogodowe między providerami

---

#### 11.5: Landing Page / Welcome Page ⏳
**Problem**: Brak strony powitalnej dla nowych użytkowników

**Zadania**:
- [ ] Utworzyć route `/` → `WelcomeController@index`
- [ ] Utworzyć `resources/views/welcome.blade.php`:
  - Hero section: "Weather Oracle - Porównaj prognozy pogody z różnych źródeł"
  - Sekcja "Jak to działa?":
    1. Wybierz lokalizację i datę
    2. Otrzymuj prognozy z 3 renomowanych providerów
    3. Porównuj zmiany prognozy na przestrzeni czasu
    4. Dostań email z podsumowaniem
  - Sekcja "Dlaczego Weather Oracle?":
    - ✅ 3 niezależne źródła (OpenWeather, Open-Meteo, Visual Crossing)
    - ✅ Monitorowanie zmian prognozy
    - ✅ Email notifications
    - ✅ Darmowe do 5 wróżb (20 dla zalogowanych)
  - CTA: "Utwórz pierwszą wróżbę pogodową" → link do /register lub /dashboard
  - Link do demo: "Zobacz przykładowe monitory" → `/demo`
- [ ] Dodać animacje/ilustracje (opcjonalnie: Tailwind UI examples)
- [ ] Tłumaczenia PL/EN
- [ ] Footer z linkami: O nas, Kontakt, Privacy Policy, Terms of Service

**Efekt**: Profesjonalna strona główna, która wyjaśnia wartość serwisu

---

## 🎯 PRIORYTET 2: Public/Demo Monitors

### Faza 12: Public Forecasts System

#### 12.1: Database Schema - Public Monitors ⏳
**Zadania**:
- [ ] Migration: dodać kolumnę `is_public` (boolean, default false) do `monitoring_requests`
- [ ] Migration: utworzyć tabelę `public_monitor_locations`:
  ```php
  Schema::create('public_monitor_locations', function (Blueprint $table) {
      $table->id();
      $table->string('name'); // "Warsaw, Poland"
      $table->decimal('latitude', 10, 7);
      $table->decimal('longitude', 10, 7);
      $table->boolean('is_active')->default(true);
      $table->integer('max_concurrent_monitors')->default(3); // max aktywnych jednocześnie
      $table->integer('days_ahead')->default(10); // target_date = now + X days
      $table->integer('stagger_days')->default(3); // co ile dni startować nowy monitor
      $table->timestamps();
  });
  ```
- [ ] Seeder: wypełnić 10 predefiniowanych lokalizacji:
  - Warszawa, Kraków, Gdańsk (Polska)
  - Berlin, Paryż, Londyn (Europa)
  - Nowy Jork, Los Angeles (USA)
  - Tokio (Azja)
  - Sydney (Australia)

**Efekt**: Struktura bazy gotowa na publiczne monitory

---

#### 12.2: Public Monitors Maintenance Command ⏳
**Zadania**:
- [ ] Utworzyć `app/Console/Commands/MaintainPublicMonitors.php`:
  - Dla każdej aktywnej lokalizacji z `public_monitor_locations`:
    1. Zlicz aktywne monitory dla tej lokalizacji (`is_public=true`, `status=active`)
    2. Jeśli 0 → utwórz nowy (target_date = now + `days_ahead`)
    3. Jeśli 1-2 → sprawdź najstarszy aktywny:
       - Jeśli utworzony >X dni temu (`stagger_days`) → utwórz kolejny
    4. Ograniczenie: max `max_concurrent_monitors` aktywnych
  - Utworzone monitory:
    - `is_public = true`
    - `user_id = null`, `email = null`
    - `status = active` (bez weryfikacji)
    - `notifications_enabled = false` (bez emaili)
- [ ] Dodać do schedulera: `Schedule::command('monitors:maintain-public')->daily()`
- [ ] Logowanie: `Log::info("Created public monitor for {location}")`

**Efekt**: System automatycznie utrzymuje aktywne publiczne monitory

---

#### 12.3: Public Forecasts View ⏳
**Zadania**:
- [ ] Route: `/demo` → `PublicForecastsController@index`
- [ ] Utworzyć `resources/views/public-forecasts.blade.php`:
  - Lista publicznych lokalizacji (grupowanie per location)
  - Dla każdej lokalizacji:
    - **Aktywne monitory**: status badge, progress bar (ile dni minęło), link do szczegółów
    - **Completed monitory**: ostatnie 5, link do podsumowania
  - Card layout, responsywny
- [ ] Controller logic:
  ```php
  $locations = PublicMonitorLocation::where('is_active', true)->get();
  $publicRequests = MonitoringRequest::where('is_public', true)
      ->orderBy('status', 'asc') // active first
      ->orderBy('created_at', 'desc')
      ->get()
      ->groupBy('location');
  ```
- [ ] Zaktualizować `MonitoringRequestDetails` aby akceptował publiczne monitory (obecnie sprawdza user_id/email)
- [ ] Dodać "Public Demo" badge na widoku szczegółów publicznego monitora
- [ ] Tłumaczenia PL/EN

**Efekt**: Strona `/demo` pokazuje aktywne i archiwalne publiczne monitory

---

## 🧪 PRIORYTET 3: Testing & Quality Assurance

### Faza 13: Automated Testing

#### 13.1: Unit Tests ⏳
**Zadania**:
- [ ] `tests/Unit/Helpers/WeatherTranslatorTest.php`:
  - Test tłumaczenia dla każdego providera
  - Test fallback dla brakujących tłumaczeń
  - Test translateDescription()
- [ ] `tests/Unit/Helpers/WeatherIconMapperTest.php`:
  - Test getIcon() dla każdej kategorii
  - Test getCategory() dla różnych warunków
  - Test fallback dla nieznanych warunków
- [ ] `tests/Unit/Models/NotificationPreferenceTest.php`:
  - Test getForEmail() - tworzenie i pobieranie
  - Test getForUser()
  - Test getByToken()
  - Test unique constraints
- [ ] `tests/Unit/Models/MonitoringRequestTest.php`:
  - Test activeCountForUser()
  - Test activeAndPendingCountForEmail()
  - Test status transitions

**Coverage target**: >80% dla helpers i models

---

#### 13.2: Feature Tests ⏳
**Zadania**:
- [ ] `tests/Feature/Auth/RegistrationTest.php`:
  - Test rejestracji użytkownika
  - Test przypisania requestów po weryfikacji email
- [ ] `tests/Feature/MonitoringRequestTest.php`:
  - Test tworzenia requesta (zalogowany vs guest)
  - Test limitów (5 dla guest, 20 dla user)
  - Test weryfikacji email dla guesta
  - Test aktywacji/odrzucenia requesta
- [ ] `tests/Feature/DashboardTest.php`:
  - Test guest dashboard (widzi tylko swoje)
  - Test user dashboard (widzi tylko swoje)
  - Test usuwania requestów
- [ ] `tests/Feature/NotificationPreferencesTest.php`:
  - Test panel powiadomień (token-based access)
  - Test update globalnych preferencji
  - Test toggle per request
- [ ] `tests/Feature/PublicMonitorsTest.php`:
  - Test strony /demo
  - Test visibility publicznych monitorów
  - Test szczegółów publicznego monitora

**Coverage target**: >70% dla feature flows

---

#### 13.3: Command Tests ⏳
**Zadania**:
- [ ] `tests/Feature/Commands/FetchForecastsTest.php`:
  - Mock API responses
  - Test tworzenia snapshots
  - Test first snapshot notification
  - Test pomijania completed/expired
- [ ] `tests/Feature/Commands/SendDailySummariesTest.php`:
  - Test wysyłania daily summary
  - Test sprawdzania preferencji
  - Test grupowania requestów per user/email
- [ ] `tests/Feature/Commands/SendFinalSummariesTest.php`:
  - Test wysyłania final summary
  - Test porównania providerów
- [ ] `tests/Feature/Commands/MaintainPublicMonitorsTest.php`:
  - Test tworzenia nowych monitorów
  - Test limitu concurrent monitors
  - Test stagger logic

**Coverage target**: >60% dla commands

---

#### 13.4: API/Integration Tests ⏳
**Zadania**:
- [ ] `tests/Integration/WeatherProviders/OpenWeatherTest.php`:
  - Mock API response
  - Test parsowania danych
  - Test error handling
- [ ] `tests/Integration/WeatherProviders/OpenMeteoTest.php`:
  - Mock API response
  - Test parsowania danych
- [ ] `tests/Integration/WeatherProviders/VisualCrossingTest.php`:
  - Mock API response
  - Test parsowania danych

**Tools**:
- PHPUnit
- Laravel HTTP fake dla mock responses
- Pest (opcjonalnie, jeśli preferujesz)

---

## 📋 Priorytetyzacja Implementation

### SPRINT 1 (Tydzień 1-2): Critical UX Fixes
1. ✅ **11.1**: Zmiana terminologii (snapshoty → odczyty) - 2h
2. ✅ **11.2**: Weather icons & categorization - 4h
3. ✅ **11.3**: Collapsible readings accordion - 6h
4. ✅ **11.4**: Multi-metric chart tabs - 8h

**Total**: ~20h work

---

### SPRINT 2 (Tydzień 3): Landing Page & Public Monitors
1. ✅ **11.5**: Landing page / welcome page - 6h
2. ✅ **12.1**: Database schema public monitors - 2h
3. ✅ **12.2**: Maintenance command - 4h
4. ✅ **12.3**: Public forecasts view - 6h

**Total**: ~18h work

---

### SPRINT 3 (Tydzień 4-5): Testing
1. ✅ **13.1**: Unit tests (helpers, models) - 8h
2. ✅ **13.2**: Feature tests (auth, dashboard, requests) - 12h
3. ✅ **13.3**: Command tests - 8h
4. ✅ **13.4**: API integration tests - 4h

**Total**: ~32h work

---

## 🎨 Optional Enhancements (Backlog)

### Faza 14: Advanced Features (Future)
- [ ] Map picker (Leaflet/Google Maps) do wyboru lokalizacji
- [ ] Provider selection (checkboxy przy tworzeniu requesta)
- [ ] Eksport danych do CSV/PDF
- [ ] Webhook notifications (alternatywa dla email)
- [ ] API endpoints dla external access (z API keys)
- [ ] Admin panel (zarządzanie public locations, users, global settings)
- [ ] Dark mode toggle
- [ ] PWA (Progressive Web App) - offline support
- [ ] Push notifications (WebPush)
- [ ] Social sharing (share forecast link)

---

## 📊 Success Metrics

### Po zakończeniu Sprint 1-3:
- ✅ Intuicyjna terminologia (odczyty zamiast snapshoty)
- ✅ Wizualne ikonki pogodowe
- ✅ Kompaktowy, zwijany widok odczytów
- ✅ Multi-metric chart z porównaniem providerów
- ✅ Profesjonalna landing page
- ✅ Działające demo (publiczne monitory)
- ✅ >70% test coverage dla krytycznych flows
- ✅ Brak regresji w istniejącej funkcjonalności

---

## 🔧 Technical Debt

### Rzeczy do refactoringu (low priority):
- [ ] Wydzielić logic Chart.js do osobnego pliku JS (resources/js/components/weather-chart.js)
- [ ] Utworzyć ViewModels dla email templates (zamiast przekazywać raw models)
- [ ] Dodać Redis cache dla API responses (rate limiting protection)
- [ ] Optymalizacja N+1 queries (eager loading check)
- [ ] Dodać indices do bazy danych (email, user_id, status, is_public)
- [ ] Code style check (Laravel Pint) + pre-commit hooks
- [ ] Documentation (PHPDoc dla wszystkich public methods)

---

## 📝 Notes

- **Backward compatibility**: Wszystkie zmiany muszą być kompatybilne wstecz (istniejące dane, API)
- **Translations**: Każda nowa feature wymaga tłumaczeń PL/EN
- **Mobile-first**: Wszystkie UI changes muszą być responsywne
- **Accessibility**: WCAG 2.1 AA compliance (keyboard navigation, screen readers)
- **Performance**: Page load <2s, API response <500ms
- **Security**: OWASP Top 10 compliance, regular dependency updates

---

**Last updated**: 2025-12-21
**Version**: 1.0.0
**Maintained by**: Weather Oracle Dev Team
