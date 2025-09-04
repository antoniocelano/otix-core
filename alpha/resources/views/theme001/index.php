<?php partial('head'); ?>
<?php disableCache(); ?>
<body>
    <div class="container mt-5">

        <?php if (isset($_SESSION['user_name'])): ?>
            
            <h1>Benvenuto, <?= eq($_SESSION['user_name']) ?> <?= eq($_SESSION['user_surname']) ?>!</h1>
            <img src="/static/images/1.png">
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
