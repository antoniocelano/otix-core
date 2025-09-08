<?php 
use App\Core\Session;
partial('head'); 
?>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto container">
        <h1 class="h3 mb-3 fw-normal">Registrati</h1>
        
        <?php if (Session::has('error_message')): ?>
            <div class="alert alert-danger"><?= eq(Session::get('error_message')); ?></div>
        <?php endif; ?>
        <?php if (Session::has('success_message')): ?>
            <div class="alert alert-success"><?= eq(Session::get('success_message')); ?></div>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <form action="/<?= eq(current_lang()) ?>/register/send-otp" method="post">
                <?php csrf_field(); ?>
                <p>Inserisci la tua email per ricevere un codice di verifica.</p>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="floatingEmail" placeholder="name@example.com" name="email" required>
                    <label for="floatingEmail">Indirizzo Email</label>
                </div>
                <button class="btn btn-primary w-100 py-2" type="submit">Invia Codice OTP</button>
            </form>
        <?php else: ?>
            <form action="/<?= eq(current_lang()) ?>/register" method="post">
                <?php csrf_field(); ?>
                
                <div class="form-floating mb-2">
                    <input type="email" class="form-control" id="floatingInput" value="<?= eq($email_for_otp) ?>" readonly>
                    <label for="floatingInput">Email</label>
                </div>
                <div class="form-floating mb-2">
                    <input type="text" class="form-control" id="floatingName" placeholder="Nome" name="name" required>
                    <label for="floatingName">Nome</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" required>
                    <label for="floatingPassword">Password</label>
                </div>
                <div class="form-floating mt-2">
                    <input type="text" class="form-control" id="floatingOtp" placeholder="Codice OTP" name="otp" required>
                    <label for="floatingOtp">Codice OTP</label>
                </div>

                <button class="btn btn-primary w-100 py-2 mt-3" type="submit">Completa Registrazione</button>
            </form>
        <?php endif; ?>
        
        <p class="mt-3 text-center">
            Hai già un account? <a href="/<?= eq(current_lang()) ?>/login">Accedi</a>
        </p>
    </main>
</body>

<?php partial('footer'); ?>