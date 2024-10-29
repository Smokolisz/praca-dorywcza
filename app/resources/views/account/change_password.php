<div class="container mt-6">
    <h2 class="title is-4">Zmień hasło</h2>

    <!-- Wyświetlanie błędu, jeśli istnieje -->
    <?php if (isset($error)): ?>
        <div class="notification is-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="/profil/zmien-haslo" method="POST">
        <div class="column is-half">
            <div class="field">
                <label class="label" for="current_password">Obecne hasło:</label>
                <div class="control">
                    <input class="input" type="password" id="current_password" name="current_password" required>
                </div>
            </div>

            <div class="field">
                <label class="label" for="new_password">Nowe hasło:</label>
                <div class="control">
                    <input class="input" type="password" id="new_password" name="new_password" required>
                </div>
            </div>
            <!-- Przycisk zapisania zmian i anulowania -->

            <div class="control">
                <button class="button is-link" type="submit">Zapisz nowe hasło</button>
                <a href="/profil" class="button is-light ml-2">Anuluj</a>
            </div>
        </div>
    </form>
</div>