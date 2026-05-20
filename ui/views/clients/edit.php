<?php require_once __DIR__ . '/../header.php'; ?>

<form method="post" action="index.php?entity=clients&action=update&id=<?= $old['client_id'] ?>">
    <label>Фамилия <span class="required">*</span></label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name']) ?>" class="<?= isset($errors['last_name']) ? 'error' : '' ?>">
    <?php if (isset($errors['last_name'])): ?><div class="error-message"><?= $errors['last_name'] ?></div><?php endif; ?>

    <label>Имя <span class="required">*</span></label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name']) ?>" class="<?= isset($errors['first_name']) ? 'error' : '' ?>">
    <?php if (isset($errors['first_name'])): ?><div class="error-message"><?= $errors['first_name'] ?></div><?php endif; ?>

    <label>Отчество</label>
    <input type="text" name="patronymic" value="<?= htmlspecialchars($old['patronymic'] ?? '') ?>">

    <label>Телефон <span class="required">*</span></label>
    <input type="tel" name="phone" value="<?= htmlspecialchars($old['phone']) ?>" class="<?= isset($errors['phone']) ? 'error' : '' ?>">
    <?php if (isset($errors['phone'])): ?><div class="error-message"><?= $errors['phone'] ?></div><?php endif; ?>

    <label>Email <span class="required">*</span></label>
    <input type="email" name="email" value="<?= htmlspecialchars($old['email']) ?>" class="<?= isset($errors['email']) ? 'error' : '' ?>">
    <?php if (isset($errors['email'])): ?><div class="error-message"><?= $errors['email'] ?></div><?php endif; ?>

    <label>Дата рождения <span class="required">*</span></label>
    <input type="date" name="birth_date" value="<?= htmlspecialchars($old['birth_date']) ?>" class="<?= isset($errors['birth_date']) ? 'error' : '' ?>">
    <?php if (isset($errors['birth_date'])): ?><div class="error-message"><?= $errors['birth_date'] ?></div><?php endif; ?>

    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="index.php?entity=clients&action=list" class="btn btn-back">Отмена</a>
</form>

<?php require_once __DIR__ . '/../footer.php'; ?>
