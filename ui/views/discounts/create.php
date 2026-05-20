<?php require_once __DIR__ . '/../header.php'; ?>

<form method="post" action="index.php?entity=discounts&action=store">
    <label>Клиент <span class="required">*</span></label>
    <select name="client_id">
        <option value="">— Выберите клиента —</option>
        <?php foreach ($clients as $c): ?>
        <option value="<?= $c['client_id'] ?>" <?= ($old['client_id'] ?? '') == $c['client_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['last_name'] . ' ' . $c['first_name']) ?>
        </option>
        <?php endforeach; ?>
    </select>
    <?php if (isset($errors['client_id'])): ?><div class="error-message"><?= $errors['client_id'] ?></div><?php endif; ?>

    <label>Процент скидки (5-50) <span class="required">*</span></label>
    <input type="number" name="discount_percent" value="<?= htmlspecialchars($old['discount_percent'] ?? '') ?>" min="5" max="50">
    <?php if (isset($errors['discount_percent'])): ?><div class="error-message"><?= $errors['discount_percent'] ?></div><?php endif; ?>

    <button type="submit" class="btn btn-primary">Создать</button>
    <a href="index.php?entity=discounts&action=list" class="btn btn-back">Отмена</a>
</form>

<?php require_once __DIR__ . '/../footer.php'; ?>
