# TODO - Weather Oracle

## Faza 1: Rozszerzone dane pogodowe ✅

- [x] Rozszerzyć ForecastSnapshot o więcej danych pogodowych (zachmurzenie, wilgotność, ciśnienie, wiatr, widoczność, feels_like)
- [x] Zaktualizować WeatherService aby pobierał rozszerzone dane
- [x] Dodać wyświetlanie rozszerzonych danych na kartkach snapshot'ów

## Faza 2: Upgrade dashboard'u ✅

- [x] Naprawić czyszczenie inputów formularza po submit
- [x] Dodać auto-refresh listy requestów po utworzeniu nowego
- [x] Obsłużyć błędy API (lokalizacja nie znaleziona)
- [x] Ulepszyć komunikaty walidacji
- [x] Naprawić problem z zapisywaniem snapshot'ów dla zbyt odległych dat

## Faza 3: Localization (PL/EN) ✅

- [x] Skonfigurować Laravel localization (pl, en)
- [x] Dodać pliki tłumaczeń (lang/pl i lang/en)
- [x] Przetłumaczyć wszystkie teksty w UI (formularze, komunikaty, błędy)
- [x] Zaimplementować language picker z flagami 🇵🇱🇬🇧 w nawigacji
- [x] Zapisywać preferencję języka użytkownika (session + middleware)

## Faza 4: Więcej providerów + porównanie ✅

- [x] Dodać Open-Meteo provider (darmowy, 16 dni prognozy, bez API key)
- [x] Dodać Visual Crossing provider (1000 calls/dzień, 15 dni prognozy)
- [x] Scheduler: pobierać prognozy z wszystkich aktywnych providerów
- [x] Stworzyć wykres porównawczy providerów (osobne linie per provider)
- [x] Utworzyć WeatherProviderInterface i factory pattern
- [ ] **Opcjonalnie:** Rozszerzyć UI o wybór providerów przy tworzeniu requesta (checkboxy)

## Faza 4.5: Tłumaczenia warunków pogodowych ✅

- [x] Utworzyć pliki lang/pl/weather.php i lang/en/weather.php ze słownikami per provider
- [x] Wypełnić słowniki podstawowymi warunkami (OpenWeather ~15, Open-Meteo wszystkie WMO codes, Visual Crossing ~10)
- [x] Stworzyć WeatherTranslator helper z metodą translate() + fallback do raw string
- [x] Dodać kanał logowania w config/logging.php: weather_translations.log
- [x] Logować brakujące tłumaczenia w formacie JSON (provider, condition, locale, timestamp)
- [x] Zaktualizować blade templates aby używały WeatherTranslator::translate()
- [x] Przetestować z różnymi warunkami i sprawdzić logi

## Faza 5: Statusy requestów + wygasanie ✅

- [x] Dodać nowe statusy do MonitoringRequest: `pending_verification`, `active`, `completed`, `expired`, `rejected`
- [x] Migration: dodać kolumny `verification_token`, `dashboard_token`, `expires_at` do monitoring_requests
- [x] Utworzyć command: MarkExpiredRequests (cron co 10 min) - zmienia `pending_verification` na `expired` jeśli minęło 2h
- [x] Utworzyć command: MarkCompletedRequests (cron daily) - zmienia `active` na `completed` gdy target_date < now()
- [x] Zaktualizować FetchForecasts command: pobierać tylko requesty ze statusem `active`
- [x] Zaktualizować scheduler: nie fetchować requestów `completed`, `expired`, `rejected`

## Faza 6: Email verification dla niezalogowanych ✅

- [x] Utworzyć Mailable: RequestVerificationEmail (link aktywacyjny + link do dashboardu + link "reject")
- [x] Route: /verify/{token} - aktywuje request (status: pending → active), przekierowuje na dashboard
- [x] Route: /reject/{token} - odrzuca request (status: pending → rejected)
- [x] Route: /dashboard/{dashboard_token} - dashboard niezalogowanego użytkownika
- [x] Dashboard niezalogowanego: lista requestów z danego email (pending, active, completed, expired, rejected)
- [x] Dashboard niezalogowanego: przycisk "Activate" dla pending requestów (inline aktywacja)
- [x] Zaktualizować MonitoringForm: po utworzeniu requesta wysyłaj email weryfikacyjny (status: pending_verification)
- [x] Generowanie unikalnych tokenów: verification_token (per request), dashboard_token (per email - reuse)

## Faza 7: Limity dla niezalogowanych ✅

- [x] Walidacja w MonitoringForm: sprawdź czy email nie ma już 5 requestów (active + pending)
- [x] Komunikat błędu: "Osiągnąłeś limit 5 requestów. Aktywuj lub odrzuć pending requesty albo poczekaj aż aktywne wygasną."
- [x] Query helper: `MonitoringRequest::activeAndPendingCountForEmail($email)`

