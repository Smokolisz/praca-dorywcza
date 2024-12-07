<?php $this->startSection('title'); ?>
Strona Główna
<?php $this->endSection(); ?>
<div class="container is-fluid py-6">

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
                                    <!-- Tytuł jako link do szczegółów -->
                                    <p class="title is-4 no-gap">
                                        <a href="/job/<?= htmlspecialchars($listing['id']) ?>" class="has-text-light">
                                            <?= htmlspecialchars($listing['job_type']) ?>
                                        </a>
                                    </p>


                                    <div class="box">
                                        <p class="title is-4 no-gap">Koszenie trawnika na terenie Opola</p>
                                        <p class="subtitle is-6 no-gap">
                                            <span class="icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            Opole, Zaodrze |
                                            <span class="icon">
                                                <i class="fas fa-file-contract"></i>
                                            </span>
                                            Umowa zlecenie |
                                            <span class="icon">
                                                <i class="fas fa-truck"></i>
                                            </span>
                                            We własnym zakresie
                                        </p>
                                        <div class="tags">
                                            <span class="tag">Brak doświadczenia zawodowego</span>
                                            <span class="tag">20zł/h</span>
                                            <span class="tag">Dla uczniów/studentów</span>
                                            <span class="icon has-text-danger is-pulled-right">
                                                <i class="fas fa-heart"></i>
                                            </span>
                                        </div>
                                    </div>


                                    <div class="box">
                                        <p class="title is-4 no-gap">Opieka nad zwierzętami w Krakowie</p>
                                        <p class="subtitle is-6 no-gap">
                                            <span class="icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            Kraków, Podgórze |
                                            <span class="icon">
                                                <i class="fas fa-file-contract"></i>
                                            </span>
                                            Umowa o dzieło |
                                            <span class="icon">
                                                <i class="fas fa-paw"></i>
                                            </span>
                                            Praca z zwierzętami
                                        </p>
                                        <div class="tags">
                                            <span class="tag">Doświadczenie z zwierzętami mile widziane</span>
                                            <span class="tag">25zł/h</span>
                                            <span class="tag">Elastyczne godziny</span>
                                            <span class="icon has-text-danger is-pulled-right">
                                                <i class="fas fa-heart"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="box">
                                        <p class="title is-4 no-gap">Nauczyciel języka angielskiego w Warszawie</p>
                                        <p class="subtitle is-6 no-gap">
                                            <span class="icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            Warszawa, Śródmieście |
                                            <span class="icon">
                                                <i class="fas fa-file-contract"></i>
                                            </span>
                                            Umowa o pracę |
                                            <span class="icon">
                                                <i class="fas fa-book-open"></i>
                                            </span>
                                            Nauczanie angielskiego
                                        </p>
                                        <div class="tags">
                                            <span class="tag">Wymagane certyfikaty TESOL/CELTA</span>
                                            <span class="tag">50zł/h</span>
                                            <span class="tag">Dla osób z doświadczeniem</span>
                                            <span class="icon has-text-danger is-pulled-right">
                                                <i class="fas fa-heart"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="box">
                                        <p class="title is-4 no-gap">Dostawca jedzenia na rowerze w Gdańsku</p>

                                        <p class="subtitle is-6 no-gap">
                                            <span class="icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <?= htmlspecialchars($listing['city']) ?>, <?= htmlspecialchars($listing['address']) ?>
                                        </p>
                                        <div class="tags">

                                            |

                                            <span class="tag">Brak doświadczenia wymagane</span>
                                            <span class="tag">18zł/h</span>
                                            <span class="tag">Dla aktywnych fizycznie</span>
                                            <span class="icon has-text-danger is-pulled-right">
                                                <i class="fas fa-heart"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="box">
                                        <p class="title is-4 no-gap">Programista PHP we Wrocławiu</p>
                                        <p class="subtitle is-6 no-gap">
                                            <span class="icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            Wrocław, Fabryczna |

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

                                            Programowanie w PHP
                                        </p>
                                        <div class="tags">
                                            <span class="tag">Wymagane doświadczenie min. 2 lata</span>
                                            <span class="tag">90zł/h</span>
                                            <span class="tag">Dla doświadczonych programistów</span>

                                            <span class="icon has-text-danger is-pulled-right">
                                                <i class="fas fa-heart"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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


<?php $this->startSection('head'); ?>
<?php $this->endSection(); ?>


<?php $this->startSection('scripts'); ?>
<?php $this->endSection(); ?>
