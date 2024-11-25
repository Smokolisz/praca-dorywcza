<?php $this->startSection('title'); ?>
Strona Główna
<?php $this->endSection(); ?>


<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<div class="notification is-primary is-light">
    <button class="delete"></button>
    Ogłoszenie zostało pomyślnie dodane!
</div>
<?php endif; ?>

<div class="strona-glowna pt-6">

    <div class="grid">
        <div class="cell">
            <h1 class="title is-1">Praca dla ciebie!</h1>
            <p class="subtitle is-5">Szukasz pracy dorywczej lub potrzebujesz wsparcia w codziennych zadaniach? U nas znajdziesz setki ofert dostosowanych do Twoich potrzeb - szybko, wygodnie i bez zbędnych formalności. Dołącz do naszej społeczności, przeglądaj ogłoszenia w swojej okolicy lub wystaw własne i zacznij działać już dziś!</p>
        </div>

        <div class="cell">
            <h1 class="title is-3">Popularne ogłoszenia</h1>
            <div class="scroll-container">

                <div class="box has-background-grey-darker">
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
                        <span class="tag is-dark">Brak doświadczenia zawodowego</span>
                        <span class="tag is-dark">20zł/h</span>
                        <span class="tag is-dark">Dla uczniów/studentów</span>
                        <span class="icon has-text-danger is-pulled-right">
                            <i class="fas fa-heart"></i>
                        </span>
                    </div>
                </div>


                <div class="box has-background-grey-darker">
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
                        <span class="tag is-dark">Doświadczenie z zwierzętami mile widziane</span>
                        <span class="tag is-dark">25zł/h</span>
                        <span class="tag is-dark">Elastyczne godziny</span>
                        <span class="icon has-text-danger is-pulled-right">
                            <i class="fas fa-heart"></i>
                        </span>
                    </div>
                </div>

                <div class="box has-background-grey-darker">
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
                        <span class="tag is-dark">Wymagane certyfikaty TESOL/CELTA</span>
                        <span class="tag is-dark">50zł/h</span>
                        <span class="tag is-dark">Dla osób z doświadczeniem</span>
                        <span class="icon has-text-danger is-pulled-right">
                            <i class="fas fa-heart"></i>
                        </span>
                    </div>
                </div>

                <div class="box has-background-grey-darker">
                    <p class="title is-4 no-gap">Dostawca jedzenia na rowerze w Gdańsku</p>
                    <p class="subtitle is-6 no-gap">
                        <span class="icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        Gdańsk, Wrzeszcz |
                        <span class="icon">
                            <i class="fas fa-file-contract"></i>
                        </span>
                        Umowa-zlecenie |
                        <span class="icon">
                            <i class="fas fa-bicycle"></i>
                        </span>
                        Dostawa rowerowa
                    </p>
                    <div class="tags">
                        <span class="tag is-dark">Brak doświadczenia wymagane</span>
                        <span class="tag is-dark">18zł/h</span>
                        <span class="tag is-dark">Dla aktywnych fizycznie</span>
                        <span class="icon has-text-danger is-pulled-right">
                            <i class="fas fa-heart"></i>
                        </span>
                    </div>
                </div>

                <div class="box has-background-grey-darker">
                    <p class="title is-4 no-gap">Programista PHP we Wrocławiu</p>
                    <p class="subtitle is-6 no-gap">
                        <span class="icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        Wrocław, Fabryczna |
                        <span class="icon">
                            <i class="fas fa-file-contract"></i>
                        </span>
                        Umowa o pracę |
                        <span class="icon">
                            <i class="fas fa-laptop-code"></i>
                        </span>
                        Programowanie w PHP
                    </p>
                    <div class="tags">
                        <span class="tag is-dark">Wymagane doświadczenie min. 2 lata</span>
                        <span class="tag is-dark">90zł/h</span>
                        <span class="tag is-dark">Dla doświadczonych programistów</span>
                        <span class="icon has-text-danger is-pulled-right">
                            <i class="fas fa-heart"></i>
                        </span>
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
<link rel="stylesheet" href="/css/style.css">
<?php $this->endSection(); ?>


<?php $this->startSection('scripts'); ?>
<!-- <script src="path/to/script.js"></script> -->

<?php $this->endSection(); ?>