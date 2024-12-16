<div class="container mt-5">
    <h2 class="title">Dodaj Opinię</h2>
    <form action="/opinie/dodaj" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="listing_id" value="<?= htmlspecialchars($listingId); ?>">
        <input type="hidden" name="reviewed_user_id" value="<?= htmlspecialchars($reviewedUserId); ?>">

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

        <div class="control">
            <button class="button is-primary" type="submit">Dodaj opinię</button>
        </div>
    </form>
</div>