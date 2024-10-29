<div class="container mt-6">
    <h2 class="title is-4">Powiadomienia</h2>

    <?php if (empty($notifications)): ?>
        <p class="has-text-grey-light">Brak nowych powiadomień</p>
    <?php else: ?>
        <ul>
            <?php foreach ($notifications as $notification): ?>
                <li class="box">
                    <p><?= htmlspecialchars($notification['message']) ?></p>
                    <small class="has-text-grey">Otrzymano: <?= htmlspecialchars($notification['created_at']) ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>