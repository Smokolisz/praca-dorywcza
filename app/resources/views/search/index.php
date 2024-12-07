<?php $this->startSection('title'); ?>
Wyniki wyszukiwania
<?php $this->endSection(); ?>

<div class="container py-6">

    <div class="box">

        <form method="GET" action="/szukaj">
            <div class="field has-addons">
                <div class="control is-expanded">
                    <input 
                        class="input py-4" 
                        style="height: auto;" 
                        type="text" 
                        name="q" 
                        value="<?= isset($_GET['q']) ? $_GET['q'] : ''  ?>"
                    >
                </div>
                <div class="control">
                    <button type="submit" class="button is-info py-4 px-5" style="height: 100%;">Szukaj</button>
                </div>
            </div>

            <div class="pt-2">
                <!-- FILTRY -->
                <div class="columns is-multiline">

                    <!-- Rodzaj stawki -->
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Rodzaj stawki</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select name="payment_type">
                                        <option value="">Wszystkie</option>
                                        <option value="godzinowa" <?= (isset($_GET['payment_type']) && $_GET['payment_type'] === 'godzinowa') ? 'selected' : '' ?>>Godzinowa</option>
                                        <option value="za_cala_prace" <?= (isset($_GET['payment_type']) && $_GET['payment_type'] === 'za_cala_prace') ? 'selected' : '' ?>>Za całą pracę</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Miasto -->
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Miasto</label>
                            <div class="control">
                                <input class="input" type="text" name="city" placeholder="Miasto" value="<?= isset($_GET['city']) ? htmlspecialchars($_GET['city']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Wynagrodzenie od -->
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Wynagrodzenie od</label>
                            <div class="control">
                                <input class="input" type="number" step="0.01" name="payment_from" placeholder="Minimalna kwota" value="<?= isset($_GET['payment_from']) ? htmlspecialchars($_GET['payment_from']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Wynagrodzenie do -->
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Wynagrodzenie do</label>
                            <div class="control">
                                <input class="input" type="number" step="0.01" name="payment_to" placeholder="Maksymalna kwota" value="<?= isset($_GET['payment_to']) ? htmlspecialchars($_GET['payment_to']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Przewidywany czas wykonania od -->
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Przewidywany czas od (godz.)</label>
                            <div class="control">
                                <input class="input" type="number" name="estimated_time_from" placeholder="Od" value="<?= isset($_GET['estimated_time_from']) ? htmlspecialchars($_GET['estimated_time_from']) : '' ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Przewidywany czas wykonania do -->
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Przewidywany czas do (godz.)</label>
                            <div class="control">
                                <input class="input" type="number" name="estimated_time_to" placeholder="Do" value="<?= isset($_GET['estimated_time_to']) ? htmlspecialchars($_GET['estimated_time_to']) : '' ?>">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>

    </div>


    <?php
    foreach ($jobs as $job):
    ?>
        <div class="box mt-1">
            <article class="media">
                <div class="media-content">
                    <h2 class="title is-5">
                        <?= $job->job_type ?>
                    </h2>

                    <p class="subtitle is-6 pt-2">
                        <?= $job->employer_name ?> | <?= $job->city ?>
                    </p>

                    <div class="content">
                        <?= substr($job->description, 0, 200) . '...' ?>
                    </div>

                    <p class="is-size-7 has-text-weight-semibold">
                        Wynagrodzenie: <?= $job->payment_type ?> - <?= $job->payment ?> PLN
                    </p>
                </div>

                <div class="media-right">
                    <a class="button is-link is-small" href="/job/<?= $job->id ?>">Zobacz szczegóły</a>
                </div>
            </article>
        </div>

    <?php
    endforeach;
    ?>

    <?php
    if (count($jobs) == 0):
    ?>
        <div class="mt-6 mb-3">
            <p class="title is-2">Nie znaleźliśmy żadnych ogłoszeń :(</p>
            <p>Spróbuj zmienić swoje filtry wyszukiwania</p>
        </div>
    <?php
    endif;
    ?>

</div>