<?php
use App\Core\Session; 

partialAdmin('head'); 
?>

<body class="min-h-screen flex items-center justify-center bg-gray-50 py-8">
  <main class="w-full max-w-md mx-auto px-6">
    <div class="bg-white shadow-sm rounded-xl p-6 text-center">
      <h1 class="text-2xl font-semibold tracking-tight mb-6">
        Benvenuto, <?= eq(Session::get('user_name') ?? 'Ospite') ?>
      </h1>

      <form action="/<?= eq(current_lang()) ?>/logout" method="get">
        <?php csrf_field(); ?>
        <button type="submit"
          class="w-full rounded-lg cursor-pointer bg-gray-900 text-white py-2.5 font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
          Esci
        </button>
      </form>
    </div>
  </main>
</body>

<?php partialAdmin('footer'); ?>
