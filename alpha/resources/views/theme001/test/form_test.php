<?php partial('header'); ?>
<body>
    <main class="container mt-5">
        <div class="card">
            <div class="card-header"><h3>1. Inserisci Nuovo Utente</h3></div>
            <div class="card-body">
                <form action="/<?= current_lang() ?>/test-db/insert" method="POST">
                    <?php csrf_field(); ?>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Inserisci Utente</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
             <div class="card-header"><h3>2. Aggiorna Utente Esistente</h3></div>
             <div class="card-body">
                <form action="/<?= current_lang() ?>/test-db/update" method="POST">
                <?php csrf_field(); ?>
                    <div class="mb-3"><label for="update_id" class="form-label">ID Utente da modificare</label><input type="number" class="form-control" id="update_id" name="id" required placeholder="Es. 1"></div>
                    <div class="mb-3"><label for="update_name" class="form-label">Nuovo Nome</label><input type="text" class="form-control" id="update_name" name="name" required></div>
                    <div class="mb-3"><label for="update_email" class="form-label">Nuova Email</label><input type="email" class="form-control" id="update_email" name="email" required></div>
                    <button type="submit" class="btn btn-warning">Aggiorna Utente</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><h3>3. Elimina Utente Esistente</h3></div>
            <div class="card-body">
                <form action="/<?= current_lang() ?>/test-db/delete" method="POST">
                <?php csrf_field(); ?>
                    <div class="mb-3"><label for="delete_id" class="form-label">ID Utente da eliminare</label><input type="number" class="form-control" id="delete_id" name="id" required placeholder="Es. 2"></div>
                    <button type="submit" class="btn btn-danger">Elimina Utente</button>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header"><h3>4. Cerca Utente (SELECT)</h3></div>
            <div class="card-body">
                <form action="/<?= current_lang() ?>/test-db/select" method="POST">
                <?php csrf_field(); ?>
                    <div class="mb-3"><label for="select_id" class="form-label">ID Utente da cercare</label><input type="text" class="form-control" id="select_id" name="id" required placeholder="Es. 1"></div>
                    <button type="submit" class="btn btn-info">Cerca Utente</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header"><h3>5. Trova Ultimo Record Inserito</h3></div>
            <div class="card-body">
                <p>Questo test recupera l'intera riga dell'ultimo record inserito nella tabella (quello con l'ID più alto).</p>
                <form action="/<?= current_lang() ?>/test-db/find-last" method="POST">
                <?php csrf_field(); ?>
                    <button type="submit" class="btn btn-secondary">Trova Ultimo Record</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h3>6. Esegui Query Libera (SELECT)</h3>
            </div>
            <div class="card-body">
                <p>Inserisci una query <code>SELECT</code> completa. Per sicurezza, sono bloccati tutti gli altri tipi di comandi (<code>UPDATE</code>, <code>DELETE</code>, etc.).</p>
                <form action="/<?= current_lang() ?>/test-db/raw-query" method="POST">
                <?php csrf_field(); ?>
                    <div class="mb-3">
                        <label for="raw_sql" class="form-label">Query SQL</label>
                        <textarea class="form-control font-monospace" id="raw_sql" name="raw_sql" rows="4" placeholder="Es. SELECT id, name FROM test_users WHERE id > 1 ORDER BY name DESC"><?= isset($rawSql) ? eq($rawSql) : '' ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark">Esegui Query</button>
                </form>
            </div>
        </div>


        <?php if (isset($message)): ?>
        <div class="card mt-4" style="position: fixed;top: 0;z-index: 999;left: 30px;max-width: 36rem;">
            <div class="card-header"><h5>Risultato Operazione</h5></div>
            <div class="card-body">
                <div class="alert alert-<?= $status === 'success' ? 'success' : ($status === 'danger' ? 'danger' : 'info') ?>" role="alert">
                    <p class="mb-0 font-monospace"><?= $message ?></p>
                </div>
                
                <?php if (!empty($results)): ?>
                    <?php if (count($results) === 1): ?>
                        <dl class="row mt-3">
                            <?php foreach ($results[0] as $key => $value): ?>
                                <dt class="col-sm-3"><?= eq(ucfirst(str_replace('_', ' ', $key))) ?></dt>
                                <dd class="col-sm-9 font-monospace"><?= eq($value) ?></dd>
                            <?php endforeach; ?>
                        </dl>
                    <?php else: ?>
                        <table class="table table-striped table-bordered mt-3">
                            <thead class="table-dark">
                                <tr>
                                    <?php foreach (array_keys($results[0]) as $header): ?>
                                        <th><?= eq(ucfirst(str_replace('_', ' ', $header))) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $row): ?>
                                <tr>
                                    <?php foreach ($row as $cell): ?>
                                        <td><?= eq($cell) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
         <a href="/" class="btn btn-secondary btn-sm my-3">Torna alla Home</a>
    </main>
</body>
</html>