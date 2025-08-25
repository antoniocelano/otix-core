<?php partial('head'); ?>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto container">
        <form action="/<?= eq(current_lang()) ?>/password/reset" method="post">
            <?php csrf_field(); ?>
            <input type="hidden" name="token" value="<?= eq($token) ?>">
            <h1 class="h3 mb-3 fw-normal">Reset Password</h1>
            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Nuova Password" name="password" required>
                <label for="floatingPassword">Nuova Password</label>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Imposta Nuova Password</button>
        </form>
    </main>
</body>

<?php partial('footer'); ?>