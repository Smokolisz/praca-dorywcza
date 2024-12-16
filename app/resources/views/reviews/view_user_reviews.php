<div class="container my-6">
    <h1 class="title has-text-centered">
        <?php if ($isOwnProfile): ?>
            Opinie o Tobie
        <?php else: ?>
            Opinie o użytkowniku <?= htmlspecialchars($userName) ?>
        <?php endif; ?>
    </h1>

    <?php if (empty($reviews)): ?>
        <div class="notification is-warning">Brak opinii o tym użytkowniku.</div>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="box mb-4">
                <p><strong>Wystawiona przez:</strong> <?= htmlspecialchars($review['reviewer_name']) ?></p>
                <p><strong>Ocena:</strong> <span class="has-text-info"><?= (int)$review['rating'] ?> / 5</span></p>
                
                <?php if (!empty($review['pros'])): ?>
                    <p><strong class="has-text-success">Zalety:</strong> <?= htmlspecialchars($review['pros']) ?></p>
                <?php endif; ?>

                <?php if (!empty($review['cons'])): ?>
                    <p><strong class="has-text-danger">Wady:</strong> <?= htmlspecialchars($review['cons']) ?></p>
                <?php endif; ?>

                <?php if (!empty($review['comment'])): ?>
                    <p><strong>Komentarz:</strong> <?= htmlspecialchars($review['comment']) ?></p>
                <?php endif; ?>

                <p class="is-size-7 has-text-grey-light">
                    <small>Data wystawienia: <?= htmlspecialchars($review['created_at']) ?></small>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

