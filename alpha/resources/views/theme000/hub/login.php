<?php 
use App\Core\Session;

partial('head'); 
?>

<body class="min-h-screen flex items-center py-8 bg-gray-50">
  <main class="w-full max-w-md mx-auto px-6">
    
    <?php if(Session::has('email_notfound')) { ?>
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
        <?= eq(Session::get('email_notfound')); ?>
      </div>
    <?php } ?>

    <?php if(Session::has('error_message')) { ?>
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
        <?= eq(Session::get('error_message')); ?>
      </div>
    <?php } ?>

    <?php if(Session::has('recover_ok')) { ?>
      <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2 text-sm">
        <?= eq(Session::get('recover_ok')); ?>
      </div>
    <?php } ?>

    <form action="/<?= eq(current_lang()) ?>/hub/login" method="post" class="bg-white shadow-sm rounded-xl p-6">
      <?php csrf_field(); ?>
      <h1 class="text-2xl font-semibold tracking-tight mb-6">Hub Login</h1>

      <div class="relative mb-5">
        <input
          type="email"
          id="email"
          name="email"
          placeholder=" "
          class="peer w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
        />
        <label for="email"
          class="pointer-events-none absolute left-3 top-2 text-gray-500 transition
                 peer-placeholder-shown:top-2 peer-placeholder-shown:text-base
                 peer-focus:-top-2 peer-focus:bg-white peer-focus:px-1 peer-focus:text-xs
                 -top-2 bg-white px-1 text-xs">
          Email
        </label>
      </div>

      <div class="relative mb-6">
        <input
          type="password"
          id="password"
          name="password"
          placeholder=" "
          class="peer w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
        />
        <label for="password"
          class="pointer-events-none absolute left-3 top-2 text-gray-500 transition
                 peer-placeholder-shown:top-2 peer-placeholder-shown:text-base
                 peer-focus:-top-2 peer-focus:bg-white peer-focus:px-1 peer-focus:text-xs
                 -top-2 bg-white px-1 text-xs">
          Password
        </label>
      </div>

      <button type="submit"
        class="w-full cursor-pointer rounded-lg bg-gray-900 text-white py-2.5 font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
        Entra
      </button>

    </form>
  </main>

</body>

<?php partial('footer'); ?>
