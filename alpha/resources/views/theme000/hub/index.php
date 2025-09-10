<?php partial('head');  use App\Core\Session; ?>
<?php disableCache(); ?>

<body class="min-h-screen flex items-center justify-center bg-gray-50 py-8">
  <div class="w-full max-w-md mx-auto px-6">
    <?php if (Session::has('hub_user_name')): ?>
      <div class="bg-white shadow-sm rounded-xl p-6 text-center">
        <h1 class="text-2xl font-semibold tracking-tight">
          Benvenuto, <?= eq(Session::get('hub_user_name')) ?> <?= eq(Session::get('user_surname')) ?>!
        </h1>

        <form action="/<?= eq(current_lang()) ?>/hub/logout" method="get" class="mt-6">
          <?php csrf_field(); ?>
          <button type="submit"
            class="w-full rounded-lg bg-gray-900 text-white py-2.5 font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
            Logout
          </button>
        </form>
      </div>
    <?php else: ?>
        ERRORE
    <?php endif; ?>
  </div>
</body>

<?php partial('footer'); ?>
