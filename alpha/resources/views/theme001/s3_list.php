<?php partial('head'); ?>

<body class="bg-body-tertiary">
    <div class="container mt-5">
        <h1 class="h3 mb-3 fw-normal">Elenco File su S3</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= eq($error) ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/<?= eq(current_lang()) ?>/s3/list">Bucket: <?= eq($bucket) ?></a></li>
                            <?php if (!empty($breadcrumbs)): ?>
                                <?php foreach ($breadcrumbs as $name => $path): ?>
                                    <?php if ($path === null): ?>
                                        <li class="breadcrumb-item active" aria-current="page"><?= eq($name) ?></li>
                                    <?php else: ?>
                                        <li class="breadcrumb-item"><a href="/<?= eq(current_lang()) ?>/s3/list/<?= enc_path($path) ?>"><?= eq($name) ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </nav>
                </div>
                <div class="col-md-4 text-end">
                    <form action="/<?= eq(current_lang()) ?>/s3/upload" method="post" enctype="multipart/form-data" class="d-flex justify-content-end align-items-center">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">
                        <input type="file" name="fileToUpload" id="fileToUpload" class="form-control form-control-sm me-2" required>
                        <button type="submit" class="btn btn-sm btn-success">Carica</button>
                    </form>
                </div>
            </div>

            <?php if (isset($_SESSION['upload_error'])): ?>
                <div class="alert alert-danger mt-3"><?= eq($_SESSION['upload_error']); unset($_SESSION['upload_error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['upload_success'])): ?>
                <div class="alert alert-success mt-3"><?= eq($_SESSION['upload_success']); unset($_SESSION['upload_success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['delete_error'])): ?>
                <div class="alert alert-danger mt-3"><?= eq($_SESSION['delete_error']); unset($_SESSION['delete_error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['delete_success'])): ?>
                <div class="alert alert-success mt-3"><?= eq($_SESSION['delete_success']); unset($_SESSION['delete_success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['copy_error'])): ?>
                <div class="alert alert-danger mt-3"><?= eq($_SESSION['copy_error']); unset($_SESSION['copy_error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['copy_success'])): ?>
                <div class="alert alert-success mt-3"><?= eq($_SESSION['copy_success']); unset($_SESSION['copy_success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['move_error'])): ?>
                <div class="alert alert-danger mt-3"><?= eq($_SESSION['move_error']); unset($_SESSION['move_error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['move_success'])): ?>
                <div class="alert alert-success mt-3"><?= eq($_SESSION['move_success']); unset($_SESSION['move_success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['rename_error'])): ?>
                <div class="alert alert-danger mt-3"><?= eq($_SESSION['rename_error']); unset($_SESSION['rename_error']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['rename_success'])): ?>
                <div class="alert alert-success mt-3"><?= eq($_SESSION['rename_success']); unset($_SESSION['rename_success']); ?></div>
            <?php endif; ?>


            <?php if (empty($files)): ?>
                <div class="alert alert-info" role="alert">
                    Questa cartella è vuota.
                </div>
            <?php else: ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Dimensione</th>
                            <th>Ultima Modifica</th>
                            <th>Azione</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($files as $file): ?>
                            <tr>
                                <td>
                                    <?php if ($file['type'] === 'folder'): ?>
                                        <a href="/<?= eq(current_lang()) ?>/s3/list/<?= enc_path($file['path']) ?>">
                                            <i class="bi bi-folder-fill"></i> <?= eq($file['name']) ?>
                                        </a>
                                    <?php else: ?>
                                        <i class="bi bi-file-earmark-code-fill"></i> <?= eq($file['name']) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= eq($file['type']) ?></td>
                                <td>
                                    <?php if ($file['type'] !== 'folder'): ?>
                                        <?= eq(formatBytes($file['size'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($file['type'] !== 'folder'): ?>
                                        <?= eq(formatDate($file['lastModified'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="d-flex">
                                    <?php if ($file['type'] !== 'folder'): ?>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary me-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#fileModal"
                                        data-file-path="<?= eq($file['path']) ?>"
                                    >
                                        Apri
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-info me-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#copyFileModal"
                                        data-source-file-path="<?= eq($file['path']) ?>"
                                        data-file-name="<?= eq($file['name']) ?>"
                                    >
                                        Copia
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-warning me-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#moveFileModal"
                                        data-source-file-path="<?= eq($file['path']) ?>"
                                        data-file-name="<?= eq($file['name']) ?>"
                                    >
                                        Sposta
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary me-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#renameFileModal"
                                        data-file-path="<?= eq($file['path']) ?>"
                                        data-file-name="<?= eq($file['name']) ?>"
                                    >
                                        Rinomina
                                    </button>
                                    <form action="/<?= eq(current_lang()) ?>/s3/delete" method="post" onsubmit="return confirm('Sei sicuro di voler eliminare il file <?= addslashes($file['name']) ?>?');">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="file_path" value="<?= eq($file['path']) ?>">
                                        <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Elimina</button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>

        <p class="mt-5 text-center text-muted">
            Torna alla <a href="/">home</a>.
        </p>
    </div>

    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Contenuto File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="fileContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="copyFileModal" tabindex="-1" aria-labelledby="copyFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="copyFileModalLabel">Copia File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/<?= eq(current_lang()) ?>/s3/copy" method="post" id="copyFileForm">
                    <div class="modal-body">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="source_file_path" id="sourceFilePath">
                        <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">

                        <div class="mb-3">
                            <label class="form-label"><strong>File da copiare:</strong></label>
                            <p id="sourceFileDisplay" class="text-muted"></p>
                        </div>

                        <div class="mb-3">
                            <label for="destinationFolder" class="form-label">Scegli la cartella di destinazione</label>
                            <select class="form-select" id="destinationFolder" name="destination_folder_path">
                                <option value="" selected>Radice del Bucket</option>
                                <?php
                                if (!empty($all_folders)):
                                    foreach ($all_folders as $folder): ?>
                                    <option value="<?= eq(rtrim($folder, '/')) ?>"><?= eq(rtrim($folder, '/')) ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="newFileName" class="form-label">Nuovo nome del file</label>
                            <input type="text" class="form-control" id="newFileName" name="new_file_name" placeholder="Lascia vuoto per mantenere il nome originale">
                            <small class="form-text text-muted">Se vuoi rinominare il file, inserisci il nuovo nome. Altrimenti verrà mantenuto il nome originale.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Copia File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="moveFileModal" tabindex="-1" aria-labelledby="moveFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveFileModalLabel">Sposta File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/<?= eq(current_lang()) ?>/s3/move" method="post" id="moveFileForm">
                    <div class="modal-body">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="source_file_path" id="moveSourceFilePath">
                        <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">

                        <div class="mb-3">
                            <label class="form-label"><strong>File da spostare:</strong></label>
                            <p id="moveSourceFileDisplay" class="text-muted"></p>
                        </div>

                        <div class="mb-3">
                            <label for="moveDestinationFolder" class="form-label">Scegli la cartella di destinazione</label>
                            <select class="form-select" id="moveDestinationFolder" name="destination_folder_path">
                                <option value="" selected>Radice del Bucket</option>
                                <?php
                                if (!empty($all_folders)):
                                    foreach ($all_folders as $folder): ?>
                                    <option value="<?= eq(rtrim($folder, '/')) ?>"><?= eq(rtrim($folder, '/')) ?></option>
                                <?php endforeach;
                                endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="moveNewFileName" class="form-label">Nuovo nome del file</label>
                            <input type="text" class="form-control" id="moveNewFileName" name="new_file_name" placeholder="Lascia vuoto per mantenere il nome originale">
                            <small class="form-text text-muted">Se vuoi rinominare il file, inserisci il nuovo nome. Altrimenti verrà mantenuto il nome originale.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-warning">Sposta File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="renameFileModal" tabindex="-1" aria-labelledby="renameFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="renameFileModalLabel">Rinomina File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/<?= eq(current_lang()) ?>/s3/rename" method="post" id="renameFileForm">
                    <div class="modal-body">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="source_file_path" id="renameSourceFilePath">
                        <input type="hidden" name="current_path" value="<?= eq($current_prefix ?? '') ?>">

                        <div class="mb-3">
                            <label class="form-label"><strong>File da rinominare:</strong></label>
                            <p id="renameSourceFileDisplay" class="text-muted"></p>
                        </div>

                        <div class="mb-3">
                            <label for="newFileName" class="form-label">Nuovo nome del file</label>
                            <input type="text" class="form-control" id="renameNewFileName" name="new_file_name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary">Rinomina</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const fileModal = document.getElementById('fileModal');
        fileModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const filePath = button.getAttribute('data-file-path');
            const fileModalLabel = fileModal.querySelector('.modal-title');
            const fileContentContainer = fileModal.querySelector('#fileContent');

            fileModalLabel.textContent = `Visualizzazione di: ${filePath.split('/').pop()}`;
            fileContentContainer.innerHTML = 'Caricamento in corso...';

            const fileExtension = filePath.split('.').pop().toLowerCase();
            const fileUrl = `/<?= eq(current_lang()) ?>/bucket/${filePath}`;
            
            if (['png', 'jpg', 'jpeg', 'webp', 'svg'].includes(fileExtension)) {
                fileContentContainer.innerHTML = `<img src="${fileUrl}" class="img-fluid" alt="${filePath}">`;
            } else if (fileExtension === 'mp4') {
                fileContentContainer.innerHTML = `<video controls class="w-100"><source src="${fileUrl}" type="video/mp4">Il tuo browser non supporta il tag video.</video>`;
            } else {
                fetch(fileUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Errore nel recupero del file.');
                        }
                        return response.text();
                    })
                    .then(content => {
                        fileContentContainer.innerHTML = `<pre><code>${eq(content)}</code></pre>`;
                    })
                    .catch(error => {
                        fileContentContainer.innerHTML = `<div class="alert alert-danger" role="alert">Errore: ${error.message}</div>`;
                    });
            }
        });

        const copyFileModal = document.getElementById('copyFileModal');
        copyFileModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const sourceFilePath = button.getAttribute('data-source-file-path');
            const fileName = button.getAttribute('data-file-name');

            const modalSourceFilePath = copyFileModal.querySelector('#sourceFilePath');
            const modalNewFileName = copyFileModal.querySelector('#newFileName');
            
            if (!modalSourceFilePath) {
                return;
            }
            
            modalSourceFilePath.value = sourceFilePath;
            if (modalNewFileName) {
                modalNewFileName.value = fileName;
            }
            
        });

        document.getElementById('copyFileForm').addEventListener('submit', function(e) {
            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
            }
        });

        const moveFileModal = document.getElementById('moveFileModal');
        moveFileModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const sourceFilePath = button.getAttribute('data-source-file-path');
            const fileName = button.getAttribute('data-file-name');

            const modalSourceFilePath = moveFileModal.querySelector('#moveSourceFilePath');
            const modalNewFileName = moveFileModal.querySelector('#moveNewFileName');
            const modalSourceFileDisplay = moveFileModal.querySelector('#moveSourceFileDisplay');

            if (!modalSourceFilePath) {
                return;
            }

            modalSourceFilePath.value = sourceFilePath;
            modalSourceFileDisplay.textContent = fileName;
            if (modalNewFileName) {
                modalNewFileName.value = fileName;
            }
        });

        document.getElementById('moveFileForm').addEventListener('submit', function(e) {
            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
            }
        });

        const renameFileModal = document.getElementById('renameFileModal');
        renameFileModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const filePath = button.getAttribute('data-file-path');
            const fileName = button.getAttribute('data-file-name');

            const modalSourceFilePath = renameFileModal.querySelector('#renameSourceFilePath');
            const modalNewFileName = renameFileModal.querySelector('#renameNewFileName');
            const modalSourceFileDisplay = renameFileModal.querySelector('#renameSourceFileDisplay');

            if (!modalSourceFilePath) {
                return;
            }

            modalSourceFilePath.value = filePath;
            modalSourceFileDisplay.textContent = fileName;
            if (modalNewFileName) {
                modalNewFileName.value = fileName;
            }
        });
    </script>
</body>

<?php partial('footer'); ?>