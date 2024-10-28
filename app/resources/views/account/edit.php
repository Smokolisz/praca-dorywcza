<div class="container mt-6">
    <h2 class="title is-4">Edytuj profil</h2>

    <form action="/profil/edytuj" method="POST">
        <div class="column is-half">
            <!-- Pola do edycji podstawowych informacji -->
            <h3 class="title is-5">Podstawowe informacje</h3>
            <div class="field">
                <label class="label">Imię</label>
                <div class="control">
                    <input class="input" type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Nazwisko</label>
                <div class="control">
                    <input class="input" type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                </div>
            </div>
            <!-- Przycisk zapisania zmian i anulowania -->

            <button class="button is-link" type="submit">Zapisz zmiany</button>
            <a href="/profil" class="button is-light ml-2">Anuluj</a>
        </div>
    </form>
</div>