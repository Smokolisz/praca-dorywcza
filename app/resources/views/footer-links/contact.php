<?php $this->startSection('title'); ?>
Kontakt
<?php $this->endSection(); ?>

<div class="container my-6">
    <h1 class="title">Kontakt</h1>
    <div class="content">
        <p>Masz pytania? Skontaktuj się z nami, a odpowiemy tak szybko, jak to możliwe.</p>

        <form id="contact-form" method="POST" action="/contact/send">
            <div class="field">
                <label class="label">Imię i nazwisko</label>
                <div class="control">
                    <input class="input" type="text" name="name" placeholder="Twoje imię i nazwisko" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Adres e-mail</label>
                <div class="control">
                    <input class="input" type="email" name="email" placeholder="Twój e-mail" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Typ zapytania</label>
                <div class="control">
                    <div class="select">
                        <select name="query_type" required>
                            <option value="" disabled selected>Wybierz typ zapytania</option>
                            <option value="technical">Problem techniczny</option>
                            <option value="billing">Pytanie o rozliczenia</option>
                            <option value="general">Zapytanie ogólne</option>
                            <option value="feedback">Opinie i sugestie</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field">
                <label class="label">Wiadomość</label>
                <div class="control">
                    <textarea class="textarea" name="message" placeholder="Twoja wiadomość" required></textarea>
                </div>
            </div>

            <div class="control">
                <button class="button is-primary" type="submit">Wyślij</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="success-modal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title">Wiadomość wysłana</p>
            <button class="delete" aria-label="close"></button>
        </header>
        <section class="modal-card-body">
            <p>Dziękujemy za kontakt! Odpowiemy na Twoje zapytanie tak szybko, jak to możliwe.</p>
        </section>
        <footer class="modal-card-foot">
            <button class="button is-primary close-modal">Zamknij</button>
        </footer>
    </div>
</div>

<?php $this->startSection('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('contact-form');
        const modal = document.getElementById('success-modal');
        const modalCloseButtons = modal.querySelectorAll('.close-modal, .delete');

        // Zamknięcie modalu
        modalCloseButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.classList.remove('is-active');
            });
        });

        // Obsługa wysłania formularza
        form.addEventListener('submit', async (event) => {
            event.preventDefault(); // Zapobiega przeładowaniu strony
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                });

                if (response.ok) {
                    modal.classList.add('is-active'); // Pokazanie modalu
                    form.reset(); // Czyszczenie formularza
                } else {
                    alert('Wystąpił błąd podczas wysyłania wiadomości. Spróbuj ponownie.');
                }
            } catch (error) {
                console.error('Błąd:', error);
                alert('Wystąpił błąd sieci. Spróbuj ponownie.');
            }
        });
    });
</script>
<?php $this->endSection(); ?>
