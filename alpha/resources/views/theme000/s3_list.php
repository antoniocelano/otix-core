<?php partial('head'); ?>

<body class="bg-gray-50 min-h-screen">
  <div class="max-w-6xl mx-auto px-4 py-8">

    <h1 class="text-2xl font-semibold tracking-tight mb-4">Elenco File su S3</h1>

    <?php if (isset($error)): ?>
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
        <?= eq($error) ?>
      </div>
    <?php else: ?>

      <!-- Top bar: breadcrumb + upload -->
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="text-sm">
          <ol class="flex flex-wrap items-center gap-1 text-gray-600">
            <li>
              <a href="/<?= eq(current_lang()) ?>/s3/list" class="hover:text-gray-900 underline-offset-2 hover:underline">
                Bucket: <?= eq($bucket) ?>
              </a>
            </li>
            <?php if (!empty($breadcrumbs)): ?>
              <?php foreach ($breadcrumbs as $name => $path): ?>
                <li class="mx-1 text-gray-400">/</li>
                <?php if ($path === null): ?>
                  <li class="text-gray-900 font-medium"><?= eq($name) ?></li>
                <?php else: ?>
                  <li>
                    <a href="/<?= eq(current_lang()) ?>/s3/list/<?= enc_path($path) ?>" class="hover:text-gray-900 underline-offset-2 hover:underline">
                      <?= eq($name) ?>
                    </a>
                  </li>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </ol>
        </nav>

        <!-- Upload form -->
        <form action="/<?= eq(current_lang()) ?>/s3/upload" method="post" enctype="multipart/form-data" class="flex items-center gap-2">
          <?php csrf_field(); ?>
          <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">
          <input
            type="file"
            name="fileToUpload"
            id="fileToUpload"
            required
            class="block w-full md:w-64 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-900 file:px-3 file:py-2 file:text-white hover:file:bg-black cursor-pointer"
          >
          <button type="submit"
            class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">
            Carica
          </button>
        </form>
      </div>

      <!-- Flash messages -->
      <?php if (isset($_SESSION['upload_error'])): ?>
        <div class="mb-3 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['upload_error']); unset($_SESSION['upload_error']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['upload_success'])): ?>
        <div class="mb-3 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['upload_success']); unset($_SESSION['upload_success']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['delete_error'])): ?>
        <div class="mb-3 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['delete_error']); unset($_SESSION['delete_error']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['delete_success'])): ?>
        <div class="mb-3 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['delete_success']); unset($_SESSION['delete_success']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['copy_error'])): ?>
        <div class="mb-3 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['copy_error']); unset($_SESSION['copy_error']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['copy_success'])): ?>
        <div class="mb-3 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['copy_success']); unset($_SESSION['copy_success']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['move_error'])): ?>
        <div class="mb-3 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['move_error']); unset($_SESSION['move_error']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['move_success'])): ?>
        <div class="mb-3 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['move_success']); unset($_SESSION['move_success']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['rename_error'])): ?>
        <div class="mb-3 rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['rename_error']); unset($_SESSION['rename_error']); ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['rename_success'])): ?>
        <div class="mb-3 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-2 text-sm">
          <?= eq($_SESSION['rename_success']); unset($_SESSION['rename_success']); ?>
        </div>
      <?php endif; ?>

      <!-- Files table -->
      <?php if (empty($files)): ?>
        <div class="rounded-lg border border-blue-200 bg-blue-50 text-blue-800 px-4 py-2 text-sm">
          Questa cartella è vuota.
        </div>
      <?php else: ?>
        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600">
              <tr>
                <th class="px-4 py-3 font-medium">Nome</th>
                <th class="px-4 py-3 font-medium">Tipo</th>
                <th class="px-4 py-3 font-medium">Dimensione</th>
                <th class="px-4 py-3 font-medium">Ultima Modifica</th>
                <th class="px-4 py-3 font-medium">Azione</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <?php foreach ($files as $file): ?>
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-3">
                    <?php if ($file['type'] === 'folder'): ?>
                      <a href="/<?= eq(current_lang()) ?>/s3/list/<?= enc_path($file['path']) ?>" class="inline-flex items-center gap-2 hover:underline">
                        <span aria-hidden="true">📁</span>
                        <span><?= eq($file['name']) ?></span>
                      </a>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-2">
                        <span aria-hidden="true">📄</span>
                        <span><?= eq($file['name']) ?></span>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3"><?= eq($file['type']) ?></td>
                  <td class="px-4 py-3">
                    <?php if ($file['type'] !== 'folder'): ?>
                      <?= eq(formatBytes($file['size'])) ?>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3">
                    <?php if ($file['type'] !== 'folder'): ?>
                      <?= eq(formatDate($file['lastModified'])) ?>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3">
                    <?php if ($file['type'] !== 'folder'): ?>
                      <div class="flex flex-wrap items-center gap-2">
                        <button
                          type="button"
                          class="rounded-md bg-gray-900 text-white px-3 py-1.5 text-xs font-medium hover:bg-black"
                          data-modal-target="fileModal"
                          data-file-path="<?= eq($file['path']) ?>"
                        >Apri</button>

                        <button
                          type="button"
                          class="rounded-md bg-blue-600 text-white px-3 py-1.5 text-xs font-medium hover:bg-blue-700"
                          data-modal-target="copyFileModal"
                          data-source-file-path="<?= eq($file['path']) ?>"
                          data-file-name="<?= eq($file['name']) ?>"
                        >Copia</button>

                        <button
                          type="button"
                          class="rounded-md bg-amber-500 text-white px-3 py-1.5 text-xs font-medium hover:bg-amber-600"
                          data-modal-target="moveFileModal"
                          data-source-file-path="<?= eq($file['path']) ?>"
                          data-file-name="<?= eq($file['name']) ?>"
                        >Sposta</button>

                        <button
                          type="button"
                          class="rounded-md bg-gray-200 text-gray-900 px-3 py-1.5 text-xs font-medium hover:bg-gray-300"
                          data-modal-target="renameFileModal"
                          data-file-path="<?= eq($file['path']) ?>"
                          data-file-name="<?= eq($file['name']) ?>"
                        >Rinomina</button>

                        <form
                          action="/<?= eq(current_lang()) ?>/s3/delete"
                          method="post"
                          onsubmit="return confirm('Sei sicuro di voler eliminare il file <?= addslashes($file['name']) ?>?');"
                          class="inline"
                        >
                          <?php csrf_field(); ?>
                          <input type="hidden" name="file_path" value="<?= eq($file['path']) ?>">
                          <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">
                          <button type="submit"
                            class="rounded-md bg-rose-600 text-white px-3 py-1.5 text-xs font-medium hover:bg-rose-700">
                            Elimina
                          </button>
                        </form>
                      </div>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>

    <?php endif; ?>

    <p class="mt-6 text-center text-sm text-gray-600">
      Torna alla <a href="/" class="font-medium text-gray-900 hover:underline">home</a>.
    </p>
  </div>

  <!-- Modal base styles -->
  <style>
    .tw-modal{display:none}
    .tw-modal.tw-open{display:flex}
  </style>

  <!-- Modale: Contenuto File -->
  <div id="fileModal" class="tw-modal fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-black/50" data-modal-close></div>
    <div class="relative z-10 w-full max-w-3xl mx-4 rounded-xl bg-white shadow-lg">
      <div class="flex items-center justify-between px-5 py-4 border-b">
        <h5 class="text-lg font-semibold" id="fileModalLabel">Contenuto File</h5>
        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-gray-100" data-modal-close aria-label="Chiudi">×</button>
      </div>
      <div class="px-5 py-4">
        <div id="fileContent" class="prose prose-sm max-w-none"></div>
      </div>
      <div class="px-5 py-4 border-t flex justify-end">
        <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 hover:bg-gray-50" data-modal-close>Chiudi</button>
      </div>
    </div>
  </div>

  <!-- Modale: Copia File -->
  <div id="copyFileModal" class="tw-modal fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-black/50" data-modal-close></div>
    <div class="relative z-10 w-full max-w-md mx-4 rounded-xl bg-white shadow-lg">
      <div class="flex items-center justify-between px-5 py-4 border-b">
        <h5 class="text-lg font-semibold">Copia File</h5>
        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-gray-100" data-modal-close aria-label="Chiudi">×</button>
      </div>
      <form action="/<?= eq(current_lang()) ?>/s3/copy" method="post" id="copyFileForm">
        <div class="px-5 py-4 space-y-4">
          <?php csrf_field(); ?>
          <input type="hidden" name="source_file_path" id="sourceFilePath">
          <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">

          <div>
            <label class="block text-sm font-medium text-gray-700"><strong>File da copiare:</strong></label>
            <p id="sourceFileDisplay" class="text-sm text-gray-500 mt-1"></p>
          </div>

          <div>
            <label for="destinationFolder" class="block text-sm font-medium text-gray-700">Scegli la cartella di destinazione</label>
            <select id="destinationFolder" name="destination_folder_path"
              class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900">
              <option value="" selected>Radice del Bucket</option>
              <?php if (!empty($all_folders)): foreach ($all_folders as $folder): ?>
                <option value="<?= eq(rtrim($folder, '/')) ?>"><?= eq(rtrim($folder, '/')) ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <div>
            <label for="newFileName" class="block text-sm font-medium text-gray-700">Nuovo nome del file</label>
            <input type="text" id="newFileName" name="new_file_name"
              class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
              placeholder="Lascia vuoto per mantenere il nome originale">
            <p class="mt-1 text-xs text-gray-500">Se vuoi rinominare il file, inserisci il nuovo nome. Altrimenti verrà mantenuto il nome originale.</p>
          </div>
        </div>
        <div class="px-5 py-4 border-t flex justify-end gap-2">
          <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 hover:bg-gray-50" data-modal-close>Annulla</button>
          <button type="submit" class="rounded-lg bg-gray-900 text-white px-4 py-2 hover:bg-black">Copia File</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modale: Sposta File -->
  <div id="moveFileModal" class="tw-modal fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-black/50" data-modal-close></div>
    <div class="relative z-10 w-full max-w-md mx-4 rounded-xl bg-white shadow-lg">
      <div class="flex items-center justify-between px-5 py-4 border-b">
        <h5 class="text-lg font-semibold">Sposta File</h5>
        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-gray-100" data-modal-close aria-label="Chiudi">×</button>
      </div>
      <form action="/<?= eq(current_lang()) ?>/s3/move" method="post" id="moveFileForm">
        <div class="px-5 py-4 space-y-4">
          <?php csrf_field(); ?>
          <input type="hidden" name="source_file_path" id="moveSourceFilePath">
          <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">

          <div>
            <label class="block text-sm font-medium text-gray-700"><strong>File da spostare:</strong></label>
            <p id="moveSourceFileDisplay" class="text-sm text-gray-500 mt-1"></p>
          </div>

          <div>
            <label for="moveDestinationFolder" class="block text-sm font-medium text-gray-700">Scegli la cartella di destinazione</label>
            <select id="moveDestinationFolder" name="destination_folder_path"
              class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900">
              <option value="" selected>Radice del Bucket</option>
              <?php if (!empty($all_folders)): foreach ($all_folders as $folder): ?>
                <option value="<?= eq(rtrim($folder, '/')) ?>"><?= eq(rtrim($folder, '/')) ?></option>
              <?php endforeach; endif; ?>
            </select>
          </div>

          <div>
            <label for="moveNewFileName" class="block text-sm font-medium text-gray-700">Nuovo nome del file</label>
            <input type="text" id="moveNewFileName" name="new_file_name"
              class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
              placeholder="Lascia vuoto per mantenere il nome originale">
            <p class="mt-1 text-xs text-gray-500">Se vuoi rinominare il file, inserisci il nuovo nome. Altrimenti verrà mantenuto il nome originale.</p>
          </div>
        </div>
        <div class="px-5 py-4 border-t flex justify-end gap-2">
          <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 hover:bg-gray-50" data-modal-close>Annulla</button>
          <button type="submit" class="rounded-lg bg-amber-500 text-white px-4 py-2 hover:bg-amber-600">Sposta File</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modale: Rinomina File -->
  <div id="renameFileModal" class="tw-modal fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-black/50" data-modal-close></div>
    <div class="relative z-10 w-full max-w-md mx-4 rounded-xl bg-white shadow-lg">
      <div class="flex items-center justify-between px-5 py-4 border-b">
        <h5 class="text-lg font-semibold">Rinomina File</h5>
        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md hover:bg-gray-100" data-modal-close aria-label="Chiudi">×</button>
      </div>
      <form action="/<?= eq(current_lang()) ?>/s3/rename" method="post" id="renameFileForm">
        <div class="px-5 py-4 space-y-4">
          <?php csrf_field(); ?>
          <input type="hidden" name="source_file_path" id="renameSourceFilePath">
          <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">

          <div>
            <label class="block text-sm font-medium text-gray-700"><strong>File da rinominare:</strong></label>
            <p id="renameSourceFileDisplay" class="text-sm text-gray-500 mt-1"></p>
          </div>

          <div>
            <label for="renameNewFileName" class="block text-sm font-medium text-gray-700">Nuovo nome del file</label>
            <input type="text" id="renameNewFileName" name="new_file_name" required
              class="mt-1 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900">
          </div>
        </div>
        <div class="px-5 py-4 border-t flex justify-end gap-2">
          <button type="button" class="rounded-lg border border-gray-300 bg-white px-4 py-2 hover:bg-gray-50" data-modal-close>Annulla</button>
          <button type="submit" class="rounded-lg bg-gray-900 text-white px-4 py-2 hover:bg-black">Rinomina</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function escapeHtml(s) {
      return s.replace(/[&<>"'`=\/]/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;',
        "'": '&#39;', '`': '&#96;', '=': '&#61;', '/': '&#47;'
      }[c]));
    }

    (function () {
      const openers = document.querySelectorAll('[data-modal-target]');
      const modals = new Map();

      function openModal(id) {
        const m = document.getElementById(id);
        if (!m) return;
        m.classList.add('tw-open', 'flex');
        m.setAttribute('aria-hidden', 'false');
        modals.set(id, m);
        if (id === 'fileModal') onOpenFileModal(m);
      }

      function closeModal(el) {
        const m = el.closest('.tw-modal') || el;
        if (!m) return;
        m.classList.remove('tw-open', 'flex');
        m.setAttribute('aria-hidden', 'true');
      }

      openers.forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-modal-target');
          const modal = document.getElementById(id);
          if (modal) modal.__trigger = btn;
          openModal(id);
        });
      });

      document.addEventListener('click', (e) => {
        if (e.target.matches('[data-modal-close]')) {
          closeModal(e.target);
        }
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          [...modals.values()].forEach(closeModal);
          modals.clear();
        }
      });

      function onOpenFileModal(modalEl) {
        const trigger = modalEl.__trigger;
        if (!trigger) return;
        const filePath = trigger.getAttribute('data-file-path');
        const label = modalEl.querySelector('#fileModalLabel');
        const container = modalEl.querySelector('#fileContent');
        label.textContent = `Visualizzazione di: ${filePath.split('/').pop()}`;
        container.innerHTML = '<div class="text-sm text-gray-500">Caricamento in corso…</div>';

        const ext = (filePath.split('.').pop() || '').toLowerCase();
        const fileUrl = `/<?= eq(current_lang()) ?>/bucket/${filePath}`;

        if (['png','jpg','jpeg','webp','svg','gif','bmp'].includes(ext)) {
          container.innerHTML = `<img src="${fileUrl}" alt="${filePath}" class="max-h-[70vh] w-auto rounded-lg border border-gray-200">`;
        } else if (['mp4','webm','ogg'].includes(ext)) {
          container.innerHTML = `<video controls class="w-full max-h-[70vh] rounded-lg border border-gray-200"><source src="${fileUrl}">Il tuo browser non supporta il tag video.</video>`;
        } else if (['pdf'].includes(ext)) {
          container.innerHTML = `<iframe src="${fileUrl}" class="w-full h-[70vh] rounded-lg border border-gray-200"></iframe>`;
        } else {
          fetch(fileUrl)
            .then(r => { if (!r.ok) throw new Error('Errore nel recupero del file.'); return r.text(); })
            .then(txt => {
              container.innerHTML = `<pre class="overflow-auto max-h-[70vh] rounded-lg bg-gray-50 p-4 border border-gray-200"><code>${escapeHtml(txt)}</code></pre>`;
            })
            .catch(err => {
              container.innerHTML = `<div class="rounded-lg border border-red-200 bg-red-50 text-red-800 px-4 py-2 text-sm">Errore: ${escapeHtml(err.message)}</div>`;
            });
        }
      }

      const copyModal = document.getElementById('copyFileModal');
      if (copyModal) {
        copyModal.addEventListener('click', (e) => {
          if (e.target === copyModal) closeModal(copyModal);
        });
        copyModal.addEventListener('tw:open', () => {});
      }
      document.getElementById('copyFileModal')?.addEventListener('transitionstart',()=>{});
      document.getElementById('copyFileModal')?.addEventListener('DOMNodeInserted',()=>{});

      function attachFillOnOpen(modalId, mappingFn) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        const observer = new MutationObserver(() => {});
        modal.addEventListener('click', (e) => {
          if (e.target === modal) closeModal(modal);
        });
        const fill = () => {
          const t = modal.__trigger; if (!t) return;
          mappingFn(modal, t);
        };
        const openHandler = () => fill();
        modal.addEventListener('transitionend', openHandler);
        fill();
      }

      attachFillOnOpen('copyFileModal', (modal, trigger) => {
        const src = trigger.getAttribute('data-source-file-path');
        const name = trigger.getAttribute('data-file-name');
        const inPath = modal.querySelector('#sourceFilePath');
        const inName = modal.querySelector('#newFileName');
        const disp = modal.querySelector('#sourceFileDisplay');
        if (inPath) inPath.value = src || '';
        if (disp) disp.textContent = name || src || '';
        if (inName) inName.value = name || '';
      });

      attachFillOnOpen('moveFileModal', (modal, trigger) => {
        const src = trigger.getAttribute('data-source-file-path');
        const name = trigger.getAttribute('data-file-name');
        modal.querySelector('#moveSourceFilePath')?.setAttribute('value', src || '');
        const disp = modal.querySelector('#moveSourceFileDisplay');
        if (disp) disp.textContent = name || src || '';
        const inName = modal.querySelector('#moveNewFileName');
        if (inName) inName.value = name || '';
      });

      attachFillOnOpen('renameFileModal', (modal, trigger) => {
        const path = trigger.getAttribute('data-file-path');
        const name = trigger.getAttribute('data-file-name');
        modal.querySelector('#renameSourceFilePath')?.setAttribute('value', path || '');
        const disp = modal.querySelector('#renameSourceFileDisplay');
        if (disp) disp.textContent = name || path || '';
        const inName = modal.querySelector('#renameNewFileName');
        if (inName) inName.value = name || '';
      });
    })();
  </script>
</body>

<?php partial('footer'); ?>
