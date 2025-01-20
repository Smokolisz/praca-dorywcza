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
                            <input 
                                class="input is-fullwidth" 
                                type="text" 
                                name="job_type" 
                                required 
                                placeholder="Wprowadź rodzaj pracy"
                                value="<?= htmlspecialchars($_SESSION['job_data']['job_type'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Wybór kategorii -->
                        <div class="field">
                        <label class="label">Kategoria</label>
                        <div class="control">
                            <div class="select is-fullwidth">
                                <select name="category_id" required>
                                    <option value="">Wybierz kategorię</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['id']); ?>"
                                            <?= (isset($_SESSION['job_data']['category_id']) && $_SESSION['job_data']['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        </div>

                    <div class="field">
                        <label class="label">Opis pracy</label>
                        <div class="control">
                            <textarea name="description" class="textarea is-fullwidth" placeholder="Tutaj opisz pracę do wykonania" required><?= htmlspecialchars($_SESSION['job_data']['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Szacowany czas pracy (godziny)</label>
                        <div class="control">
                            <input class="input is-fullwidth" type="number" id="estimated_time" name="estimated_time" placeholder="Podaj szacowany czas w godzinach" required value="<?= htmlspecialchars($_SESSION['job_data']['estimated_time'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <!-- Środkowa kolumna -->
                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Dodaj zdjęcia</label>
                        <div class="file has-name">
                            <label class="file-label">
                                <input class="file-input" type="file" id="image" name="images[]" accept="image/*" multiple onchange="previewImages(event)">

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
                                <input type="radio" name="payment_type" value="godzinowa" required <?= (isset($_SESSION['job_data']['payment_type']) && $_SESSION['job_data']['payment_type'] == 'godzinowa') ? 'checked' : '' ?>> Stawka godzinowa
                            </label>
                            <label class="radio">
                                <input type="radio" name="payment_type" value="za_cala_prace" required <?= (isset($_SESSION['job_data']['payment_type']) && $_SESSION['job_data']['payment_type'] == 'za_cala_prace') ? 'checked' : '' ?>> Kwota za całą pracę
                            </label>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label">Kwota (PLN)</label>
                        <div class="control">
                            <input class="input is-fullwidth" type="number" id="payment" name="payment" placeholder="Wpisz kwotę w zł" required value="<?= htmlspecialchars($_SESSION['job_data']['payment'] ?? '') ?>" min="1">
                        </div>
                    </div>
                </div>

                <!-- Prawa kolumna -->
                <div class="column is-one-third">
                    <div class="field">
                        <label class="label">Lokalizacja na mapie</label>
                        <div id="map" style="width: 100%; height: 300px;"></div>
                    </div>

                    <div class="field">
                        <label class="label">Adres</label>
                        <input class="input is-fullwidth" type="text" id="address" name="address" placeholder="Podaj adres" value="<?= htmlspecialchars($_SESSION['job_data']['address'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Zakładki -->
            <div class="tabs is-centered is-boxed my-6">
                <ul>
                    <li class="is-active" onclick="showTab('requirements')"><a>Wymagania</a></li>
                    <li onclick="showTab('equipment')"><a>Dostępny sprzęt</a></li>
                    <li onclick="showTab('offer')"><a>To oferujemy</a></li>
                </ul>
            </div>

            <div id="requirements" class="tab-content box">
                <h2 class="subtitle">Wymagania</h2>
                <textarea name="requirements" class="textarea is-fullwidth" placeholder="Wymień wymagania, oddzielając je przecinkami"><?= htmlspecialchars($_SESSION['job_data']['requirements'] ?? '') ?></textarea>
            </div>

            <div id="equipment" class="tab-content box" style="display: none;">
                <h2 class="subtitle">Dostępny sprzęt</h2>
                <textarea name="equipment" class="textarea is-fullwidth" placeholder="Wymień dostępny sprzęt, oddzielając go przecinkami"><?= htmlspecialchars($_SESSION['job_data']['equipment'] ?? '') ?></textarea>
            </div>

            <div id="offer" class="tab-content box" style="display: none;">
                <h2 class="subtitle">To oferujemy</h2>
                <textarea name="offer" class="textarea is-fullwidth" placeholder="Wymień korzyści, oddzielając je przecinkami"><?= htmlspecialchars($_SESSION['job_data']['offer'] ?? '') ?></textarea>
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
    // Obsługa zakładek
    function showTab(tabId) {
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => content.style.display = 'none');

        const tabs = document.querySelectorAll('.tabs ul li');
        tabs.forEach(tab => tab.classList.remove('is-active'));

        document.getElementById(tabId).style.display = 'block';
        document.querySelector(`.tabs ul li[onclick="showTab('${tabId}')"]`).classList.add('is-active');
    }

    // Podgląd zdjęć
    function previewImages(event) {
        const files = event.target.files;
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.marginRight = '10px';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }

    // Inicjalizacja mapy
    let map;
    let marker;

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), { zoom: 8, center: { lat: 52.2297, lng: 21.0122 } });
        marker = new google.maps.Marker({ map: map });
        map.addListener("click", (e) => { geocode({ location: e.latLng }); });
    }

    function geocode(request) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode(request, (results, status) => {
            if (status === "OK") {
                map.setCenter(results[0].geometry.location);
                marker.setPosition(results[0].geometry.location);
                if (request.location) document.getElementById('address').value = results[0].formatted_address;
            }
        });
    }


    
    
    window.initMap = initMap;
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDoNfnRG-X71gH-3zJBLX0A3y4irPx64PE&callback=initMap" async defer></script>

<?php $this->startSection('scripts'); ?>
<!-- Dodatkowe skrypty -->
<?php $this->endSection(); ?>
