<?php $this->startSection('title'); ?>
FAQ
<?php $this->endSection(); ?>

<div class="container my-6">
    <h1 class="title">Najczęściej zadawane pytania</h1>
    <div class="box">
        <article class="message is-info">
            <div class="message-header">
                <p>Jak mogę zarejestrować się w serwisie?</p>
                <button class="button is-small is-info is-light is-rounded toggle-button" data-target="answer1">+</button>
            </div>
            <div id="answer1" class="message-body is-hidden">
                Aby zarejestrować się w serwisie, kliknij przycisk „Zarejestruj się” w górnym menu i wypełnij formularz rejestracyjny.
            </div>
        </article>

        <article class="message is-info">
            <div class="message-header">
                <p>Czy korzystanie z serwisu jest płatne?</p>
                <button class="button is-small is-info is-light is-rounded toggle-button" data-target="answer2">+</button>
            </div>
            <div id="answer2" class="message-body is-hidden">
                Rejestracja oraz korzystanie z podstawowych funkcji serwisu są bezpłatne. Nie pobieramy żadnych opłat za przeglądanie ogłoszeń.
            </div>
        </article>

        <article class="message is-info">
            <div class="message-header">
                <p>Jak mogę skontaktować się z osobą ogłaszającą?</p>
                <button class="button is-small is-info is-light is-rounded toggle-button" data-target="answer3">+</button>
            </div>
            <div id="answer3" class="message-body is-hidden">
                Aby skontaktować się z ogłaszającym, otwórz wybrane ogłoszenie i użyj dostępnych danych kontaktowych lub funkcji wiadomości.
            </div>
        </article>

        <article class="message is-info">
            <div class="message-header">
                <p>Jak mogę dodać ogłoszenie?</p>
                <button class="button is-small is-info is-light is-rounded toggle-button" data-target="answer4">+</button>
            </div>
            <div id="answer4" class="message-body is-hidden">
                Wybierz opcję „Dodaj ogłoszenie” w menu i wypełnij formularz. Po zapisaniu ogłoszenie pojawi się na stronie.
            </div>
        </article>

        <article class="message is-info">
            <div class="message-header">
                <p>Jak mogę edytować swoje ogłoszenie?</p>
                <button class="button is-small is-info is-light is-rounded toggle-button" data-target="answer5">+</button>
            </div>
            <div id="answer5" class="message-body is-hidden">
                Zaloguj się na swoje konto, przejdź do sekcji „Moje ogłoszenia” i wybierz opcję edycji przy odpowiednim ogłoszeniu.
            </div>
        </article>
    </div>
</div>

<style>
.is-hidden {
    display: none;
}
</style>

<?php $this->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.toggle-button');
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.getAttribute('data-target');
                const target = document.getElementById(targetId);
                if (target) {
                    target.classList.toggle('is-hidden');
                }
            });
        });
    });
</script>
<?php $this->endSection(); ?>
