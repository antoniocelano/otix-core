<?php partial('head'); ?>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto container">

      <?php if(isset($_SESSION['email_notfound'])) { ?>
        <div class="alert alert-danger mt-3"><?= eq($_SESSION['email_notfound']); ?></div>
        <?php 
      } ?>

      <?php if(isset($_SESSION['recover_ok'])) { ?>
        <div class="alert alert-success mt-3"><?= eq($_SESSION['recover_ok']); ?></div>
        <?php 
      } ?>
      
        <form action="/<?= eq(current_lang()) ?>/login" method="post">
            <?php csrf_field(); ?>
            <h1 class="h3 mb-3 fw-normal">Login</h1>
            <div class="form-floating mb-4">
                <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email">
                <label for="floatingInput">Email</label>
            </div>
            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
                <label for="floatingPassword">Password</label>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Entra</button>
            <div class="d-flex justify-content-between mt-3">
                <a href="/<?= eq(current_lang()) ?>/register" class="btn btn-link">Registrati</a>
                <a href="#" class="btn btn-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Password dimenticata?</a>
            </div>
        </form>
    </main>

    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="forgotPasswordModalLabel">Recupero Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="/<?= eq(current_lang()) ?>/password/forgot" method="post">
              <div class="modal-body">
                    <?php csrf_field(); ?>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingEmail" placeholder="name@example.com" name="email" required>
                        <label for="floatingEmail">Indirizzo Email</label>
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="submit" class="btn btn-primary">Invia Link di Recupero</button>
              </div>
          </form>
        </div>
      </div>
    </div>
</body>

<?php partial('footer'); ?>