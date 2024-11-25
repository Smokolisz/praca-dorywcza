<div class="container mt-5">
    <h2 class="title">Negocjacje Stawki</h2>

    <div class="container mt-6" style="padding-bottom:20px">
        <?php if (isset($_SESSION['negotiation_success'])): ?>
            <div class="notification is-success">
                <?= htmlspecialchars($_SESSION['negotiation_success']) ?>
            </div>
            <?php unset($_SESSION['negotiation_success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['negotiation_error'])): ?>
            <div class="notification is-danger">
                <?= htmlspecialchars($_SESSION['negotiation_error']) ?>
            </div>
            <?php unset($_SESSION['negotiation_error']); ?>
        <?php endif; ?>
    </div>

    <?php if ($listingStatus === 'open'): ?>
        <!-- Formularz złożenia oferty -->
        <form action="/negocjacje/start" method="POST">
            <input type="hidden" name="listing_id" value="<?= htmlspecialchars($listingId) ?>">

            <div class="field">
                <label class="label">Kwota Oferty</label>
                <div class="control">
                    <input class="input" type="number" name="offer_amount" min="1" required placeholder="Wpisz kwotę">
                </div>
            </div>

            <div class="field">
                <label class="label">Uzasadnienie (opcjonalne)</label>
                <div class="control">
                    <textarea class="textarea" name="justification" placeholder="Dodaj uzasadnienie swojej oferty"></textarea>
                </div>
            </div>

            <div class="control">
                <button class="button is-primary" type="submit">Zaproponuj stawkę</button>
            </div>
        </form>
    <?php else: ?>
        <div class="notification is-warning">
            To ogłoszenie jest zamknięte i nie przyjmuje nowych negocjacji.
        </div>
    <?php endif; ?>

    <!-- Propozycje ofert -->
    <h3 class="title is-5 mt-5">Propozycje Ofert</h3>
    <ul>
        <?php foreach ($negotiations as $negotiation): ?>
            <li>
                <strong>Data:</strong> <?= htmlspecialchars($negotiation['created_at']) ?><br>
                <strong>Użytkownik:</strong> <?= htmlspecialchars($negotiation['user_name']) ?><br>
                <strong>Kwota:</strong> <?= htmlspecialchars($negotiation['offer_amount']) ?> zł<br>
                <strong>Uzasadnienie:</strong> <?= nl2br(htmlspecialchars($negotiation['justification'])) ?><br>
                <strong>Status:</strong> <?= htmlspecialchars(ucfirst($negotiation['status'])) ?><br>

                <?php if ($negotiation['status'] === 'pending' && $currentUserId == $listingOwnerId && $listingStatus === 'open'): ?>
                    <!-- Przyciski akceptacji i odrzucenia oferty -->
                    <form action="/negocjacje/<?= htmlspecialchars($negotiation['id']) ?>/akceptacja" method="POST" style="display:inline;">
                        <button class="button is-success" type="submit">Akceptuj ofertę</button>
                    </form>
                    <form action="/negocjacje/<?= htmlspecialchars($negotiation['id']) ?>/odrzucenie" method="POST" style="display:inline;">
                        <button class="button is-danger" type="submit">Odrzuć ofertę</button>
                    </form>
                <?php endif; ?>
            </li>
            <hr>
        <?php endforeach; ?>
    </ul>
</div>