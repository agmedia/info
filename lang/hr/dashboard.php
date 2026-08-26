<?php

return [
    'analytics' => [
        'provider' => 'Google Analytics 4',
        'unknown_source' => 'Nepoznat izvor',
        'reasons' => [
            'invalid_period' => 'Odabrano razdoblje nije valjano pa se analitika trenutno ne može dohvatiti.',
            'not_configured' => 'Google Analytics izvještaji još nisu povezani s administratorskom nadzornom pločom.',
            'invalid_configuration' => 'GA4 Property ID nije ispravno postavljen na serveru.',
            'credentials_unavailable' => 'GA4 pristupni podaci nisu dostupni na serveru.',
            'authentication_failed' => 'Google Analytics nije prihvatio pristupne podatke. Provjerite service account i njegova prava.',
            'report_request_failed' => 'Google Analytics trenutačno nije vratio izvještaj. Pokušajte ponovno malo kasnije.',
            'unavailable' => 'Web analitika trenutačno nije dostupna.',
        ],
        'setup' => [
            'measurement' => 'unesite GA4 Measurement ID oblika G-XXXXXXXXXX kako bi se uključilo mjerenje na javnoj stranici.',
            'credentials' => 'postavite GA4 Property ID te service-account JSON ili putanju do credentials datoteke s pravom čitanja analitike.',
        ],
        'notes' => [
            'new_users' => ':count novi korisnik|:count novih korisnika',
            'engagement' => 'Udio angažiranih sesija',
            'duration' => 'Prosječna sesija :seconds s',
            'pages_per_session' => ':count stranica po sesiji',
        ],
        'chart' => [
            'visitors' => 'Posjetitelji',
            'sessions' => 'Sesije',
            'render_error' => 'Graf se nije mogao prikazati. Osvježite podatke i pokušajte ponovno.',
        ],
        'loading' => [
            'title' => 'Učitavanje analitike',
            'description' => 'Dohvaćamo najnovije podatke iz Google Analyticsa. Nadzorna ploča će se automatski ažurirati.',
            'refreshing' => 'Osvježavanje podataka…',
            'retry' => 'Pokušaj ponovno',
            'error_title' => 'Analitika se trenutačno nije mogla učitati',
        ],
        'devices' => [
            'desktop' => 'Računalo',
            'mobile' => 'Mobitel',
            'tablet' => 'Tablet',
            'smart tv' => 'Pametni TV',
            'other' => 'Ostalo',
        ],
    ],
];
