<?php
// imposta qui il dominio da forzare, oppure lascia '' per usare l'host reale
$selectedDomain = 'localhost';

return [
    '_selected' => $selectedDomain, // chiave speciale per override
    'localhost' => [
        'theme' => 'theme001',
        'usr'   => 'USR0000001',
        'env'   => '.env.localhost',
    ],
    'dominio.it' => [
        'theme' => 'theme002',
        'usr'   => 'USR0000002',
        'env'   => '.env.dominio',
    ],
]; 