## Faza 8: Laravel Breeze + konta użytkowników ✅

- [x] Zainstalować Laravel Breeze z email verification (MustVerifyEmail enabled)
- [x] Migration: dodać `user_id` (nullable) do monitoring_requests
- [x] Migracja danych: po rejestracji/logowaniu przypisać requesty z email do user_id (Event Listener)
- [x] MonitoringForm: auto-fill email dla zalogowanych, ustawia user_id
- [x] Zalogowani: requesty od razu `active` (bez weryfikacji email)
- [x] Dashboard zalogowanego: pełna lista requestów użytkownika (wszystkie statusy)
- [x] Dashboard zalogowanego: możliwość usuwania requestów
- [x] Nawigacja: Login/Register/Dashboard/Logout
- [ ] Dashboard zalogowanego: ustawienia powiadomień (placeholder - funkcjonalność w Fazie 10)

## Faza 9: Limity dla zalogowanych ✅

- [x] Walidacja w MonitoringForm: zalogowany użytkownik może mieć max 20 requestów `active`
- [x] Komunikat błędu: "Osiągnąłeś limit 20 aktywnych requestów. Poczekaj aż któryś wygaśnie lub usuń niepotrzebne."
- [x] Zalogowani: requesty od razu `active` (bez pending/verification)
- [x] Query helper: `MonitoringRequest::activeCountForUser($userId)`

## Faza 10: Email notifications

### 10.1: System zarządzania powiadomieniami ✅
- [x] Migration: utworzyć tabelę `notification_preferences` (email, user_id, token, first_snapshot_enabled, daily_summary_enabled, final_summary_enabled)
- [x] Migration: dodać kolumnę `notifications_enabled` (boolean, default true) do `monitoring_requests`
- [x] Model `NotificationPreference` z metodami helper (getForEmail, getForUser, getByToken, hasAnyEnabled)
- [x] Route `/notifications/{token}` - panel zarządzania powiadomieniami (token-based, bez auth)
- [x] Controller `NotificationPreferencesController` - wyświetlanie i update preferencji (show, updateGlobal, toggleRequest)
- [x] View `notification-preferences.blade.php` - 3 globalne toggle + lista wróżb z toggle per wróżba
- [x] Livewire component `NotificationToggles` dla interaktywnych toggles z ładnymi stylami
- [x] Linki do panelu powiadomień w dashboardach (zalogowany + guest)
- [x] Tłumaczenia PL/EN dla wszystkich tekstów powiadomień
- [x] Dodany Chart.js do app.js (fix błędu na stronie szczegółów)
- [x] Naprawione @livewireScripts i @livewireStyles w layoutcie

### 10.2: Powiadomienia - First Snapshot ✅
- [x] Mailable `FirstSnapshotNotification` - email gdy pierwszy snapshot z providera się pojawi
- [x] Logika w `FetchForecasts` command - wykrywanie pierwszego snapshotu per provider
- [x] Link do ustawień powiadomień w mailu prowadzący do `/notifications/{token}`
- [x] Sprawdzanie `notifications_enabled` i `first_snapshot_enabled` przed wysłaniem
- [x] Email template z danymi prognozy i informacjami o wróżbie
- [x] Tłumaczenia PL/EN dla email template

### 10.3: Powiadomienia - Daily Summary ✅
- [x] Mailable `DailySummary` - email z podsumowaniem wszystkich aktywnych wróżb
- [x] Command `SendDailySummaries` - wysyła daily summary dla użytkowników z włączonym daily_summary_enabled
- [x] Scheduler: daily o 8:00 rano
- [x] Link do ustawień powiadomień w mailu
- [x] Grupowanie wróżb per email/user w zbiorczym mailu
- [x] Pokazuje liczbę nowych snapshotów dzisiaj vs łącznie
- [x] Wyświetla najnowszą prognozę dla każdej wróżby
- [x] Tłumaczenia PL/EN

### 10.4: Powiadomienia - Final Summary ✅
- [x] Mailable `FinalSummary` - podsumowanie po osiągnięciu target_date
- [x] Command `SendFinalSummaries` - wysyła summary dla wróżb które właśnie się zakończyły (status completed)
- [x] Porównanie providerów w mailu (statystyki per provider: count, avg temp, range)
- [x] Zestawienie pierwsza vs ostatnia prognoza per provider
- [x] Link do ustawień powiadomień w mailu
- [x] Scheduler: daily sprawdzanie nowo completed wróżb
- [x] Tłumaczenia PL/EN

### 10.5: Dashboard - integracja powiadomień ✅
- [x] Dashboard zalogowanego: link do globalnych ustawień powiadomień (przycisk 🔔 w headerze)
- [x] Guest dashboard: link do globalnych ustawień (token-based, przycisk 🔔 w headerze)
- [x] Panel powiadomień zawiera toggles per wróżba (nie trzeba ich w dashboardzie)

