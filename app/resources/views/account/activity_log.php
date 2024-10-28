<div class="container mt-6">
    <div class="box">
        <h2 class="title is-4">Historia aktywności</h2>
        <div class="content">
            <p><strong>Imię i nazwisko:</strong> <?= htmlspecialchars($activity['first_name']) . ' ' . htmlspecialchars($activity['last_name']) ?></p>
            <p><strong>Data rejestracji:</strong> <?= htmlspecialchars($activity['date_created']) ?></p>
            <p><strong>Ostatnie logowanie:</strong> <?= htmlspecialchars($activity['last_login_date'] ?? 'Brak danych') ?></p>
            <p><strong>Ostatnia aktualizacja profilu:</strong> <?= htmlspecialchars($activity['updated_at']) ?></p>
            <p><strong>Status konta:</strong> <?= $activity['active'] ? 'Aktywne' : 'Nieaktywne' ?></p>
            <?php if ($activity['date_removed']): ?>
                <p><strong>Data usunięcia konta:</strong> <?= htmlspecialchars($activity['date_removed']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>