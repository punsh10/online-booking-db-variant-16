<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — Коворкинг</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="menu">
    <a href="index.php?entity=clients&action=list">Клиенты</a>
    <a href="index.php?entity=workspaces&action=list">Рабочие места</a>
    <a href="index.php?entity=discounts&action=list">Скидки</a>
</div>

<h1><?= htmlspecialchars($title) ?></h1>

<?php if (isset($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?>">
        <?= htmlspecialchars($_SESSION['flash']['message']) ?>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>
