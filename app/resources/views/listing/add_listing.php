<?php $this->startSection('title'); ?>
Dodaj Ogłoszenie
<?php $this->endSection(); ?>

<div class="container my-6 is-flex is-flex-direction-column" style="min-height: 80vh;">
    <div class="box my-6">
        <h1 class="title">Dodaj Ogłoszenie</h1>

        <form action="/add-listing" method="post" enctype="multipart/form-data">
            <div class="columns">
                
                <!-- Lewa kolumna -->
                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Rodzaj ogłoszenia</label>
                        <div class="control">
                            <div class="select is-fullwidth">
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
                            <textarea name="description" class="textarea is-fullwidth" placeholder="Tutaj opisz pracę do wykonania" required></textarea>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Szacowany czas pracy</label>
                        <div class="control">
                            <input class="input is-fullwidth" type="number" id="estimated_time" name="estimated_time" placeholder="Podaj szacowany czas w godzinach" required>
                        </div>
                    </div>
                </div>

                <!-- Środkowa kolumna -->
                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Dodaj zdjęcie</label>
                        <div class="file has-name">
                            <label class="file-label">
                                <input class="file-input" type="file" id="image" name="image[]" accept="image/*" multiple onchange="previewImages(event)" max="10">
                                <span class="file-cta">
                                    <span class="file-label">Wybierz pliki</span>
                                </span>
                            </label>
                        </div>
                        <div id="imagePreviewContainer" style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap; max-height: 150px; overflow-y: auto;">
                            <!-- Kontener na zdjęcia z pionowym przewijaniem -->
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Typ zapłaty</label>
                        <div class="control">
                            <label class="radio">
                                <input type="radio" name="payment_type" value="godzinowa" class="is-radio">
                                Stawka godzinowa
                            </label>
                            <label class="radio">
                                <input type="radio" name="payment_type" value="za_cala_prace" class="is-radio">
                                Kwota za całą pracę
                            </label>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Kwota</label>
                        <div class="control">
                            <input class="input is-fullwidth" type="number" id="payment" name="payment" placeholder="Wpisz kwotę w zł" required>
                        </div>
                    </div>
                </div>

                <!-- Prawa kolumna -->
                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Lokalizacja na mapie</label>
                        <div id="map" style="width: 100%; height: 300px;"></div> <!-- Kontener na mapę -->
                    </div>

                    <!-- Pole tekstowe na wybraną lokalizację (współrzędne) -->
                    <!-- Pole tekstowe na wybraną lokalizację (adres) -->
                    <div class="field">
                        <label class="label">Adres</label>
                        <input class="input is-fullwidth" type="text" id="address" name="address" placeholder="Podaj adres" readonly>
                    </div>
                </div>

            </div>

            <div class="field">
                <div class="control">
                    <button class="button is-primary is-fullwidth" style="max-width: 300px; margin: 0 auto;">Dodaj ogłoszenie</button>
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

        // Przycisk do geokodowania na podstawie adresu
        document.getElementById('geocodeBtn').addEventListener('click', () => {
            const address = document.getElementById('address').value;
            if (address) {
                geocode({ address: address });
            } else {
                alert("Wprowadź adres!");
            }
        });
    }
///////////////////////////////////////////////////////////////
    function geocode(request) {
        geocoder
            .geocode(request)
            .then((result) => {
                const { results } = result;

                // Ustaw marker w nowej lokalizacji
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                marker.setMap(map);

                // Zapisz adres do pola adresu
                document.getElementById('address').value = results[0].formatted_address;
            })
            .catch((e) => {
                alert("Geocode was not successful: " + e);
            });
    }


    window.initMap = initMap;

    // Dodanie "godzin" po wpisaniu wartości w szacowanym czasie pracy
    document.getElementById('estimated_time').addEventListener('input', function() {
        document.getElementById('timeSuffix').style.visibility = this.value ? 'visible' : 'hidden';
    });

    // Dodanie "zł" po wpisaniu wartości w polu kwoty
    document.getElementById('payment').addEventListener('input', function() {
        document.getElementById('currencySuffix').style.visibility = this.value ? 'visible' : 'hidden';
    });
</script>

<!-- Załaduj Google Maps API -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoNfnRG-X71gH-3zJBLX0A3y4irPx64PE&callback=initMap"
    async defer></script>

<script>
function previewImages(event) {
    const files = event.target.files;
    const container = document.getElementById('imagePreviewContainer');

    if (files.length > 10 || (files.length + container.children.length) > 10) {
        alert('Możesz wybrać maksymalnie 10 zdjęć!');
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
            img.style.borderRadius = '8px';
            img.style.marginRight = '10px';
            container.appendChild(img);
        };

        reader.readAsDataURL(file);
    }

    // Dodaj pionowy scrollbar po dodaniu więcej niż 3 zdjęć
    if (container.children.length > 2) {
        container.style.maxHeight = '150px'; // Ogranicz wysokość kontenera
        container.style.overflowY = 'auto'; // Dodaj pionowy scrollbar
    } else {
        container.style.overflowY = 'hidden'; // Ukryj scrollbar, jeśli mniej niż 4 zdjęcia
    }
}
</script>

<?php $this->startSection('scripts'); ?>
<!-- Możesz dodać tu dodatkowe skrypty -->
<?php $this->endSection(); ?>
