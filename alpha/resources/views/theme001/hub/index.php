<?php
use App\Core\Session;

partialHub('head'); 
?>
<body>
    <div class="container">
        <h1 class="mt-5">Ciao, <?= eq(Session::get('hub_user_name')) ?> (hub)!</h1>
        <p>Questa è la dashboard segreta dell'Hub.</p>
        <form action="/<?= eq(current_lang()) ?>/hub/logout" method="get">
            <?php csrf_field(); ?>
            <button class="btn btn-danger" type="submit">Logout dall'Hub</button>
        </form>
    </div>
</body>
<?php partialHub('footer'); ?>