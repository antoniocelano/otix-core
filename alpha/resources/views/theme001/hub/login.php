<?php partialHub('head'); ?>

<body class="d-flex align-items-center py-4 bg-dark text-white">
    <div class="form-signin w-100 m-auto container">
        <form action="/<?= eq(current_lang()) ?>/hub/login" method="post">
            <?php csrf_field(); ?>
            <h1 class="h3 mb-3 fw-normal">Hub Login</h1>

            <?php if (isset($_SESSION['hub_error_message'])): ?>
                <div class="alert alert-danger"><?= eq($_SESSION['hub_error_message']); unset($_SESSION['hub_error_message']); ?></div>
            <?php endif; ?>

            <div class="form-floating">
                <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" required>
                <label for="floatingInput" class="text-dark">Email</label>
            </div>
            <div class="form-floating mt-2">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" required>
                <label for="floatingPassword" class="text-dark">Password</label>
            </div>

            <button class="btn btn-primary w-100 py-2 mt-3" type="submit">Entra nell'Hub</button>
        </form>
    </div>
</body>