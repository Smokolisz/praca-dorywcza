<?php $this->startSection('title'); ?>
Dodaj Ogłoszenie
<?php $this->endSection(); ?>

<div class="container my-6 is-max-desktop" style="padding-bottom:20px">
    <div class="box my-6">
        <h1 class="title">Dodaj Ogłoszenie</h1>

        <form action="add_listing.php" method="post" enctype="multipart/form-data">
            <div class="columns">
                
                <!-- Lewa kolumna -->
                <div class="column">
                    <div class="field">
                        <label class="label">Rodzaj ogłoszenia</label>
                        <div class="control">
                            <div class="select">
                                <select name="job_type" required>
                                    <option value="koszenie_trawnika">Koszenie trawnika</option>
                                    <option value="rabanie_drewna">Rąbanie drewna</option>
                                    <option value="mycie_okien">Mycie okien</option>
                                    <option value="sprzatanie">Sprzątanie</option>
                                    <option value="naprawa_sprzetu">Naprawa sprzętu</option>
                                    <option value="skladanie_mebli">Składanie mebli</option>
                                    <option value="prace_ogrodowe">Prace ogrodowe</option>
                                    <option value="przeprowadzki">Pomoc przy przeprowadzkach</option>
                                    <option value="opieka_nad_zwierzetami">Opieka nad zwierzętami</option>
                                    <option value="prace_budowlane">Drobne prace budowlane</option>
                                    <option value="naprawa_rowerow">Naprawa rowerów</option>
                                    <option value="czyszczenie_podjazdow">Czyszczenie podjazdów</option>
                                    <option value="prace_malarskie">Prace malarskie</option>
                                    <option value="inne">Inne</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Opis pracy</label>
                        <div class="control">
                            <textarea name="description" class="textarea" placeholder="Tutaj opisz pracę do wykonania" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Środkowa kolumna -->
                <div class="column">
                    <div class="field">
                        <label class="label">Dodaj zdjęcie</label>
                        <div class="file has-name">
                            <label class="file-label">
                                <input class="file-input" type="file" id="image" name="image[]" accept="image/*" multiple onchange="previewImages(event)" max="3">
                                <span class="file-cta">
                                    <span class="file-label">Wybierz pliki</span>
                                </span>
                            </label>
                        </div>
                        <div id="imagePreviewContainer" style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; max-width: 100%;">
                            <!-- Kontener na zdjęcia -->
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Typ zapłaty</label>
                        <div class="control">
                            <div class="select">
                                <select name="payment_type" required>
                                    <option value="godzinowa">Stawka godzinowa</option>
                                    <option value="za_cala_prace">Kwota za całą pracę</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Kwota</label>
                        <div class="control">
                            <input class="input" type="number" name="payment" placeholder="Wpisz kwotę" required>
                        </div>
                    </div>
                </div>

                <!-- Prawa kolumna -->
                <div class="column">
                    <div class="field">
                        <label class="label">Lokalizacja na mapie</label>
                        <div id="map" style="width: 100%; height: 300px;"></div> <!-- Kontener na mapę -->
                    </div>

                    <!-- Pole tekstowe na wybraną lokalizację (adres) -->
                    <div class="field">
                        <label class="label">Adres</label>
                        <input class="input" type="text" id="address" name="address" placeholder="Podaj adres">
                    </div>
                </div>

            </div>

            <div class="field">
                <div class="control">
                    <button class="button is-primary is-fullwidth">Dodaj ogłoszenie</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let map;
    let marker;
    let geocoder;

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 8,
            center: { lat: 52.2297, lng: 21.0122 }, // Domyślnie Warszawa
        });

        geocoder = new google.maps.Geocoder();

        // Marker, który pojawi się na mapie
        marker = new google.maps.Marker({
            map,
        });

        // Kliknięcie na mapie
        map.addListener("click", (e) => {
            geocode({ location: e.latLng });
        });
    }

    function geocode(request) {
        geocoder
            .geocode(request)
            .then((result) => {
                const { results } = result;

                // Ustaw marker w nowej lokalizacji
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                marker.setMap(map);

                // Zapisz adres do pola tekstowego
                document.getElementById('address').value = results[0].formatted_address;
            })
            .catch((e) => {
                alert("Geocode was not successful: " + e);
            });
    }

    window.initMap = initMap;
</script>

<!-- Załaduj Google Maps API -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoNfnRG-X71gH-3zJBLX0A3y4irPx64PE&callback=initMap"
    async defer></script>

<script>
function previewImages(event) {
    const files = event.target.files;
    const container = document.getElementById('imagePreviewContainer');

    if (files.length > 4 || (files.length + container.children.length) > 4) {
        alert('Możesz wybrać maksymalnie 4 zdjęcia!');
        event.target.value = ''; // Zresetuj pole pliku
        return;
    }

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const reader = new FileReader();

        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '8px'; // Lekko zaokrąglone rogi
            img.style.marginRight = '10px'; // Odstęp między zdjęciami
            container.appendChild(img); // Dodaj zdjęcie do kontenera
        };

        reader.readAsDataURL(file);
    }
}
</script>

<?php $this->startSection('scripts'); ?>
<!-- Możesz dodać tu dodatkowe skrypty -->
<?php $this->endSection(); ?>
