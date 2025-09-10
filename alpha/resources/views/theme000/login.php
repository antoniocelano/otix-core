<?php 
use App\Core\Session;

partialAdmin('head'); 
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

    <form action="/<?= eq(current_lang()) ?>/login" method="post" class="bg-white shadow-sm rounded-xl p-6">
      <?php csrf_field(); ?>
      <h1 class="text-2xl font-semibold tracking-tight mb-6">Login</h1>

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

      <div class="flex items-center justify-between mt-4">
        <a href="/<?= eq(current_lang()) ?>/register" class="text-sm font-medium text-gray-700 hover:text-black">Registrati</a>
        <button type="button"
          data-modal-target="forgotPasswordModal"
          class="text-sm font-medium text-gray-700 hover:text-black cursor-pointer">
          Password dimenticata?
        </button>
      </div>
    </form>
    
    <?php if (config('is_site') === true) { ?>
        <a href="/<?= eq(current_lang()) ?>/index" class="w-full mt-4 block text-center rounded-lg border border-gray-300 bg-white text-gray-700 py-2.5 font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
            Vai al sito
        </a>
    <?php } ?>

  </main>

  <div id="forgotPasswordModal"
       class="fixed inset-0 z-50 hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/50" data-modal-close></div>

    <div class="relative z-10 w-full max-w-md mx-4 rounded-xl bg-white shadow-lg">
      <div class="flex items-center justify-between px-5 py-4 border-b">
        <h5 class="text-lg font-semibold">Recupero Password</h5>
        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-gray-100 cursor-pointer" data-modal-close aria-label="Chiudi">
          <span class="sr-only">Chiudi</span>
          ×
        </button>
      </div>

      <form action="/<?= eq(current_lang()) ?>/password/forgot" method="post">
        <div class="px-5 py-4">
          <?php csrf_field(); ?>
          <div class="relative">
            <input
              type="email"
              id="recoverEmail"
              name="email"
              placeholder=" "
              required
              class="peer w-full rounded-lg border border-gray-300 bg-white px-3 py-2 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
            />
            <label for="recoverEmail"
              class="pointer-events-none absolute left-3 top-2 text-gray-500 transition
                     peer-placeholder-shown:top-2 peer-placeholder-shown:text-base
                     peer-focus:-top-2 peer-focus:bg-white peer-focus:px-1 peer-focus:text-xs
                     -top-2 bg-white px-1 text-xs">
              Indirizzo Email
            </label>
          </div>
        </div>
        <div class="px-5 py-4 border-t flex items-center justify-end gap-2">
          <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 hover:bg-gray-50 cursor-pointer" data-modal-close>Chiudi</button>
          <button type="submit" class="rounded-lg bg-gray-900 text-white px-4 py-2 hover:bg-black cursor-pointer">Invia Link di Recupero</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      const openers = document.querySelectorAll('[data-modal-target]');
      const closers = () => document.querySelectorAll('[data-modal-close]');
      openers.forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-modal-target');
          const modal = document.getElementById(id);
          if (!modal) return;
          modal.classList.remove('hidden');
          modal.classList.add('flex');
        });
      });
      document.addEventListener('click', (e) => {
        if (e.target.closest('[data-modal-close]')) {
          const modal = document.getElementById('forgotPasswordModal');
          if (!modal) return;
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          const modal = document.getElementById('forgotPasswordModal');
          if (modal && modal.classList.contains('flex')) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
          }
        }
      });
    })();
  </script>
</body>

<?php partialAdmin('footer'); ?>