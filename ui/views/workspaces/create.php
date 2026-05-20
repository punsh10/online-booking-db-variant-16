<?php require_once __DIR__ . '/../header.php'; ?>

<form method="post" action="index.php?entity=workspaces&action=store">
    <label>Название <span class="required">*</span></label>
    <input type="text" name="workspace_name" value="<?= htmlspecialchars($old['workspace_name'] ?? '') ?>">
    <?php if (isset($errors['workspace_name'])): ?><div class="error-message"><?= $errors['workspace_name'] ?></div><?php endif; ?>

    <label>Тип <span class="required">*</span></label>
    <select name="workspace_type">
        <option value="">— Выберите —</option>
        <option value="открытое" <?= ($old['workspace_type'] ?? '') === 'открытое' ? 'selected' : '' ?>>Открытое</option>
        <option value="переговорная" <?= ($old['workspace_type'] ?? '') === 'переговорная' ? 'selected' : '' ?>>Переговорная</option>
        <option value="офис" <?= ($old['workspace_type'] ?? '') === 'офис' ? 'selected' : '' ?>>Офис</option>
    </select>
    <?php if (isset($errors['workspace_type'])): ?><div class="error-message"><?= $errors['workspace_type'] ?></div><?php endif; ?>

    <label>Цена за час (руб.) <span class="required">*</span></label>
    <input type="number" name="price_per_hour" value="<?= htmlspecialchars($old['price_per_hour'] ?? '') ?>" step="0.01">
    <?php if (isset($errors['price_per_hour'])): ?><div class="error-message"><?= $errors['price_per_hour'] ?></div><?php endif; ?>

    <button type="submit" class="btn btn-primary">Создать</button>
    <a href="index.php?entity=workspaces&action=list" class="btn btn-back">Отмена</a>
</form>

<?php require_once __DIR__ . '/../footer.php'; ?>
