<h2>Login</h2>

<?php if (isset($error) && $error): ?>
    <p style="color:red;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form method="post" action="/authentication/login">
    <label>Username: <input type="text" name="username"></label><br>
    <label>Password: <input type="password" name="password"></label><br>
    <?php
    $label = 'Login';
    $attrs = ['type' => 'submit', 'class' => 'btn-primary'];
    require __DIR__ . '/../../../../app/views/components/button.php';
    ?>
</form>
