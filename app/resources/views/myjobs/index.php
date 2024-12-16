<?php $this->startSection('title'); ?>
Moje Prace
<?php $this->endSection(); ?>

<div class="container is-fluid py-6">
    <div class="grid">
        <div class="cell">
            <h1 class="title is-3 has-text-centered">Moje Prace</h1>

            <?php if (empty($jobs)): ?>
                <!-- Komunikat, gdy brak prac -->
                <div class="notification is-info has-text-centered">
                    Nie masz jeszcze przyjętych prac. <br>
                    <a href="/" class="button is-primary mt-3">Przeglądaj ogłoszenia</a>
                </div>
            <?php else: ?>
                <!-- Lista prac -->
                <div class="scroll-container py-3">
                    <?php foreach ($jobs as $job): ?>
                        <div class="box has-background-grey-darker">
                            <!-- Tytuł jako link do szczegółów -->
                            <p class="title is-4 no-gap">
                                <a href="/job/<?= htmlspecialchars($job['id']) ?>" class="has-text-light">
                                    <?= htmlspecialchars($job['job_type']) ?>
                                </a>
                            </p>

                            <p class="subtitle is-6 no-gap">
                                <span class="icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['address']) ?> |
                                <span class="icon">
                                    <i class="fas fa-file-contract"></i>
                                </span>
                                <?= htmlspecialchars($job['payment_type']) ?> |
                                <span class="icon">
                                    <i class="fas fa-coins"></i>
                                </span>
                                <?= number_format($job['payment'], 2) ?> zł/h
                            </p>
                            <div class="tags">
                                <span class="tag">Data akceptacji: <?= htmlspecialchars($job['accepted_date']) ?></span>
                                <span class="tag"><?= number_format($job['payment'], 2) ?> zł/h</span>
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
