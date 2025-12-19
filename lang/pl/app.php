<?php

return [
    // Page titles
    'page_title' => 'Weather Oracle',
    'monitor_forecasts' => 'Monitoruj prognozy pogody',
    'dashboard' => 'Panel',
    'login' => 'Zaloguj się',
    'register' => 'Zarejestruj się',
    'logout' => 'Wyloguj',

    // Form labels
    'location' => 'Lokalizacja',
    'location_placeholder' => 'Wpisz nazwę miasta lub współrzędne',
    'target_date' => 'Data docelowa',
    'email' => 'Email',
    'email_placeholder' => 'twoj@email.com',
    'start_monitoring' => 'Rozpocznij monitorowanie',
    'creating' => 'Tworzenie...',

    // Messages
    'request_created_success' => 'Wróżba utworzona i początkowa prognoza pobrana pomyślnie!',
    'request_created_no_data' => 'Wróżba utworzona. Dane prognozy będą dostępne gdy data docelowa będzie w zasięgu 5 dni.',
    'request_created_verify_email' => 'Sprawdź swoją skrzynkę email i kliknij link aktywacyjny aby rozpocząć monitorowanie!',
    'request_created_no_email' => 'Wróżba utworzona! Prognozy będą pobierane automatycznie.',
    'guest_limit_reached' => 'Osiągnąłeś limit 5 wróżb. Aktywuj lub odrzuć oczekujące wróżby albo poczekaj aż aktywne wygasną.',
    'user_limit_reached' => 'Osiągnąłeś limit 20 aktywnych wróżb. Poczekaj aż któraś wygaśnie lub usuń niepotrzebne.',
    'request_deleted' => 'Wróżba usunięta pomyślnie.',

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
    'request_details' => 'Szczegóły wróżby',
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
    'your_monitoring_requests' => 'Twoje wróżby pogodowe',
    'no_requests_yet' => 'Nie masz jeszcze żadnych wróżb.',
    'view_details' => 'Zobacz szczegóły',
    'delete_request' => 'Usuń',
    'confirm_delete' => 'Czy na pewno chcesz usunąć tę wróżbę?',
    'snapshots_count' => ':count snapshot(ów)',
    'back_to_all_requests' => 'Powrót do wszystkich wróżb',
    'snapshots' => 'snapshotów',

    // Guest dashboard
    'guest_dashboard' => 'Twój Panel',
    'managing_requests_for' => 'Zarządzasz wróżbami dla',
    'guest_dashboard_info' => 'Możesz aktywować oczekujące wróżby, przeglądać aktywne monitorowania i sprawdzać archiwum.',
    'pending_verification' => 'Oczekuje na aktywację',
    'expired' => 'Wygasłe',
    'rejected' => 'Odrzucone',
    'pending_activation_message' => 'Ta wróżba czeka na aktywację. Kliknij przycisk poniżej aby rozpocząć monitorowanie.',
    'expires_in' => 'Wygasa za',
    'activate_now' => 'Aktywuj teraz',
    'cancel_request' => 'Anuluj',
    'confirm_reject' => 'Czy na pewno chcesz anulować tę wróżbę?',
    'want_more' => 'Chcesz więcej?',
    'guest_limit_info' => 'Jako gość możesz mieć maksymalnie 5 aktywnych wróżb. Załóż darmowe konto aby zwiększyć limit do 20 wróżb!',
    'create_free_account' => 'Załóż darmowe konto',

    // Email verification
    'important' => 'Ważne',
    'email_verify_title' => 'Potwierdź swoją wróżbę pogodową',
    'email_greeting' => 'Witaj!',
    'email_requested_monitoring' => 'Utworzyłeś wróżbę pogodową dla:',
    'email_please_verify' => 'Proszę potwierdź swoją wróżbę aby rozpocząć monitorowanie:',
    'email_activate_button' => 'Aktywuj monitorowanie',
    'email_or_visit_dashboard' => 'Lub odwiedź swój panel aby zarządzać wszystkimi wróżbami:',
    'email_view_dashboard' => 'Zobacz Panel',
    'email_expires_warning' => 'Link weryfikacyjny wygaśnie za 2 godziny. Jeśli nie potwierdzisz w tym czasie, twoja wróżba zostanie automatycznie anulowana.',
    'email_not_interested' => 'Nie jesteś zainteresowany?',
    'email_cancel_request' => 'Anuluj tę wróżbę',
    'email_footer_line1' => 'To automatyczna wiadomość od Weather Oracle.',
    'email_footer_line2' => 'Otrzymałeś ją ponieważ utworzyłeś wróżbę pogodową.',
    'email_footer_ignore' => 'Jeśli nie tworzyłeś tej wróżby, możesz bezpiecznie zignorować tę wiadomość.',
];
