<title>Pagina Non Trovata! - <?= eq($_ENV['APP_NAME'])?> </title>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-white text-white">
  <div class="text-center px-6">
    <h1 class="text-9xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-rose-700 animate-pulse">
      404
    </h1>
    <h2 class="text-2xl font-semibold mt-4">Oops! Pagina non trovata</h2>
    <p class="text-gray-400 mt-2">La pagina richiesta non esiste o è stata rimossa.</p>

    <div class="mt-10">
      <img src="/public/assets/images/error/crashed-error.svg" alt="Errore 404"
           class="w-64 mx-auto opacity-90">
    </div>
  </div>
</body>
<script src="/public/assets/js/tailwind.min.js"></script>
</html>