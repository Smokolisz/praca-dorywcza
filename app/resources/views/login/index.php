<?php $this->startSection('title'); ?>
Zaloguj się
<?php $this->endSection(); ?>

<div class="container my-6 is-max-tablet" style="padding-bottom:20px">
    <div class="box my-6">
        <h1 class="title">Zaloguj się</h1>

        <form method="POST">

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
                    <button type="submit" class="button is-link">Zaloguj</button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php $this->startSection('scripts'); ?>
<!-- <script src="path/to/script.js"></script> -->
<?php $this->endSection(); ?>