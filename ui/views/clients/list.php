<?php require_once __DIR__ . '/../header.php'; ?>

<div class="search-box">
    <form method="get" action="index.php">
        <input type="hidden" name="entity" value="clients">
        <input type="hidden" name="action" value="list">
        <input type="text" name="search" placeholder="Поиск по фамилии или телефону" value="<?= htmlspecialchars($search ?? '') ?>">
        <button type="submit" class="btn btn-primary">Найти</button>
        <?php if (!empty($search)): ?>
            <a href="index.php?entity=clients&action=list" class="btn btn-back">Сбросить</a>
        <?php endif; ?>
    </form>
</div>

<a href="index.php?entity=clients&action=create" class="btn btn-primary">Добавить клиента</a>

<table>
    <thead>
        <tr><th>ID</th><th>Фамилия</th><th>Имя</th><th>Телефон</th><th>Email</th><th>Дата рождения</th><th>Действия</th></tr>
    </thead>
    <tbody>
        <?php foreach ($clients as $client): ?>
        <tr>
            <td><?= $client['client_id'] ?></td>
            <td><?= htmlspecialchars($client['last_name']) ?></td>
            <td><?= htmlspecialchars($client['first_name']) ?></td>
            <td><?= htmlspecialchars($client['phone']) ?></td>
            <td><?= htmlspecialchars($client['email']) ?></td>
            <td><?= htmlspecialchars($client['birth_date']) ?></td>
            <td>
                <a href="index.php?entity=clients&action=edit&id=<?= $client['client_id'] ?>" class="btn btn-edit">Изменить</a>
                <a href="index.php?entity=clients&action=delete&id=<?= $client['client_id'] ?>" class="btn btn-delete">Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../footer.php'; ?>