## Faza 11: UX & Chart Enhancements ✅

### 11.1: Terminologia - "Snapshoty" → "Prognozy" ✅
- [x] Zmienić tłumaczenia: `snapshoty` → `prognozy` w lang/pl/app.php i lang/en/app.php
- [x] Zmienić "monitory" → "wróżby" w limitach
- [x] Usunąć techniczny żargon ("dashboard" → "panel")

### 11.2: Weather Icons & Categorization ✅
- [x] Utworzyć `WeatherIconMapper.php` z HTML entities (unikanie problemów UTF-8)
- [x] Mapowanie kategorii pogodowych z regex patterns
- [x] Dodać ikony do accordion UI w monitoring-request-details.blade.php
- [x] Poprawić regex patterns dla wszystkich wariantów warunków

### 11.3: Collapsible/Accordion Forecast Readings ✅
- [x] Grupowanie prognoz po czasie odczytu (nie po providerze)
- [x] Compact view: ikony wszystkich providerów, czas, liczba źródeł, średnia temperatura
- [x] Detailed view: karty dla każdego providera obok siebie z pełnymi danymi
- [x] Implementacja z Alpine.js (`x-data="{ open: false }"`, `x-collapse`)
- [x] Dodać tłumaczenia: źródło/źródła/źródeł, "Średnia temperatura"
- [x] Responsywny grid layout

### 11.4: Multi-metric Chart (Tabs) ✅
- [x] Dodać tabs: Temperatura, Opady, Zachmurzenie, Ciśnienie, Wiatr, Wilgotność
- [x] Implementacja z Alpine.js: `x-data="{ activeMetric: 'temperature' }"`
- [x] Refaktoryzacja Chart.js z dynamicznym updatem przy zmianie zakładki
- [x] Metody pomocnicze: `getDatasetsForMetric()`, `getYAxisConfig()`
- [x] Zachowano porównanie providerów i adaptacyjne punkty
- [x] Tłumaczenia PL/EN

### 11.5: Landing Page / Welcome Page ✅
- [x] Utworzono `welcome.blade.php` z hero section
- [x] Sekcja "Jak to działa?" z 3 krokami
- [x] Sekcja "Dlaczego Weather Oracle?" z benefitami
- [x] Formularz monitorowania dla gości i zalogowanych
- [x] Tłumaczenia PL/EN

---

## Faza 12: Public Forecasts System ✅

### 12.1: Database Schema - Public Monitors ✅
- [x] Migration: dodać `is_public` do `monitoring_requests`
- [x] Migration: utworzyć tabelę `public_monitor_locations`
- [x] Seeder: 11 polskich miast (Zakopane, Ustrzyki, Suwałki, Łeba, Hel, Szczecin, Toruń, Kraków, Wrocław, Gdańsk, Warszawa)
- [x] Model `PublicMonitorLocation` z relacjami

### 12.2: Public Monitors Maintenance Command ✅
- [x] Utworzyć `MaintainPublicMonitors` command
- [x] Logika: utrzymuj max 3 aktywne monitory per lokalizacja, stagger co 3 dni
- [x] Scheduler: daily + dodany do deploy.yml
- [x] Czyszczenie expired monitorów

### 12.3: Public Forecasts View ✅
- [x] Route: `/demo` → PublicForecastsController
- [x] View: lista publicznych lokalizacji, aktywne + completed monitory z progress barami
- [x] Zaktualizować autoryzację aby akceptowała publiczne monitory
- [x] Tłumaczenia PL/EN
- [x] Link "Demo" w nawigacji i duży CTA na welcome page
- [x] Ujednolicono layout (welcome i demo używają tego samego layoutu)
- [x] Ujednolicono style list wróżb (dashboard i demo mają progress bary)
- [x] Dodano graceful error handling dla nieistniejących emaili gości
- [x] Fix wyświetlania dni (diffForHumans zamiast float)

---

## Faza 13: Automated Testing ⏳

### 13.1: Unit Tests
- [x] WeatherTranslatorTest (14/14 testów, kompletne pokrycie logiki tłumaczeń)
- [x] WeatherIconMapperTest (20/20 testów, wykryto i naprawiono 5 bugów w regex patterns)
- [x] NotificationPreferenceTest (15/15 testów, pełne pokrycie metod i relacji)
- [x] MonitoringRequestTest (15/15 testów, pełne pokrycie statusów i query helpers)
**Target**: >80% coverage dla helpers i models ✅

### 13.2: Feature Tests
- [ ] RegistrationTest (przypisanie requestów)
- [ ] MonitoringRequestTest (limity, weryfikacja)
- [ ] DashboardTest (visibility, usuwanie)
- [ ] NotificationPreferencesTest
- [ ] PublicMonitorsTest
**Target**: >70% coverage dla feature flows

