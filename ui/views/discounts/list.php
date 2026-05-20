<?php require_once __DIR__ . '/../header.php'; ?>

<a href="index.php?entity=discounts&action=create" class="btn btn-primary">Добавить скидку</a>

<table>
    <thead>
        <tr><th>ID</th><th>Клиент ID</th><th>Процент</th><th>Активна</th><th>Дата</th><th>Действия</th></tr>
    </thead>
    <tbody>
        <?php foreach ($discounts as $d): ?>
        <tr>
            <td><?= $d['discount_id'] ?></td>
            <td><?= $d['client_id'] ?></td>
            <td><?= $d['discount_percent'] ?>%</td>
            <td><?= $d['is_active'] ? 'Да' : 'Нет' ?></td>
            <td><?= $d['granted_date'] ?></td>
            <td>
                <a href="index.php?entity=discounts&action=delete&id=<?= $d['discount_id'] ?>" class="btn btn-delete">Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../footer.php'; ?>
