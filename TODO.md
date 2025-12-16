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

## Faza 5: Statusy requestów + wygasanie

- [ ] Dodać nowe statusy do MonitoringRequest: `pending_verification`, `active`, `completed`, `expired`, `rejected`
- [ ] Migration: dodać kolumny `verification_token`, `dashboard_token`, `expires_at` do monitoring_requests
- [ ] Utworzyć command: MarkExpiredRequests (cron co 10 min) - zmienia `pending_verification` na `expired` jeśli minęło 2h
- [ ] Utworzyć command: MarkCompletedRequests (cron daily) - zmienia `active` na `completed` gdy target_date < now()
- [ ] Zaktualizować FetchForecasts command: pobierać tylko requesty ze statusem `active`
- [ ] Zaktualizować scheduler: nie fetchować requestów `completed`, `expired`, `rejected`

## Faza 6: Email verification dla niezalogowanych

- [ ] Utworzyć Mailable: RequestVerificationEmail (link aktywacyjny + link do dashboardu + link "reject")
- [ ] Route: /verify/{token} - aktywuje request (status: pending → active), przekierowuje na dashboard
- [ ] Route: /reject/{token} - odrzuca request (status: pending → rejected)
- [ ] Route: /dashboard/{dashboard_token} - dashboard niezalogowanego użytkownika
- [ ] Dashboard niezalogowanego: lista requestów z danego email (pending, active, completed, expired, rejected)
- [ ] Dashboard niezalogowanego: przycisk "Activate" dla pending requestów (inline aktywacja bez przechodzenia na /verify)
- [ ] Zaktualizować MonitoringForm: po utworzeniu requesta wysyłaj email weryfikacyjny (nie od razu active)
- [ ] Generowanie unikalnych tokenów: verification_token (per request), dashboard_token (per email - reuse dla tego samego email)

## Faza 7: Limity dla niezalogowanych

- [ ] Walidacja w MonitoringForm: sprawdź czy email nie ma już 5 requestów (active + pending)
- [ ] Komunikat błędu: "Osiągnąłeś limit 5 requestów. Aktywuj lub odrzuć pending requesty albo poczekaj aż aktywne wygasną."
- [ ] Opcja: link w błędzie do dashboardu niezalogowanego
- [ ] Query helper: `MonitoringRequest::activeAndPendingCountForEmail($email)`

## Faza 8: Laravel Breeze + konta użytkowników

- [ ] Zainstalować Laravel Breeze z email verification
- [ ] Migration: dodać `user_id` (nullable) do monitoring_requests
- [ ] Migracja danych: po rejestracji/logowaniu przypisać requesty z email do user_id
- [ ] Middleware: sprawdź czy user jest zalogowany i automatycznie przypisz email z konta
- [ ] Dashboard zalogowanego: pełna lista requestów użytkownika (wszystkie statusy)
- [ ] Dashboard zalogowanego: możliwość usuwania requestów
- [ ] Dashboard zalogowanego: ustawienia powiadomień (placeholder - funkcjonalność w Fazie 10)

## Faza 9: Limity dla zalogowanych

- [ ] Walidacja w MonitoringForm: zalogowany użytkownik może mieć max 20 requestów `active`
- [ ] Komunikat błędu: "Osiągnąłeś limit 20 aktywnych requestów. Poczekaj aż któryś wygaśnie lub usuń niepotrzebne."
- [ ] Zalogowani: requesty od razu `active` (bez pending/verification)
- [ ] Query helper: `MonitoringRequest::activeCountForUser($userId)`

## Faza 10: Email notifications

- [ ] Email powiadomienie gdy pierwszy snapshot zostanie zapisany (target date w zasięgu API)
- [ ] Jeśli kilku providerów zapisuje snapshot tego samego dnia → zbiorczy mail
- [ ] Daily summary email dla aktywnych requestów z aktualnymi prognozami
- [ ] Command w schedulerze wysyłający codzienne podsumowania
- [ ] Ustawienia powiadomień w dashboardzie (opt-out per request)
- [ ] Link "unsubscribe" w każdym mailu (zmienia ustawienia powiadomień dla requesta)

## Faza 11: UI Enhancements

- [ ] Dodać map picker do wyboru lokalizacji (Leaflet/Google Maps)
- [ ] Rozszerzyć UI o wybór providerów przy tworzeniu requesta (checkboxy)

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
