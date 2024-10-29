<div class="container mt-6">
    <div class="box">
        <h2 class="title is-4 has-text-centered">Profil użytkownika</h2>

        <div class="columns is-multiline mt-6">
            <!-- Zdjęcie profilowe i informacje o użytkowniku -->
            <div class="column is-one-third has-text-centered">
                <?php if (!empty($user['profile_picture'])): ?>
                    <figure class="image is-128x128 is-inline-block">
                        <img class="is-rounded" src="/profile_pictures/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Zdjęcie profilowe">
                    </figure>
                <?php endif; ?>
                <div class="box p-5">
                    <h3 class="title is-5 has-text-white">Informacje o użytkowniku</h3>
                    <p><strong>Imię:</strong> <?= htmlspecialchars($user['first_name'] ?? 'Brak imienia') ?></p>
                    <p><strong>Nazwisko:</strong> <?= htmlspecialchars($user['last_name'] ?? 'Brak nazwiska') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? 'Brak adresu email') ?></p>
                </div>
            </div>

            <!-- Przesyłanie zdjęcia profilowego -->
            <div class="column is-one-third">
                <div class="box p-5">
                    <h3 class="title is-5 has-text-white">Prześlij zdjęcie profilowe</h3>
                    <form action="/profil/upload-profile-picture" method="POST" enctype="multipart/form-data">
                        <div class="file has-name is-fullwidth mb-3">
                            <label class="file-label is-fullwidth">
                                <input class="file-input" type="file" name="profile_picture" required onchange="updateFileName(this)">
                                <span class="file-cta is-fullwidth">
                                    <span class="icon"><i class="fas fa-upload"></i></span>
                                    <span>Wybierz plik</span>
                                </span>
                                <span class="file-name">Nie wybrano pliku</span>
                            </label>
                        </div>
                        <button class="button is-link is-fullwidth is-rounded" type="submit">Prześlij</button>
                    </form>
                </div>
            </div>


            <!-- Opcje profilu -->
            <div class="column is-one-third">
                <nav class="panel  p-5 is-rounded">
                    <p class="panel-heading has-background-black-ter has-text-white has-text-centered">Opcje profilu</p>
                    <a href="/profil/edytuj" class="panel-block  has-text-white">
                        <span class="panel-icon"><i class="fas fa-user-edit"></i></span>
                        Edytuj profil
                    </a>
                    <a href="/profil/zmien-haslo" class="panel-block  has-text-white">
                        <span class="panel-icon"><i class="fas fa-key"></i></span>
                        Zmień hasło
                    </a>
                    <a href="/profil/powiadomienia" class="panel-block  has-text-white">
                        <span class="panel-icon"><i class="fas fa-bell"></i></span>
                        Powiadomienia
                    </a>
                    <a href="/profil/aktywnosc" class="panel-block  has-text-white">
                        <span class="panel-icon"><i class="fas fa-history"></i></span>
                        Historia aktywności
                    </a>
                </nav>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input) {
        const fileName = input.files[0]?.name || "Nie wybrano pliku";
        input.closest('.file').querySelector('.file-name').textContent = fileName;
    }
</script>