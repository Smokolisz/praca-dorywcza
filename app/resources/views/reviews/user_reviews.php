<div class="container mt-5">
    <h2 class="title">
        <?php if ($isOwnProfile): ?>
            Opinie o Tobie
        <?php else: ?>
            Opinie o użytkowniku <?= htmlspecialchars($userName) ?>
        <?php endif; ?>
    </h2>

    <?php if (empty($reviews)): ?>
        <p>Brak opinii.</p>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="box mb-4">
                <p><strong>Wystawiona przez:</strong> <?= htmlspecialchars($review['reviewer_name']) ?></p>
                <p><strong>Ocena:</strong> <?= (int)$review['rating'] ?> / 5</p>
                <?php if (!empty($review['pros'])): ?>
                    <p><strong>Zalety:</strong> <?= htmlspecialchars($review['pros']) ?></p>
                <?php endif; ?>
                <?php if (!empty($review['cons'])): ?>
                    <p><strong>Wady:</strong> <?= htmlspecialchars($review['cons']) ?></p>
                <?php endif; ?>
                <?php if (!empty($review['comment'])): ?>
                    <p><strong>Komentarz:</strong> <?= htmlspecialchars($review['comment']) ?></p>
                <?php endif; ?>

                <p><small>Data wystawienia: <?= htmlspecialchars($review['created_at']) ?></small></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>