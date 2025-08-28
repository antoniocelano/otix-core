<?php partial('head'); ?>

<body class="bg-body-tertiary">
    <div class="container mt-5">
        <h1 class="h3 mb-3 fw-normal">Elenco File su S3</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= eq($error) ?>
            </div>
        <?php else: ?>
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
                                <td>
                                    <?php if ($file['type'] !== 'folder'): ?>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#fileModal"
                                        data-file-path="<?= eq($file['path']) ?>"
                                    >
                                        Visualizza File
                                    </button>
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
    </script>
</body>

<?php partial('footer'); ?>