<?php $this->startSection('title'); ?>
Zapomniałeś hasła?
<?php $this->endSection(); ?>

<div class="container my-6 is-max-tablet" style="padding-bottom:20px">
    <div class="box my-6">

        <h1 class="title">Zapomniałeś hasła?</h1>
        <p>Wprowadź swój adres e-mail, aby otrzymać link do zresetowania hasła.</p>

        <?php if (!empty($_SESSION['password-reset-errors'])): ?>
            <div class="notification is-danger">
                <?php foreach ($_SESSION['password-reset-errors'] as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['password-reset-errors']); // Usuń błędy po wyświetleniu ?>
        <?php endif; ?>

        <?php if (isset($emailSent) && $emailSent): ?>
            <div class="notification is-success mt-4">
                <p>Na Twój adres e-mail został wysłany link do resetowania hasła. Sprawdź skrzynkę pocztową.</p>
            </div>
        <?php endif; ?>

        <form action="/resetuj-haslo/wyslij-email" method="POST" class="pt-3">

            <div class="field">
                <label class="label" for="email">Email</label>
                <div class="control">
                    <input class="input" type="email" id="email" name="email" required placeholder="Wprowadź swój adres e-mail">
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-link">Wyślij link resetujący</button>
                </div>
            </div>

        </form>
    </div>
</div>
