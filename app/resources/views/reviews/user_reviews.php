<div class="container mt-5">
    <h2 class="title">Opinie o Tobie</h2>

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

                <?php
                // Wyświetlanie zdjęć
                // Zakładamy, że kolumna "photos" zawiera dane w formacie JSON np. ["image1.jpg", "image2.jpg"]
                // oraz że zdjęcia są przechowywane w katalogu "/review_photos/" w publicznej ścieżce.
                if (!empty($review['photos'])):
                    $photos = json_decode($review['photos'], true);
                    if (is_array($photos) && count($photos) > 0):
                ?>
                        <div class="columns is-multiline mt-3">
                            <?php foreach ($photos as $photo): ?>
                                <div class="column is-one-quarter">
                                    <figure class="image">
                                        <img src="/review_photos/<?= htmlspecialchars($photo) ?>" alt="Zdjęcie z opinii">
                                    </figure>
                                </div>
                            <?php endforeach; ?>
                        </div>
                <?php
                    endif;
                endif;
                ?>

                <p><small>Data wystawienia: <?= htmlspecialchars($review['created_at']) ?></small></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>