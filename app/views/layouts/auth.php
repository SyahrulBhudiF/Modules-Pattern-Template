<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Auth Area', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
<div class="auth-wrapper">
    <?= $content ?>
</div>
</body>
</html>
