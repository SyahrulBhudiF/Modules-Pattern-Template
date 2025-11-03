<?php

namespace Core;

use JetBrains\PhpStorm\NoReturn;

class Helpers
{
    public static function sanitize($value)
    {
        if (is_string($value)) {
            return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }
        return $value;
    }

    #[NoReturn]
    public static function redirect($url): void
    {
        header('Location: ' . $url);
        exit;
    }

    public static function sessionStart($cookieName = 'SESSION'): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name($cookieName);
            session_start();
        }
    }

    public static function requireLogin(): void
    {
        self::sessionStart();
        if (!isset($_SESSION['user_id'])) {
            self::redirect('index.php?module=Authentication&action=index');
        }
    }

    public static function render(string $viewPath, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        $title = $data['title'] ?? null;

        require __DIR__ . "/../views/layouts/{$layout}.php";
    }
}
