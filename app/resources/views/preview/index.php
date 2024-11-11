<?php $this->startSection('title'); ?>
Podgląd ogłoszenia pracy dorywczej
<?php $this->endSection(); ?>

<div class="container my-6">
    <div class="box has-text-centered">
        <h1 class="title"><?= htmlspecialchars($job['job_type']) ?></h1>
    </div>
</div>

<div class="container my-6">
    <div class="columns is-variable is-8">
        <!-- Informacje o pracy i zdjęcia -->
        <div class="column is-three-quarters">
            <div class="box">
                <div class="columns">
                    <!-- Informacje o pracy -->
                    <div class="column">
                        <h2 class="subtitle">Informacje</h2>
                        <p><strong>Pracodawca:</strong> <?= htmlspecialchars($job['employer_name']) ?></p>
                        <p><strong>Miasto:</strong> <?= htmlspecialchars($job['city']) ?></p>
                        <p><strong>Stawka godzinowa:</strong> <?= htmlspecialchars($job['payment']) ?> PLN/h</p>
                        <p><strong>Data publikacji:</strong> <?= htmlspecialchars($job['created_at']) ?></p>
                    </div>

                    <!-- Zdjęcia i opis stanowiska -->
                    <div class="column">
                        <h2 class="subtitle">Zdjęcia</h2>
                        <div class="gallery is-flex is-flex-wrap-wrap is-justify-content-space-between">
                            <?php foreach (json_decode($job['images']) as $image): ?>
                                <figure class="image is-128x128">
                                    <img src="/pictures/<?= htmlspecialchars($image) ?>" alt="Zdjęcie związane z ofertą">
                                </figure>
                            <?php endforeach; ?>
                        </div>

                        <h2 class="subtitle mt-4">Opis stanowiska</h2>
                        <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dane kontaktowe i akcje -->
        <div class="column">
            <div class="box">
                <div class="field">
                    <button class="button is-primary is-fullwidth" onclick="showConfirmationModal()">Przyjmuje zlecenie</button>
                </div>

                <div class="content has-text-centered mt-4">
                    <h2 class="subtitle">Dane kontaktowe</h2>
                    <p><strong>Telefon:</strong> <?= htmlspecialchars($job['phone_number']) ?></p>
                    <p><strong>E-mail:</strong> <?= htmlspecialchars($job['e-mail']) ?></p>
                </div>

                <div class="field mt-4">
                    <button class="button is-info is-fullwidth" onclick="showChatWindow()">Napisz wiadomość</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal potwierdzenia -->
<div class="modal" id="confirmationModal">
    <div class="modal-background"></div>
    <div class="modal-card">
        <header class="modal-card-head">
            <p class="modal-card-title has-text-centered">Potwierdzenie</p>
            <button class="delete" aria-label="close" onclick="closeConfirmationModal()"></button>
        </header>
        <section class="modal-card-body has-text-centered">
            <p>Czy na pewno chcesz przyjąć to zlecenie?</p>
        </section>
        <footer class="modal-card-foot is-justify-content-center">
            <button class="button is-primary" onclick="closeConfirmationModal()">Tak, przyjmuję</button>
            <button class="button" onclick="closeConfirmationModal()">Anuluj</button>
        </footer>
    </div>
</div>

<!-- Panele informacyjne z zakładkami -->
<div class="tabs is-centered is-boxed">
    <ul>
        <li class="is-active" onclick="showTab('requirements')"><a>Wymagania</a></li>
        <li onclick="showTab('equipment')"><a>Dostępny sprzęt</a></li>
        <li onclick="showTab('offer')"><a>To oferujemy</a></li>
    </ul>
</div>

<div class="container my-6">
    <div id="requirements" class="tab-content box">
        <h2 class="subtitle">Wymagania</h2>
        <ul>
            <?php foreach (json_decode($job['requirements']) as $requirement): ?>
                <li><?= htmlspecialchars($requirement->requirement) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="equipment" class="tab-content box" style="display: none;">
        <h2 class="subtitle">Dostępny sprzęt</h2>
        <ul>
            <?php foreach (json_decode($job['equipment']) as $item): ?>
                <li><?= htmlspecialchars($item->item) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="offer" class="tab-content box" style="display: none;">
        <h2 class="subtitle">To oferujemy</h2>
        <ul>
            <?php foreach (json_decode($job['offer']) as $benefit): ?>
                <li><?= htmlspecialchars($benefit->benefit) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Kafelek z mapą Google -->
<div class="container my-6">
    <div class="box">
        <h2 class="subtitle">Lokalizacja</h2>
        <div id="map" style="width: 100%; height: 300px;"></div>
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

    function showTab(tabId) {
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => content.style.display = 'none');

        const tabs = document.querySelectorAll('.tabs ul li');
        tabs.forEach(tab => tab.classList.remove('is-active'));

        document.getElementById(tabId).style.display = 'block';
        document.querySelector(`.tabs ul li[onclick="showTab('${tabId}')"]`).classList.add('is-active');
    }

    let map;
    let marker;

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 14
        });

        marker = new google.maps.Marker({
            map: map
        });

        geocodeAddress("<?= addslashes($job['address']) ?>");
    }

    function geocodeAddress(address) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address: address }, (results, status) => {
            if (status === "OK") {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
            } else {
                console.error("Geocode was not successful for the following reason: " + status);
            }
        });
    }

    window.initMap = initMap;
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoNfnRG-X71gH-3zJBLX0A3y4irPx64PE&callback=initMap" async defer></script>
<?php $this->endSection(); ?>
