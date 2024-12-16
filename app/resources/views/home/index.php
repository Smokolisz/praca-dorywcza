<?php $this->startSection('title'); ?>
Strona Główna
<?php $this->endSection(); ?>
<div class="container is-fluid py-6">

    <!--powiadomienia o wystawionej opinii -->
    <div class="container mb-4">
        <div class="columns is-centered">
            <div class="column is-half">

                <?php if (!empty($_SESSION['review_success'])): ?>
                    <div class="notification is-success mb-4">
                        <button class="delete" aria-label="close"></button>
                        <?php
                        echo htmlspecialchars($_SESSION['review_success']);
                        unset($_SESSION['review_success']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_SESSION['review_error'])): ?>
                    <div class="notification is-danger mb-4">
                        <button class="delete" aria-label="close"></button>
                        <?php
                        echo htmlspecialchars($_SESSION['review_error']);
                        unset($_SESSION['review_error']);
                        ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="grid">
        <div class="cell">

            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="notification is-primary is-light">
                    <button class="delete"></button>
                    Ogłoszenie zostało pomyślnie dodane!
                </div>
            <?php endif; ?>

            <div class="strona-glowna pt-6">

                <div class="grid">

                    <div class="cell pr-5">

                        <h1 class="title is-1">Praca dla ciebie!</h1>
                        <p class="subtitle is-5 pt-5">Szukasz pracy dorywczej lub potrzebujesz wsparcia w codziennych zadaniach? U nas znajdziesz setki ofert dostosowanych do Twoich potrzeb - szybko, wygodnie i bez zbędnych formalności. Dołącz do naszej społeczności, przeglądaj ogłoszenia w swojej okolicy lub wystaw własne i zacznij działać już dziś!</p>

                        <h2 class="title is-3 pt-6"><strong>Kategorie</strong></h2>

                        <div class="field is-grouped is-grouped-multiline">
                            <p class="control">
                                <button class="button">
                                    IT i Technologia
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Zdrowie i Opieka Medyczna
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Budownictwo i Architektura
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Edukacja i Nauka
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Finanse i Księgowość
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Handel i Obsługa Klienta
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Marketing i Reklama
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Produkcja i Inżynieria
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Transport i Logistyka
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Turystyka i Gastronomia
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Sztuka i Rozrywka
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Prawo i Administracja
                                </button>
                            </p>
                            <p class="control">
                                <button class="button">
                                    Praca Dorywcza i Sezonowa
                                </button>
                            </p>
                        </div>

                    </div>

                    <div class="cell pl-5">
                        <h1 class="title is-3">Popularne ogłoszenia</h1>
                        <div class="scroll-container py-3">


                            <?php foreach ($listings as $listing): ?>
                                    <div class="box has-background-grey-darker">
                                        <p class="title is-4 no-gap">
                                        <a href="/job/<?= htmlspecialchars($listing['id']) ?>" class="has-text-light">
                                            <?= htmlspecialchars($listing['job_type']) ?>
                                        </a>
                                        <p class="subtitle is-6 no-gap">
                                            <span class="icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <?= htmlspecialchars($listing['address']); ?> |
                                            <span class="icon">
                                                <i class="fas fa-file-contract"></i>
                                            </span>
                                            <?= htmlspecialchars($listing['payment_type']); ?> |
                                        </p>
                                        <div class="tags">
                                            <span class="tag">Brak doświadczenia zawodowego</span>
                                            <span class="tag"><?= htmlspecialchars($listing['payment']); ?>zł</span>
                                            <span class="tag">Dla uczniów/studentów</span>
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
    </div>
</div>

<?php $this->startSection('head'); ?>
<?php $this->endSection(); ?>

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

    document.addEventListener('DOMContentLoaded', () => {
        (document.querySelectorAll('.notification .delete') || []).forEach(($delete) => {
            const $notification = $delete.parentNode;

            $delete.addEventListener('click', () => {
                $notification.parentNode.removeChild($notification);
            });
        });
    });
</script>

<?php $this->endSection(); ?>