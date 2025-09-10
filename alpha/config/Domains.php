<?php
// imposta qui il dominio da forzare, oppure lascia '' per usare l'host reale
$selectedDomain = 'localhost';

return [
    '_selected' => $selectedDomain, // chiave speciale per override
    'localhost' => [
        'theme' => 'theme000',
        'usr'   => 'USR0000000',
        'env'   => '.env.localhost',
    ],
]; 