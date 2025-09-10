<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/app/Core/Session.php';
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/app/Core/Notify.php';

$notification = \App\Core\Notify::getAndForget();

if ($notification):
    $message  = htmlspecialchars($notification['message']);             // es. "Successfully saved!"
    $details  = htmlspecialchars($notification['details'] ?? '');       // es. "Anyone with a link can now view this file."
    $duration = max(1, (int)$notification['duration']) * 1000;          // ms
    $type     = htmlspecialchars($notification['type'] ?? 'success');   // success|error|warning|info

    // Palette per tipo
    $palette = [
        'success' => ['ring' => 'ring-green-400/30', 'icon' => 'text-green-500'],
        'error'   => ['ring' => 'ring-red-400/30',   'icon' => 'text-red-500'],
        'warning' => ['ring' => 'ring-amber-400/30', 'icon' => 'text-amber-500'],
        'info'    => ['ring' => 'ring-blue-400/30',  'icon' => 'text-blue-500'],
    ];
    $colors = $palette[$type] ?? $palette['success'];
?>
<div id="notify-root" class="fixed top-5 right-5 z-50">
  <div
    id="notify-toast"
    role="status"
    class="max-w-xl w-[520px] bg-white rounded-2xl shadow-lg ring-1 ring-black/5 <?= $colors['ring'] ?> p-4 md:p-5 opacity-0 -translate-y-3 transition-all duration-300 ease-out"
  >
    <div class="flex items-start gap-4">
      <!-- Icona -->
      <div class="shrink-0 mt-0.5">
        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full ring-1 ring-current/20 <?= $colors['icon'] ?>">
          <!-- cerchio + check -->
          <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <path d="m9 11 3 3L22 4"/>
          </svg>
        </span>
      </div>

      <!-- Testi -->
      <div class="min-w-0 flex-1">
        <p class="text-[15px] font-semibold text-gray-900 leading-6"><?= $message ?></p>
        <?php if ($details): ?>
          <p class="mt-0.5 text-[15px] text-gray-500 leading-6"><?= $details ?></p>
        <?php endif; ?>
      </div>

      <!-- Close -->
      <button
        type="button"
        id="notify-close"
        aria-label="Close"
        class="shrink-0 rounded-full p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition"
      >
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6 6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>
  </div>
</div>

<script>
  (function () {
    const root  = document.getElementById('notify-root');
    const toast = document.getElementById('notify-toast');
    const btn   = document.getElementById('notify-close');

    requestAnimationFrame(() => {
      toast.classList.remove('opacity-0', '-translate-y-3');
      toast.classList.add('opacity-100', 'translate-y-0');
    });

    const timeout = setTimeout(hide, <?= $duration ?>);

    btn.addEventListener('click', () => { clearTimeout(timeout); hide(); });

    function hide() {
      toast.classList.remove('opacity-100', 'translate-y-0');
      toast.classList.add('opacity-0', '-translate-y-3');
      setTimeout(() => root.remove(), 250);
    }
  })();
</script>
<?php endif; ?>
