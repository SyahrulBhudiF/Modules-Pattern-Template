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
    public static function redirect($url)
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
}
