<?php partial('head'); ?>
<?php disableCache(); ?>
<body>
    <div class="container mt-5">

        <?php if (Session::has('user_name')): ?>
            
            <h1>Benvenuto, <?= eq(Session::get('user_name')) ?> <?= eq(Session::get('user_surname')) ?>!</h1>
            <form action="/<?= eq(current_lang()) ?>/logout" method="get" class="mt-3">
                <?php csrf_field(); ?>
                <button class="btn btn-primary" type="submit">Logout</button>
            </form>

        <?php else: ?>

            <h1>Ciao!</h1>
            <p>Per continuare, accedi o registrati.</p>
            <a href="/<?= eq(current_lang()) ?>/login" class="btn btn-primary me-2">Login</a>
            <a href="/<?= eq(current_lang()) ?>/register" class="btn btn-secondary">Registrati</a>

        <?php endif; ?>

    </div>
</body>
<?php partial('footer'); ?>