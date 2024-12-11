<div class="container mt-5">
    <h2 class="title">Dodaj Opinię</h2>
    <form action="/opinie/dodaj" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="negotiation_id" value="<?= htmlspecialchars($negotiationId) ?>">
        <input type="hidden" name="reviewed_user_id" value="<?= htmlspecialchars($reviewedUserId) ?>">

        <div class="field">
            <label class="label">Ocena (1-5)</label>
            <div class="control">
                <input class="input" type="number" name="rating" min="1" max="5" required>
            </div>
        </div>

        <div class="field">
            <label class="label">Zalety</label>
            <div class="control">
                <input class="input" type="text" name="pros" placeholder="Zalety wykonania zlecenia">
            </div>
        </div>

        <div class="field">
            <label class="label">Wady</label>
            <div class="control">
                <input class="input" type="text" name="cons" placeholder="Wady wykonania zlecenia">
            </div>
        </div>

        <div class="field">
            <label class="label">Komentarz (opcjonalny)</label>
            <div class="control">
                <textarea class="textarea" name="comment" placeholder="Napisz swoją opinię"></textarea>
            </div>
        </div>

        <!-- Sekcja przesyłania zdjęć podobna do przesyłania zdjęcia profilowego -->
        <div class="field">
            <label class="label">Dodaj zdjęcia (opcjonalnie)</label>
            <div class="file has-name is-fullwidth mb-3">
                <label class="file-label is-fullwidth">
                    <input class="file-input" type="file" name="photos[]" multiple onchange="updateFileName(this)">
                    <span class="file-cta is-fullwidth">
                        <span class="icon"><i class="fas fa-upload"></i></span>
                        <span>Wybierz pliki</span>
                    </span>
                    <span class="file-name">Nie wybrano pliku</span>
                </label>
            </div>
        </div>

        <div class="control">
            <button class="button is-primary" type="submit">Dodaj opinię</button>
        </div>
    </form>
</div>

<script>
    function updateFileName(input) {
        // Jeżeli wiele plików, wyświetlamy ilość plików zamiast pojedynczej nazwy
        if (input.files.length > 1) {
            input.closest('.file').querySelector('.file-name').textContent = input.files.length + " plików wybrano";
        } else {
            const fileName = input.files[0]?.name || "Nie wybrano pliku";
            input.closest('.file').querySelector('.file-name').textContent = fileName;
        }
    }
</script>