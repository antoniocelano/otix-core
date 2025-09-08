<?php
use App\Core\Session; 

partialAdmin('head'); ?>

<body class="d-flex align-items-center py-4">
    <main class="form-signin w-100 m-auto container">
        <h1>Benvenuto, <?= eq(Session::get('user_name') ?? 'Ospite') ?> (Admin)!</h1>
        <form action="/<?= eq(current_lang()) ?>/logout" method="get">
            <?php csrf_field(); ?>
            <button class="btn btn-primary w-100 py-2" type="submit">Esci</button>
        </form>
    </main>
</body>

<?php partialAdmin('footer'); ?>