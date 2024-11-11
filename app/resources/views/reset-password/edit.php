<?php $this->startSection('title'); ?>
Resetuj hasło
<?php $this->endSection(); ?>

<div class="container my-6 is-max-tablet" style="padding-bottom:20px">
    <div class="box my-6">

        <h1 class="title">Resetuj hasło</h1>

        <?php if (!empty($_SESSION['reset_errors'])): ?>
            <div class="notification is-danger">
                <?php foreach ($_SESSION['reset_errors'] as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['reset_errors']); // Usuń błędy po wyświetleniu 
            ?>
        <?php endif; ?>

        <form action="/resetuj-haslo/<?= $token ?>" method="POST">

            <div class="field">
                <label class="label" for="new_password">Nowe hasło</label>
                <div class="control">
                    <input class="input" type="password" id="new_password" name="new_password" required minlength="8">
                </div>
            </div>

            <div class="field">
                <label class="label" for="confirm_password">Potwierdź nowe hasło</label>
                <div class="control">
                    <input class="input" type="password" id="confirm_password" name="confirm_password" required minlength="8">
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-link">Zresetuj hasło</button>
                </div>
            </div>

        </form>
    </div>
</div>
