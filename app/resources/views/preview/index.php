<?php $this->startSection('title'); ?>
Podgląd ogłoszenia pracy dorywczej
<?php $this->endSection(); ?>

<div class="container my-6" style="display: flex; justify-content: space-between; align-items: stretch; gap: 20px;">
    <div class="box" style="padding: 20px; width: 100%; text-align: center;">
        <h1 class="title"><?= htmlspecialchars($job['job_type']) ?></h1>
    </div>
</div>
<div class="container my-6" style="display: flex; gap: 20px; align-items: stretch;">
    <div class="box" style="padding: 20px; width: 100%;">
        <div class="columns">
            <div class="column is-half">
                <div class="content">
                    <h2 class="subtitle">Informacje</h2>
                    <p><strong>Pracodawca:</strong> <?= htmlspecialchars($job['employer_name']) ?></p>
                    <p><strong>Miasto:</strong> <?= htmlspecialchars($job['city']) ?></p>
                    <p><strong>Stawka godzinowa:</strong> <?= htmlspecialchars($job['payment']) ?> PLN/h</p>
                    <p><strong>Data publikacji:</strong> <?= htmlspecialchars($job['created_at']) ?></p>
                </div>
            </div>
            <div class="column is-half">
                <div class="content">
                    <h2 class="subtitle">Zdjęcia</h2>
                    <div class="gallery" style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php foreach (json_decode($job['images']) as $image): ?>
                            <img src="/pictures/<?= htmlspecialchars($image) ?>" alt="Zdjęcie związane z ofertą" style="width: 100px; height: auto;">
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="content">
                    <h2 class="subtitle">Opis stanowiska</h2>
                    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="box" style="padding: 20px; height: 100%; width: 25%; display: flex; flex-direction: column; justify-content: space-between;">
        <div class="field" style="width: 100%; margin-bottom: 15px;">
            <button class="button is-primary is-fullwidth" onclick="showConfirmationModal()">Przyjmuje zlecenie</button>
        </div>

        <div class="content" style="text-align: center; padding-top: 10px; width: 100%;">
            <h2 class="subtitle">Dane kontaktowe</h2>
            <p><strong>Telefon:</strong> <?= htmlspecialchars($job['phone_number']) ?></p>
            <p><strong>E-mail:</strong> <?= htmlspecialchars($job['e-mail']) ?></p>
        </div>

        <div class="field" style="width: 100%; margin-top: 15px;">
            <button class="button is-info is-fullwidth" onclick="showChatWindow()">Napisz wiadomość</button>
        </div>
    </div>
</div>

<div class="modal" id="confirmationModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head" style="justify-content: center;">
            <p class="modal-card-title" style="text-align: center;">Potwierdzenie</p>
            <button class="delete" aria-label="close" onclick="closeConfirmationModal()"></button>
        </header>
        <section class="modal-card-body" style="text-align: center;">
            <p>Czy na pewno chcesz przyjąć to zlecenie?</p>
        </section>
        <footer class="modal-card-foot" style="justify-content: center;">
            <button class="button is-primary" style="margin-right: 10px;" onclick="closeConfirmationModal()">Tak, przyjmuję</button>
            <button class="button" onclick="closeConfirmationModal()">Anuluj</button>
        </footer>
    </div>
</div>

<?php $this->startSection('scripts'); ?>
<script>
    function showChatWindow() {
        alert("Tu będzie okno Chatu");
    }

    function showConfirmationModal() {
        document.getElementById("confirmationModal").classList.add("is-active");
    }

    function closeConfirmationModal() {
        document.getElementById("confirmationModal").classList.remove("is-active");
    }
</script>
<?php $this->endSection(); ?>