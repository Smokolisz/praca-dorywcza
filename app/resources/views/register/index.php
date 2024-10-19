<?php $this->startSection('title'); ?>
Zarejestruj się
<?php $this->endSection(); ?>

<div class="container my-6 is-max-tablet" style="padding-bottom:20px">
    <div class="box my-6">
        <h1 class="title">Zarejestruj się</h1>

        <form method="POST">

            <div class="field">
                <label class="label" for="first-name">Imię</label>
                <div class="control">
                    <input class="input" type="text" id="first-name" name="first-name" max="50" required>
                </div>
            </div>

            <div class="field">
                <label class="label" for="last-name">Nazwisko</label>
                <div class="control">
                    <input class="input" type="text" id="last-name" name="last-name" max="100" required>
                </div>
            </div>

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

            <div class="field">
                <label class="label" for="confirm-password">Powtórz hasło</label>
                <div class="control">
                    <input class="input" type="password" id="confirm-password" name="confirm-password" min="6" required>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" name="terms" required> &nbsp;
                        Akceptuję <a href="#">regulamin</a>
                    </label>
                </div>
            </div>

            <div class="field is-grouped">
                <div class="control">
                    <button type="submit" class="button is-link">Zarejestruj</button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php $this->startSection('scripts'); ?>
<!-- <script src="path/to/script.js"></script> -->
<?php $this->endSection(); ?>