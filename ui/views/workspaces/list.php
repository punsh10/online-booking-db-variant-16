<?php require_once __DIR__ . '/../header.php'; ?>

<form method="get" action="index.php" class="search-box">
    <input type="hidden" name="entity" value="workspaces">
    <input type="hidden" name="action" value="list">
    <input type="text" name="search" placeholder="Поиск по названию" value="<?= htmlspecialchars($search ?? '') ?>">
    <select name="type">
        <option value="">Все типы</option>
        <option value="открытое" <?= ($type ?? '') === 'открытое' ? 'selected' : '' ?>>Открытое</option>
        <option value="переговорная" <?= ($type ?? '') === 'переговорная' ? 'selected' : '' ?>>Переговорная</option>
        <option value="офис" <?= ($type ?? '') === 'офис' ? 'selected' : '' ?>>Офис</option>
    </select>
    <button type="submit" class="btn btn-primary">Фильтр</button>
</form>

<a href="index.php?entity=workspaces&action=create" class="btn btn-primary">Добавить место</a>

<table>
    <thead>
        <tr><th>ID</th><th>Название</th><th>Тип</th><th>Цена/час</th><th>Действия</th></tr>
    </thead>
    <tbody>
        <?php foreach ($workspaces as $w): ?>
        <tr>
            <td><?= $w['workspace_id'] ?></td>
            <td><?= htmlspecialchars($w['workspace_name']) ?></td>
            <td><?= htmlspecialchars($w['workspace_type']) ?></td>
            <td><?= $w['price_per_hour'] ?> руб.</td>
            <td>
                <a href="index.php?entity=workspaces&action=edit&id=<?= $w['workspace_id'] ?>" class="btn btn-edit">Изменить</a>
                <a href="index.php?entity=workspaces&action=delete&id=<?= $w['workspace_id'] ?>" class="btn btn-delete">Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../footer.php'; ?>
