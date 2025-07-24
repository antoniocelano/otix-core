<?php

// Pulizia base dell'URL ricevuto via GET
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '/';

echo "<h1>Ciao dal nostro framework Dev!</h1>";
echo "<p>La rotta richiesta è: <strong>" . htmlspecialchars($url) . "</strong></p>";

// Da qui in poi, caricheremo il router per gestire la richiesta.
// Lo faremo nel prossimo step.