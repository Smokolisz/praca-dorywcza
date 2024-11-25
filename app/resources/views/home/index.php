<?php $this->startSection('title'); ?>
Strona Główna
<?php $this->endSection(); ?>
<div class="strona-glowna pt-6">   

<div class="grid">
        <div class="cell">
            <h1 class="title is-1">Praca dla ciebie!</h1>
            <p class="subtitle is-5">
                Szukasz pracy dorywczej lub potrzebujesz wsparcia w codziennych zadaniach?
                U nas znajdziesz setki ofert dostosowanych do Twoich potrzeb - szybko, wygodnie i bez zbędnych formalności.
                Dołącz do naszej społeczności, przeglądaj ogłoszenia w swojej okolicy lub wystaw własne i zacznij działać już dziś!
            </p>
        </div>

        <div class="cell">
            <h1 class="title is-3">Popularne ogłoszenia</h1>
            <div class="scroll-container">
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
                        <?= htmlspecialchars($listing['city']) ?>, <?= htmlspecialchars($listing['address']) ?> 
                    </p>
                    <div class="tags">
                    |
                        <span class="icon">
                            <i class="fas fa-file-contract"></i>
                        </span>
                        <?= htmlspecialchars($listing['payment_type']) ?> |
                        <span class="icon">
                            <i class="fas fa-coins"></i>
                        </span>
                        <?= number_format($listing['payment'], 2) ?> zł/h
                        |
                        <span>Brak dodatkowych informacji</span>
                        <span class="icon has-text-danger is-pulled-right">
                            <i class="fas fa-heart"></i>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->startSection('head'); ?>
<link rel="stylesheet" href="/css/style.css">
<?php $this->endSection(); ?>


<?php $this->startSection('scripts'); ?>
<!-- <script src="path/to/script.js"></script> -->

<?php $this->endSection(); ?>