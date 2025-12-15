# TODO - Weather Oracle

## Faza 1: Rozszerzone dane pogodowe

- [ ] Rozszerzyć ForecastSnapshot o więcej danych pogodowych (zachmurzenie, wilgotność, ciśnienie, wiatr, UV index)
- [ ] Zaktualizować WeatherService aby pobierał rozszerzone dane
- [ ] Dodać wizualizację rozszerzonych danych na wykresach

## Faza 2: Daily summaries

- [ ] Stworzyć daily summary email dla aktywnych requestów
- [ ] Dodać Command do schedulera wysyłający codzienne podsumowania

## Faza 3: Więcej providerów + porównanie

- [ ] Dodać drugiego providera (np. WeatherAPI.com lub Tomorrow.io)
- [ ] Rozszerzyć UI o wybór providerów przy tworzeniu requesta
- [ ] Stworzyć widok porównania providerów dla tego samego requesta

## Faza 4: Email verification + unsubscribe

- [ ] Stworzyć system weryfikacji email (token w URL)
- [ ] Dodać status 'pending_verification' do MonitoringRequest
- [ ] Zaimplementować wysyłkę maili z linkiem weryfikacyjnym
- [ ] Stworzyć endpoint do potwierdzania requestu z tokenu
- [ ] Dodać system unsubscribe (token w mailu)

## Faza 5: Konta użytkowników + limity

- [ ] Stworzyć model User + migration (opcjonalne konta)
- [ ] Dodać Laravel Breeze do autoryzacji
- [ ] Zaimplementować limity: 5 requestów/dzień dla niezalogowanych
- [ ] Dodać middleware: zablokować anonimowe zapytania dla zalogowanych
- [ ] Dashboard dla zalogowanych użytkowników

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
