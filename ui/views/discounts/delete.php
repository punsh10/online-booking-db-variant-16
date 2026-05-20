<?php require_once __DIR__ . '/../header.php'; ?>

<p>Удалить скидку <?= $discount['discount_percent'] ?>% для клиента ID <?= $discount['client_id'] ?>?</p>

<form method="post" action="index.php?entity=discounts&action=destroy&id=<?= $discount['discount_id'] ?>">
    <button type="submit" class="btn btn-delete">Удалить</button>
    <a href="index.php?entity=discounts&action=list" class="btn btn-back">Отмена</a>
</form>

<?php require_once __DIR__ . '/../footer.php'; ?>
