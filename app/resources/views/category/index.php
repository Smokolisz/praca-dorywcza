<?php $this->startSection('title'); ?>
Wyszukiwarka kategorii
<?php $this->endSection(); ?>

<section class="section">
    <div class="container">
        <div class="columns">
            <!-- Główna sekcja wyszukiwania i wyników -->
            <div class="column is-two-thirds">
                <h1 class="title">Wyszukiwarka kategorii</h1>
                <p class="subtitle">Wpisz nazwę kategorii, aby znaleźć powiązane ogłoszenia.</p>

                <!-- Formularz wyszukiwania -->
                <form action="/kategoria" method="GET" class="box">
                    <div class="field">
                        <label class="label">Nazwa kategorii</label>
                        <div class="control">
                            <input 
                                class="input" 
                                type="text" 
                                name="category_name" 
                                placeholder="Wpisz nazwę kategorii (np. IT)" 
                                value="<?php echo htmlspecialchars($_GET['category_name'] ?? ''); ?>" 
                                required>
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <button type="submit" class="button is-primary">Wyszukaj</button>
                        </div>
                    </div>
                </form>

                <!-- Box ulubionych kategorii -->
                <div class="box" style="background-color: none; color: #fff; margin-top: 20px;">
                    <h2 class="title is-5" style="color: #fff;">Ulubione kategorie</h2>
                    <?php if (!empty($favoriteCategories)): ?>
                        <ul style="list-style-type: none; padding-left: 0;">
                            <?php foreach ($categories as $cat): ?>
                                <?php if (in_array($cat['id'], $favoriteCategories)): ?>
                                    <li style="margin-bottom: 10px;">
                                        <a href="/kategoria?category_name=<?php echo rawurlencode($cat['name']); ?>" 
                                           style="font-size: 1.1em; color: #fff; text-decoration: none;">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Nie obserwujesz żadnych kategorii.</p>
                    <?php endif; ?>
                </div>

                <hr>

                <!-- Wyniki wyszukiwania -->
                <?php if (!empty($listings)): ?>
                    <h2 class="title is-4">Ogłoszenia dla kategorii: <?php echo htmlspecialchars($category['name']); ?></h2>
                    <div class="columns is-multiline">
                        <?php foreach ($listings as $listing): ?>
                            <div class="column is-half">
                                <div class="box">
                                    <h3 class="title is-5"><a href="/job/<?= htmlspecialchars($listing['id']) ?>"><?php echo htmlspecialchars($listing['job_type']); ?></a></h3>
                                    <p><?php echo htmlspecialchars($listing['description']); ?></p>
                                    <p><strong>Płatność:</strong> <?php echo htmlspecialchars($listing['payment_type']); ?></p>
                                    <p><strong>Pracodawca:</strong> <?php echo htmlspecialchars($listing['employer_name'] ?? 'Nie podano'); ?></p>
                                    <p><strong>Miasto:</strong> <?php echo htmlspecialchars($listing['city'] ?? 'Nie podano'); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif (isset($_GET['category_name'])): ?>
                    <div class="notification is-danger">
                        Nie znaleziono kategorii ani ogłoszeń dla "<?php echo htmlspecialchars($_GET['category_name']); ?>".
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sekcja boczna z kategoriami -->
            <div class="column is-one-third">
                <!-- Box dostępnych kategorii -->
                <div class="box" style="background-color: none; color: #fff;">
                    <h2 class="title is-5" style="color: #fff;">Dostępne kategorie</h2>
                    <ul style="list-style-type: none; padding-left: 0;">
                        <?php foreach ($categories as $cat): ?>
                            <li style="margin-bottom: 10px; display: flex; justify-content: space-between;">
                                <a href="/kategoria?category_name=<?php echo rawurlencode($cat['name']); ?>" 
                                   style="font-size: 1.1em; color: #fff; text-decoration: none;">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                                <?php if (in_array($cat['id'], $favoriteCategories)): ?>
                                    <a href="/kategoria/ulubione/usun/<?php echo $cat['id']; ?>" style="color: red;">
                                        ❤️
                                    </a>
                                <?php else: ?>
                                    <a href="/kategoria/ulubione/dodaj/<?php echo $cat['id']; ?>" style="color: gray;">
                                        🤍
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
