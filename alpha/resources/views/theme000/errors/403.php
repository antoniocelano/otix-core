<?php
?><!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Errore 403 - Theme001</title>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-white text-white">
  <div class="text-center px-6">
    <h1 class="text-9xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-red-600 animate-bounce">
      403
    </h1>
    <h2 class="text-2xl font-semibold mt-4">Accesso Negato</h2>
    <p class="text-gray-400 mt-2">Non hai i permessi necessari per visualizzare questa pagina.</p>

    <div class="mt-6 flex justify-center gap-3">
      <a href="/<?= eq(current_lang()) ?>/login"
        class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 focus:ring-offset-gray-900 transition">
        Accedi
      </a>
    </div>
  </div>
</body>
<script src="/public/assets/js/tailwind.min.js"></script>
</html>