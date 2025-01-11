<?php $this->startSection('title'); ?>
Historia wykonanych prac
<?php $this->endSection(); ?>

<div class="container is-fluid py-6">
    <div class="grid">
        <div class="cell">
            <h1 class="title is-3 has-text-centered">Historia wykonanych prac</h1>

            <?php if (empty($listings)): ?>
                <!-- Komunikat, gdy brak ogłoszeń -->
                <div class="notification is-info has-text-centered">
                    Brak historii wykonanych prac <br>
                </div>
            <?php else: ?>
                <!-- Lista ogłoszeń -->
                <div class="scroll-container py-3">
                    <?php foreach ($listings as $listing): ?>
                        <div class="box has-background-grey-darker">
                            <!-- Tytuł jako link do szczegółów -->
                            <p class="title is-4 no-gap">
                                <a href="/job/<?= htmlspecialchars($listing['id']) ?>" class="has-text-light">
                                    <?= htmlspecialchars($listing['job_type']) ?>
                                </a>
                            </p>

                            <p class="subtitle is-6 no-gap">
                                <span class="icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <?= htmlspecialchars($listing['city']) ?>, <?= htmlspecialchars($listing['address']) ?> |
                                <span class="icon">
                                    <i class="fas fa-file-contract"></i>
                                </span>
                                <?= htmlspecialchars($listing['payment_type']) ?> |
                                <span class="icon">
                                    <i class="fas fa-coins"></i>
                                </span>
                                <?= number_format($listing['payment'], 2) ?> zł/h
                            </p>
                            <div class="tags">
                                <span class="tag">Brak doświadczenia wymagane</span>
                                <span class="tag"><?= number_format($listing['payment'], 2) ?> zł/h</span>
                                <span class="icon has-text-danger is-pulled-right">
                                    <i class="fas fa-heart"></i>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->startSection('scripts'); ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const deleteButtons = document.querySelectorAll('.notification .delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', () => {
                const notification = button.parentElement;
                notification.remove(); // Usuwa element powiadomienia
            });
        });
    });
</script>

<?php $this->endSection(); ?>
