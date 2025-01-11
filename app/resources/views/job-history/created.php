<?php $this->startSection('title'); ?>
Historia utworzonych ogłoszeń
<?php $this->endSection(); ?>

<div class="container is-fluid py-6">

    <div class="tabs is-medium is-centered">
        <ul>
            <li class="is-active"><a href="/archiwum/utworzone">Utworzone</a></li>
            <li><a href="/archiwum/wykonane">Wykonane</a></li>
        </ul>
    </div>

    <div class="grid">
        <div class="cell">
            <h1 class="title is-3 has-text-centered">Historia utworzonych ogłoszeń</h1>

            <?php if (empty($listings)): ?>
                <!-- Komunikat, gdy brak ogłoszeń -->
                <div class="notification is-info has-text-centered">
                    Brak dodanych ogłoszeń <br>
                </div>
            <?php else: ?>
                <!-- Lista ogłoszeń -->
                <div class="py-3">
                    <?php foreach ($listings as $listing): ?>
                        <div class="">
                            <!-- Tytuł jako link do szczegółów -->
                            <p class="title is-4 no-gap">
                                <a href="/job/<?= htmlspecialchars($listing->id) ?>" class="has-text-light">
                                    <?= htmlspecialchars($listing->job_type ?? '') ?>
                                </a>
                            </p>

                            <p class="subtitle is-6 no-gap">
                                <span class="icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <?= htmlspecialchars($listing->city ?? '') ?>, <?= htmlspecialchars($listing->address ?? '') ?> |
                                <span class="icon">
                                    <i class="fas fa-file-contract"></i>
                                </span>
                                <?= htmlspecialchars($listing->payment_type ?? '') ?> |
                                <span class="icon">
                                    <i class="fas fa-coins"></i>
                                </span>
                                <?= number_format($listing->payment ?? 0, 2) ?> zł/h
                            </p>
                            <div class="tags">
                                <span class="tag"><?= number_format($listing->payment ?? 0, 2) ?> zł/h</span>
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