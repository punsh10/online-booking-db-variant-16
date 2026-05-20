<?php require_once __DIR__ . '/../header.php'; ?>

<?php if ($hasBookings): ?>
    <div class="alert alert-error">
        Нельзя удалить место &laquo;<?= htmlspecialchars($workspace['workspace_name']) ?>&raquo; — есть активные бронирования.
    </div>
    <a href="index.php?entity=workspaces&action=list" class="btn btn-back">Назад к списку</a>
<?php else: ?>
    <p>Удалить место &laquo;<?= htmlspecialchars($workspace['workspace_name']) ?>&raquo;?</p>
    <form method="post" action="index.php?entity=workspaces&action=destroy&id=<?= $workspace['workspace_id'] ?>">
        <button type="submit" class="btn btn-delete">Удалить</button>
        <a href="index.php?entity=workspaces&action=list" class="btn btn-back">Отмена</a>
    </form>
<?php endif; ?>

<?php require_once __DIR__ . '/../footer.php'; ?>
