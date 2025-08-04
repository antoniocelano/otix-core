<?php partial('head'); ?>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
        <form action="/<?= eq(current_lang()) ?>/logout" method="get">
            <?php csrf_field(); ?>

            <button class="btn btn-primary w-100 py-2" type="submit">Esci</button>
        </form>
    </main>
</body>

<?php partial('footer'); ?>