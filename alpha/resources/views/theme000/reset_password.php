<?php partial('head'); ?>

<body class="min-h-screen flex items-center py-8 bg-gray-50">
  <main class="w-full max-w-md mx-auto px-6">
    <form action="/<?= eq(current_lang()) ?>/password/reset" method="post" class="bg-white shadow-sm rounded-xl p-6">
      <?php csrf_field(); ?>
      <input type="hidden" name="token" value="<?= eq($token) ?>">

      <h1 class="text-2xl font-semibold tracking-tight mb-6">Reset Password</h1>

      <div class="relative mb-6">
        <input
          type="password"
          id="password"
          name="password"
          placeholder=" "
          required
          class="peer w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
        />
        <label for="password"
          class="pointer-events-none absolute left-3 top-2 text-gray-500 transition
                 peer-placeholder-shown:top-2 peer-placeholder-shown:text-base
                 peer-focus:-top-2 peer-focus:bg-white peer-focus:px-1 peer-focus:text-xs
                 -top-2 bg-white px-1 text-xs">
          Nuova Password
        </label>
      </div>

      <button type="submit"
        class="w-full rounded-lg bg-gray-900 text-white py-2.5 font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
        Imposta Nuova Password
      </button>
    </form>
  </main>
</body>

<?php partial('footer'); ?>
