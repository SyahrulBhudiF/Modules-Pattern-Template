<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'My App', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>

<main>
    <?= $content ?>
</main>

<?php require __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
