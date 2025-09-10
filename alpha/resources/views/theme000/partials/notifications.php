<?php
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/app/Core/Session.php';
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/app/Core/Notify.php';

$notification = \App\Core\Notify::getAndForget();

if ($notification):
    $message  = htmlspecialchars($notification['message'] ?? 'Something went wrong.');
    $details  = htmlspecialchars($notification['details'] ?? '');
    $duration = max(1, (int)($notification['duration'] ?? 3)) * 1000; // default 3s
    $type     = htmlspecialchars($notification['type'] ?? 'generic');

    // Palette per tipo
    $palette = [
        'success' => ['ring' => 'ring-green-400/30', 'icon' => 'text-green-500', 'svg' => 'check'],
        'error'   => ['ring' => 'ring-red-400/30',   'icon' => 'text-red-500',   'svg' => 'x'],
        'warning' => ['ring' => 'ring-amber-400/30', 'icon' => 'text-amber-500', 'svg' => 'warning'],
        'info'    => ['ring' => 'ring-blue-400/30',  'icon' => 'text-blue-500',  'svg' => 'info'],
    ];

    $colors = $palette[$type] ?? ['ring' => 'ring-gray-400/30', 'icon' => 'text-gray-500', 'svg' => 'info'];
    $svg    = $colors['svg'];
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
          <?php if ($svg === 'check'): ?>
            <!-- Success -->
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
              <path d="m9 11 3 3L22 4"/>
            </svg>
          <?php elseif ($svg === 'x'): ?>
            <!-- Error -->
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 6 6 18M6 6l12 12"/>
            </svg>
          <?php elseif ($svg === 'warning'): ?>
            <!-- Warning -->
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 9v2m0 4h.01"/>
              <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3l-8.47-14.14a2 2 0 0 0-3.42 0z"/>
            </svg>
          <?php else: ?>
            <!-- Info -->
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M13 16h-1v-4h-1m1-4h.01"/>
              <circle cx="12" cy="12" r="9"/>
            </svg>
          <?php endif; ?>
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
