<?php $this->startSection('title'); ?>
Zaloguj się
<?php $this->endSection(); ?>

<div class="container my-6 is-max-tablet" style="padding-bottom:20px">
    <div class="box my-6">

        <?php if (isset($isJustRegistered) && $isJustRegistered): ?>
            <div class="notification is-success">
                <p>Rejestracja przebiegła pomyślnie. Zweryfikuj adres e-mail żeby móc się zalogować.</p>
            </div>
        <?php endif; ?>

        <h1 class="title">Zaloguj się</h1>

        <?php if (!empty($_SESSION['login_errors'])): ?>
            <div class="notification is-danger">
                <?php foreach ($_SESSION['login_errors'] as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['login_errors']); // Usuń błędy po wyświetleniu 
            ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['password-reset-successfully'])): ?>
            <?php unset($_SESSION['password-reset-successfully']); ?>
            <div class="notification is-success">
                <p>Hasło zostało zmienione pomyślnie.</p>
            </div>
        <?php endif; ?>

        <form action="/zaloguj-sie" method="POST">

            <div class="field">
                <label class="label" for="email">Email</label>
                <div class="control">
                    <input class="input" type="email" id="email" name="email" max="255" required>
                </div>
            </div>

            <div class="field">
                <label class="label" for="password">Hasło</label>
                <div class="control">
                    <input class="input" type="password" id="password" name="password" required>
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-primary">Zaloguj</button>
                </div>
                <div class="control">
                    <a href="/resetuj-haslo" class="button">Przypomnij hasło</a>
                </div>
            </div>

        </form>
    </div>
</div>

<?php $this->startSection('scripts'); ?>
<!-- <script src="path/to/script.js"></script> -->
<?php $this->endSection(); ?>
