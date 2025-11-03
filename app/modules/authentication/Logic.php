<?php

namespace Modules\authentication;

use Core\Helpers;
use JetBrains\PhpStorm\NoReturn;
use PDO;

class Logic
{
    private PDO $db;
    private array $config;

    public function __construct($db, $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function index(): void
    {
        Helpers::render(
            __DIR__ . '/views/login.php', [
            'title' => 'Login Page',
            'error' => null
        ],
            'auth'
        );
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = Helpers::sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                Helpers::sessionStart($this->config['session_cookie_name']);
                $_SESSION['user_id'] = $user['id'];
                Helpers::redirect('/dashboard');

            } else {
                $error = 'Invalid credentials';
            }
        }

        Helpers::render(__DIR__ . '/views/login.php', [
            'title' => 'Login Page',
            'error' => $error ?? null
        ], 'auth');
    }

    #[NoReturn]
    public function logout()
    {
        Helpers::sessionStart($this->config['session_cookie_name']);
        session_destroy();
        Helpers::redirect('index.php?module=Authentication&action=index');
    }
}
