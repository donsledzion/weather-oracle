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

## Faza 3: Localization (PL/EN)

- [ ] Skonfigurować Laravel localization (pl, en)
- [ ] Dodać pliki tłumaczeń (resources/lang/pl i resources/lang/en)
- [ ] Przetłumaczyć wszystkie teksty w UI (formularze, komunikaty, błędy)
- [ ] Zaimplementować language picker w nawigacji
- [ ] Zapisywać preferencję języka użytkownika (session/cookie)

## Faza 4: Więcej providerów + porównanie

- [ ] Dodać drugiego providera (np. WeatherAPI.com lub Tomorrow.io)
- [ ] Rozszerzyć UI o wybór providerów przy tworzeniu requesta
- [ ] Stworzyć widok porównania providerów dla tego samego requesta

## Faza 5: Email verification + unsubscribe

- [ ] Stworzyć system weryfikacji email (token w URL)
- [ ] Dodać status 'pending_verification' do MonitoringRequest
- [ ] Zaimplementować wysyłkę maili z linkiem weryfikacyjnym
- [ ] Stworzyć endpoint do potwierdzania requestu z tokenu
- [ ] Dodać system unsubscribe (token w mailu)

## Faza 6: Konta użytkowników + limity

- [ ] Stworzyć model User + migration (opcjonalne konta)
- [ ] Dodać Laravel Breeze do autoryzacji
- [ ] Zaimplementować limity: 5 requestów/dzień dla niezalogowanych
- [ ] Dodać middleware: zablokować anonimowe zapytania dla zalogowanych
- [ ] Dashboard dla zalogowanych użytkowników

## Faza 7: Email notifications

- [ ] Email powiadomienie gdy pierwszy snapshot zostanie zapisany (target date w zasięgu API)
- [ ] Jeśli kilku providerów zapisuje snapshot tego samego dnia → zbiorczy mail
- [ ] Daily summary email dla aktywnych requestów z aktualnymi prognozami
- [ ] Command w schedulerze wysyłający codzienne podsumowania

## Faza 8: UI Enhancements

- [ ] Dodać map picker do wyboru lokalizacji (Leaflet/Google Maps)

---

## Uwagi implementacyjne

### Email verification flow (Faza 4):
- Użytkownik wypełnia formularz (lokacja, data, email)
- System tworzy request ze statusem `pending_verification`
- Wysyła email z unikalnym tokenem weryfikacyjnym
- Po kliknięciu w link → status zmienia się na `active`
- Dopiero wtedy scheduler zaczyna pobierać prognozy
- W każdym emailu link "Unsubscribe" z tokenem

### Limity dla anonimowych (Faza 5):
- Sprawdzanie po IP lub email
- Max 5 requestów/dzień dla niezalogowanych
- Zalogowani: bez limitu (lub wyższy limit)
- Middleware: jeśli zalogowany → nie może robić anonimowo

### Dane pogodowe do rozszerzenia (Faza 1):
- **Zachmurzenie** (cloud coverage %)
- **Wilgotność** (humidity %)
- **Ciśnienie** (pressure hPa)
- **Wiatr** (speed m/s, direction)
- **UV Index**
- **Widoczność** (visibility km)
- **Punkt rosy** (dew point °C)
