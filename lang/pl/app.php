<?php

return [
    // Page titles
    'page_title' => 'Weather Oracle',
    'monitor_forecasts' => 'Monitoruj prognozy pogody',

    // Form labels
    'location' => 'Lokalizacja',
    'location_placeholder' => 'Wpisz nazwę miasta lub współrzędne',
    'target_date' => 'Data docelowa',
    'email' => 'Email (opcjonalnie)',
    'email_placeholder' => 'twoj@email.com',
    'start_monitoring' => 'Rozpocznij monitorowanie',
    'creating' => 'Tworzenie...',

    // Messages
    'request_created_success' => 'Żądanie monitorowania utworzone i początkowa prognoza pobrana pomyślnie!',
    'request_created_no_data' => 'Żądanie monitorowania utworzone. Dane prognozy będą dostępne gdy data docelowa będzie w zasięgu 5 dni.',
    'request_created_verify_email' => 'Sprawdź swoją skrzynkę email i kliknij link aktywacyjny aby rozpocząć monitorowanie!',
    'guest_limit_reached' => 'Osiągnąłeś limit 5 żądań. Aktywuj lub odrzuć oczekujące żądania albo poczekaj aż aktywne wygasną.',

    // Errors
    'location_not_found' => 'Lokalizacja nie znaleziona. Sprawdź pisownię lub spróbuj współrzędnych (lat,lon).',
    'api_config_error' => 'Błąd konfiguracji API pogody. Skontaktuj się z pomocą techniczną.',
    'fetch_failed' => 'Nie udało się pobrać danych pogodowych: :message',

    // Validation
    'validation' => [
        'location_required' => 'Lokalizacja jest wymagana.',
        'location_min' => 'Lokalizacja musi mieć co najmniej 2 znaki.',
        'target_date_required' => 'Data docelowa jest wymagana.',
        'target_date_after' => 'Data docelowa musi być w przyszłości.',
        'email_invalid' => 'Podaj prawidłowy adres email.',
    ],

    // Request details
    'request_details' => 'Szczegóły żądania',
    'status' => 'Status',
    'created' => 'Utworzono',
    'active' => 'Aktywne',
    'completed' => 'Zakończone',

    // Forecast snapshots
    'forecast_snapshots' => 'Snapshoty prognozy',
    'no_data_yet' => 'Brak danych prognozy',
    'no_data_message' => 'Data docelowa jest zbyt daleko w przyszłości. Prognozy pogody są dostępne do 5 dni do przodu. Dane prognozy zaczną się pojawiać gdy twoja data będzie w zasięgu.',
    'fetched' => 'Pobrano',
    'forecast_for' => 'Prognoza na',

    // Weather data
    'temperature' => 'Temperatura',
    'feels_like' => 'Odczuwalna',
    'min' => 'Min',
    'max' => 'Maks',
    'conditions' => 'Warunki',
    'precipitation' => 'Opady',
    'humidity' => 'Wilgotność',
    'pressure' => 'Ciśnienie',
    'wind' => 'Wiatr',
    'clouds' => 'Zachmurzenie',

    // Chart
    'temperature_trends' => 'Trendy temperatur',
    'avg_temperature' => 'Średnia temperatura',
    'min_temperature' => 'Minimalna temperatura',
    'max_temperature' => 'Maksymalna temperatura',
    'not_enough_data' => 'Niewystarczająco danych do wykresu (potrzeba co najmniej 2 snapshot\'ów)',

    // Monitoring list
    'your_monitoring_requests' => 'Twoje żądania monitorowania',
    'no_requests_yet' => 'Nie masz jeszcze żadnych żądań monitorowania.',
    'view_details' => 'Zobacz szczegóły',
    'snapshots_count' => ':count snapshot(ów)',
    'back_to_all_requests' => 'Powrót do wszystkich żądań',
    'snapshots' => 'snapshotów',

    // Guest dashboard
    'guest_dashboard' => 'Twój Dashboard',
    'managing_requests_for' => 'Zarządzasz żądaniami dla',
    'guest_dashboard_info' => 'Możesz aktywować oczekujące żądania, przeglądać aktywne monitorowania i sprawdzać archiwum.',
    'pending_verification' => 'Oczekuje na aktywację',
    'expired' => 'Wygasłe',
    'rejected' => 'Odrzucone',
    'pending_activation_message' => 'To żądanie czeka na aktywację. Kliknij przycisk poniżej aby rozpocząć monitorowanie.',
    'expires_in' => 'Wygasa za',
    'activate_now' => 'Aktywuj teraz',
    'cancel_request' => 'Anuluj',
    'confirm_reject' => 'Czy na pewno chcesz anulować to żądanie?',
    'want_more' => 'Chcesz więcej?',
    'guest_limit_info' => 'Jako gość możesz mieć maksymalnie 5 aktywnych żądań. Załóż darmowe konto aby zwiększyć limit do 20 żądań!',
    'create_free_account' => 'Załóż darmowe konto',
];
