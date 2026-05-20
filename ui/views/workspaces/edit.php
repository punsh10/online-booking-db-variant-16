<?php require_once __DIR__ . '/../header.php'; ?>

<form method="post" action="index.php?entity=workspaces&action=update&id=<?= $old['workspace_id'] ?>">
    <label>Название <span class="required">*</span></label>
    <input type="text" name="workspace_name" value="<?= htmlspecialchars($old['workspace_name']) ?>">
    <?php if (isset($errors['workspace_name'])): ?><div class="error-message"><?= $errors['workspace_name'] ?></div><?php endif; ?>

    <label>Тип <span class="required">*</span></label>
    <select name="workspace_type">
        <option value="открытое" <?= $old['workspace_type'] === 'открытое' ? 'selected' : '' ?>>Открытое</option>
        <option value="переговорная" <?= $old['workspace_type'] === 'переговорная' ? 'selected' : '' ?>>Переговорная</option>
        <option value="офис" <?= $old['workspace_type'] === 'офис' ? 'selected' : '' ?>>Офис</option>
    </select>

    <label>Цена за час (руб.) <span class="required">*</span></label>
    <input type="number" name="price_per_hour" value="<?= htmlspecialchars($old['price_per_hour']) ?>" step="0.01">

    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="index.php?entity=workspaces&action=list" class="btn btn-back">Отмена</a>
</form>

<?php require_once __DIR__ . '/../footer.php'; ?>
