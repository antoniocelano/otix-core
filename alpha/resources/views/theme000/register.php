<?php 
use App\Core\Session;
partial('head'); 
?>

<body class="min-h-screen flex items-center py-8 bg-gray-50">
  <main class="w-full max-w-md mx-auto px-6">

    <?php if (Session::has('error_message')): ?>
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
        <?= eq(Session::get('error_message')); ?>
      </div>
    <?php endif; ?>

    <?php if (Session::has('success_message')): ?>
      <div class="mb-4 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2 text-sm">
        <?= eq(Session::get('success_message')); ?>
      </div>
    <?php endif; ?>

    <?php if ($step === 1): ?>
      <form action="/<?= eq(current_lang()) ?>/register/send-otp" method="post" class="bg-white shadow-sm rounded-xl p-6">
        <?php csrf_field(); ?>
        <h1 class="text-2xl font-semibold tracking-tight mb-4">Registrati</h1>
        <p class="text-sm text-gray-600 mb-5">Inserisci la tua email per ricevere un codice di verifica.</p>

        <div class="relative mb-6">
          <input
            type="email"
            id="email"
            name="email"
            placeholder=" "
            required
            class="peer w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
          />
          <label for="email"
            class="pointer-events-none absolute left-3 top-2 text-gray-500 transition
                   peer-placeholder-shown:top-2 peer-placeholder-shown:text-base
                   peer-focus:-top-2 peer-focus:bg-white peer-focus:px-1 peer-focus:text-xs
                   -top-2 bg-white px-1 text-xs">
            Indirizzo Email
          </label>
        </div>

        <button type="submit"
          class="w-full rounded-lg cursor-pointer bg-gray-900 text-white py-2.5 font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
          Invia Codice OTP
        </button>
      </form>
    <?php else: ?>
      <form action="/<?= eq(current_lang()) ?>/register" method="post" class="bg-white shadow-sm rounded-xl p-6">
        <?php csrf_field(); ?>

        <div class="relative mb-4">
          <input
            type="email"
            id="email_readonly"
            value="<?= eq($email_for_otp) ?>"
            readonly
            placeholder=" "
            class="peer w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-gray-700 outline-none"
          />
          <label for="email_readonly"
            class="pointer-events-none absolute left-3 -top-2 bg-white px-1 text-xs text-gray-500">
            Email
          </label>
        </div>

        <div class="relative mb-4">
          <input
            type="text"
            id="name"
            name="name"
            placeholder=" "
            required
            class="peer w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
          />
          <label for="name"
            class="pointer-events-none absolute left-3 top-2 text-gray-500 transition
                   peer-placeholder-shown:top-2 peer-placeholder-shown:text-base
                   peer-focus:-top-2 peer-focus:bg-white peer-focus:px-1 peer-focus:text-xs
                   -top-2 bg-white px-1 text-xs">
            Nome
          </label>
        </div>

        <div class="relative mb-4">
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
            Password
          </label>
        </div>

        <div class="relative mb-6">
          <input
            type="text"
            id="otp"
            name="otp"
            placeholder=" "
            required
            class="peer w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
          />
          <label for="otp"
            class="pointer-events-none absolute left-3 top-2 text-gray-500 transition
                   peer-placeholder-shown:top-2 peer-placeholder-shown:text-base
                   peer-focus:-top-2 peer-focus:bg-white peer-focus:px-1 peer-focus:text-xs
                   -top-2 bg-white px-1 text-xs">
            Codice OTP
          </label>
        </div>

        <button type="submit"
          class="w-full rounded-lg cursor-pointer bg-gray-900 text-white py-2.5 font-medium hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
          Completa Registrazione
        </button>
      </form>
    <?php endif; ?>

    <p class="mt-4 text-center text-sm text-gray-700">
      Hai già un account?
      <a href="/<?= eq(current_lang()) ?>/login" class="font-medium text-gray-900 hover:underline">Accedi</a>
    </p>
  </main>
</body>

<?php partial('footer'); ?>
