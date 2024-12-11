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

                <hr>

                <!-- Wyniki wyszukiwania -->
                <?php if (!empty($listings)): ?>
                    <h2 class="title is-4">Ogłoszenia dla kategorii: <?php echo htmlspecialchars($category['name']); ?></h2>
                    <div class="columns is-multiline">
                        <?php foreach ($listings as $listing): ?>
                            <div class="column is-half">
                                <div class="box">
                                    <h3 class="title is-5"><?php echo htmlspecialchars($listing['job_type']); ?></h3>
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

            <!-- Sekcja z listą kategorii -->
            <div class="column is-one-third">
                <div class="box">
                    <h2 class="title is-5">Dostępne kategorie</h2>
                    <ul>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="/kategoria?category_name=<?php echo rawurlencode($cat['name']); ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
