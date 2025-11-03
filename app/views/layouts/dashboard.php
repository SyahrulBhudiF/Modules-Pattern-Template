<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Dashboard', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
<?php require __DIR__ . '/../components/header.php'; ?>

<div class="dashboard-container">
    <aside class="sidebar">
        <ul>
            <li><a href="/dashboard">Home</a></li>
            <li><a href="/authentication/logout">Logout</a></li>
        </ul>
    </aside>

    <section class="content">
        <?= $content ?>
    </section>
</div>

<?php require __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
