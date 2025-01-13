<div class="container mt-6">
    <h2 class="title is-4">Powiadomienia</h2>

    <?php if (empty($notifications)): ?>
        <p class="has-text-grey-light">Brak nowych powiadomień</p>
    <?php else: ?>
        <ul>
            <form method="post" action="/profil/powiadomienia/oznacz-jako-przeczytane">
                <button class="button is-primary ">Oznacz wszystkie jako przeczytane</button>
            </form>
            <?php foreach ($notifications as $notification): ?>
                <li class="box mt-2">
                    <p><?= htmlspecialchars($notification['content']) ?></p>
                    <small class="has-text-grey">Otrzymano: <?= htmlspecialchars($notification['created_at']) ?></small>
                    <?php if (!$notification['is_read']): ?>
                        <span class="tag is-danger mt-2">Nieprzeczytane</span>
                    <?php endif; ?>

                    <form method="post" action="/profil/powiadomienia/zobacz-szczegoly">
                        <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                        <button type="submit" class="button is-info is-small mt-2">Zobacz szczegóły</button>
                    </form>
                </li>

            <?php endforeach; ?>
        </ul>
        <?php
        // Sprawdzanie, czy użytkownik ma nieprzeczytane powiadomienia
        $hasNewNotifications = false;
        foreach ($notifications as $notification) {
            if (!$notification['is_read']) {
                $hasNewNotifications = true;
                break; // Jeśli znajdziemy przynajmniej jedno nieprzeczytane powiadomienie, przerwijmy
            }
        }
        ?>
    <?php endif; ?>
</div>