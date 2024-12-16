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
        <div class="column is-two-thirds">
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
                            <?php
                            $images = json_decode($job['images']);
                            foreach ($images as $image): ?>
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
        <div class="column is-one-third">
            <div class="box">
                <div class="field">
                    <div class="field">
                        <?php if ($job['listing_status'] === 'closed'): ?>
                            <button class="button is-danger is-fullwidth" disabled>To ogłoszenie zostało zakończone</button>
                        <?php elseif ($contractExists): ?>
                            <button class="button is-primary is-fullwidth" disabled>Kontrakt został już wysłany</button>
                        <?php else: ?>
                            <button class="button is-primary is-fullwidth" onclick="showConfirmationModal()">Przyjmuję zlecenie</button>
                        <?php endif; ?>
                    </div>



                    <div class="content has-text-centered mt-4">
                        <h2 class="subtitle">Dane kontaktowe</h2>
                        <p><strong>Telefon:</strong> <?= htmlspecialchars($job['phone_number']) ?></p>
                        <p><strong>E-mail:</strong> <?= htmlspecialchars($job['e-mail']) ?></p>
                    </div>
                    <div class="field mt-4">
                        <a class="button is-info is-fullwidth" href="/czat/utworz/<?= $job['id'] ?>">Napisz wiadomość</a>
                    </div>
                    <!-- Przycisk Negocjuj stawkę, który zamienia się na zostaw opinię, po zakończeniu ogłoszenia -->
                    <div class="mt-4">
                        <?php if ($job['listing_status'] === 'closed'): ?>
                            <a href="/opinie/dodaj/<?= htmlspecialchars($job['id']); ?>"
                                class="button is-primary is-fullwidth">
                                Zostaw opinię
                            </a>
                        <?php else: ?>
                            <a href="/negocjacje/start/<?= htmlspecialchars($job['id']); ?>"
                                class="button is-primary is-fullwidth">
                                Negocjuj stawkę
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4">
                        <?php if ($job['user_id'] == $_SESSION['user_id'] && $job['listing_status'] === 'active'): ?>
                            <form id="closeListingForm" action="/ogloszenia/<?php echo htmlspecialchars($job['id']); ?>/zakoncz" method="POST" style="display:inline;">
                                <button class="button is-warning is-fullwidth" type="button" onclick="showConfirmationBox()">
                                    Zakończ ogłoszenie
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php if ($isEmployer && !empty($contracts)): ?>
        <div class="box">
            <h2 class="subtitle">Panel kontraktów</h2>
            <table class="table is-fullwidth">
                <thead>
                    <tr>
                        <th>Użytkownik</th>
                        <th>Data wysłania</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contracts as $contract): ?>
                        <tr>
                            <td><?= htmlspecialchars($contract['user_id']) ?></td>
                            <td><?= htmlspecialchars($contract['created_at']) ?></td>
                            <td><?= htmlspecialchars($contract['status']) ?></td>
                            <td>
                                <form action="/contracts/accept/<?= htmlspecialchars($contract['id']) ?>" method="POST" style="display:inline;">
                                    <button class="button is-success is-small" type="submit">Zaakceptuj</button>
                                </form>
                                <form action="/contracts/reject/<?= htmlspecialchars($contract['id']) ?>" method="POST" style="display:inline;">
                                    <button class="button is-danger is-small" type="submit">Odrzuć</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>



    <!-- Modal potwierdzenia -->
    <div class="modal" id="confirmationModal">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title has-text-centered">Potwierdzenie kontraktu</p>
                <button class="delete" aria-label="close" onclick="closeConfirmationModal()"></button>
            </header>
            <section class="modal-card-body">
                <h2 class="subtitle">Szczegóły ogłoszenia</h2>
                <p><strong>Typ pracy:</strong> <?= htmlspecialchars($job['job_type']) ?></p>
                <p><strong>Pracodawca:</strong> <?= htmlspecialchars($job['employer_name']) ?></p>
                <p><strong>Miasto:</strong> <?= htmlspecialchars($job['city']) ?></p>
                <p><strong>Stawka godzinowa:</strong> <?= htmlspecialchars($job['payment']) ?> PLN/h</p>
                <p><strong>Opis:</strong> <?= nl2br(htmlspecialchars($job['description'])) ?></p>
            </section>
            <footer class="modal-card-foot" style="justify-content: center;">
                <button class="button is-primary" onclick="acceptContract()">Tak, przyjmuję</button>
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
                    <li class="is-flex is-align-items-center">
                        <span class="icon has-text-primary">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <span><?= htmlspecialchars($requirement->requirement) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div id="equipment" class="tab-content box" style="display: none;">
            <h2 class="subtitle">Dostępny sprzęt</h2>
            <ul>
                <?php foreach (json_decode($job['equipment']) as $item): ?>
                    <li class="is-flex is-align-items-center">
                        <span class="icon has-text-primary">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <span><?= htmlspecialchars($item->item) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div id="offer" class="tab-content box" style="display: none;">
            <h2 class="subtitle">To oferujemy</h2>
            <ul>
                <?php foreach (json_decode($job['offer']) as $benefit): ?>
                    <li class="is-flex is-align-items-center">
                        <span class="icon has-text-primary">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <span><?= htmlspecialchars($benefit->benefit) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>


    <!-- Kafelek z mapą Google -->
    <div class="container my-6">
        <div class="box">
            <h2 class="subtitle">Lokalizacja</h2>
            <div id="map" class="is-fullwidth" style="height: 300px;"></div>
        </div>
    </div>

    <?php $this->startSection('scripts'); ?>
    <script>
        function startNegotiation() {
            window.location.href = "/negocjacje/start/<?= htmlspecialchars($job['id']) ?>";
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
            geocoder.geocode({
                address: address
            }, (results, status) => {
                if (status === "OK") {
                    map.setCenter(results[0].geometry.location);
                    marker.setPosition(results[0].geometry.location);
                } else {
                    console.error("Geocode was not successful for the following reason: " + status);
                }
            });
        }

        window.initMap = initMap;

        function acceptContract() {
            fetch('/contracts/create', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        job_id: <?= json_encode($job['id']) ?>
                    }),
                })
                .then(response => {
                    if (response.ok) {
                        closeConfirmationModal();
                        window.location.reload();
                    } else {
                        alert('Wystąpił błąd podczas zapisywania kontraktu.');
                    }
                })
                .catch(error => console.error('Błąd:', error));
        }

        function showConfirmationBox() {
            Swal.fire({
                title: 'Czy na pewno chcesz zakończyć to ogłoszenie?',
                text: "Nie będziesz mógł cofnąć tej operacji!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Tak, zakończ',
                cancelButtonText: 'Anuluj'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('closeListingForm').submit();
                }
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoNfnRG-X71gH-3zJBLX0A3y4irPx64PE&callback=initMap" async defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php $this->endSection(); ?>