### 13.3: Command Tests
- [x] FetchForecastsTest (4/4 testy logiki command - filtrowanie aktywnych requestów i providerów)
- [x] MaintainPublicMonitorsTest (10/10 testów ✅ - wykryto i naprawiono KRYTYCZNY bug w diffInDays)
- [x] SendDailySummariesTest (8/8 testów ✅ - wykryto i naprawiono bug: snapshots() → forecastSnapshots())
- [x] SendFinalSummariesTest (7/7 testów ✅)
**Target**: >60% coverage dla commands ✅✅✅

**Podsumowanie Fazy 13.3:**
- **94 testy przeszły** (Unit + Command)
- **223 asercje**
- **Czas: 5.44s**
- **Naprawione bugi:**
  1. MaintainPublicMonitors: diffInDays zwracał ujemne wartości (KRYTYCZNY - publiczne monitory się nie tworzyły)
  2. DailySummary + FinalSummary: używały nieistniejącej relacji snapshots() zamiast forecastSnapshots()

### 13.4: API/Integration Tests
- [ ] OpenWeatherTest (mock API response)
- [ ] OpenMeteoTest
- [ ] VisualCrossingTest

---

## 📋 Development Roadmap

**SPRINT 1** (Tydzień 1-2): Faza 11 (UX Fixes) - ~20h
**SPRINT 2** (Tydzień 3): Faza 12 (Public Monitors) - ~18h
**SPRINT 3** (Tydzień 4-5): Faza 13 (Testing) - ~32h

📄 **Szczegółowy plan**: Zobacz [TODO-DEVELOPMENT.md](./TODO-DEVELOPMENT.md)

---

## Uwagi implementacyjne

### Statusy monitoring_requests:
- **`pending_verification`** - niezweryfikowany email (niezalogowany użytkownik), wygasa po 2h
- **`active`** - aktywny, scheduler fetchuje prognozy
- **`completed`** - target_date minęła, monitoring zakończony (archiwalny)
- **`expired`** - pending który wygasł (2h bez weryfikacji)
- **`rejected`** - użytkownik kliknął "reject" w mailu

### Tokeny w monitoring_requests:
- **`verification_token`** - unikalny per request, do aktywacji i odrzucenia
- **`dashboard_token`** - unikalny per email address, do dostępu do dashboardu niezalogowanego
- **`expires_at`** - timestamp wygaśnięcia dla pending requestów (created_at + 2h)

### Limity:
- **Niezalogowany**: max 5 requestów (active + pending łącznie) per email address
- **Zalogowany**: max 20 requestów active (nie liczą się completed/expired/rejected)
- Requesty `completed`, `expired`, `rejected` **nie liczą się** do limitu

### Email verification flow (niezalogowany):
1. Użytkownik wypełnia formularz (lokacja, data, email)
2. System tworzy request ze statusem `pending_verification`, generuje tokeny
3. Wysyła email z:
   - Linkiem aktywacyjnym: `/verify/{verification_token}`
   - Linkiem do dashboardu: `/dashboard/{dashboard_token}`
   - Linkiem reject: `/reject/{verification_token}`
4. Po kliknięciu aktywacji → status: `pending_verification` → `active`, przekierowanie na dashboard
5. Dashboard niezalogowanego pokazuje wszystkie requesty z tego email (można tam też aktywować pending)
6. Po 2h bez aktywacji → cron zmienia status na `expired`

### Dashboard niezalogowanego:
- URL: `/dashboard/{dashboard_token}` (token per email, reużywalny)
- Pokazuje wszystkie requesty z danego email address
- Możliwość aktywacji pending requestów (przycisk "Activate")
- Lista z filtrami: pending, active, completed, expired, rejected
- Link do zakładania konta ("Załóż darmowe konto i zwiększ limit do 20 requestów")

### Migracja niezalogowany → zalogowany:
- Użytkownik zakłada konto na email `test@example.com`
- Po weryfikacji email: system automatycznie przypisuje wszystkie requesty z tym email do `user_id`
- Wszystkie stare requesty (pending, active, completed) automatycznie widoczne w zalogowanym dashboardzie
- `dashboard_token` przestaje być potrzebny (user ma teraz auth)

### Scheduler updates:
- **FetchForecasts**: pobiera tylko requesty ze statusem `active`
- **MarkExpiredRequests**: co 10 min, zmienia `pending_verification` na `expired` jeśli `expires_at < now()`
- **MarkCompletedRequests**: daily, zmienia `active` na `completed` jeśli `target_date < now()`

### Laravel Breeze:
- Instalacja: `composer require laravel/breeze --dev && php artisan breeze:install blade`
- Email verification: included
- Routes: login, register, forgot-password, verify-email
- Dashboard zalogowanego: rozszerzamy domyślny dashboard Breeze
