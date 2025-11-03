<?php

namespace Modules\dashboard;

use Core\Helpers;
use PDO;

class Logic
{
    private PDO $db;
    private array $config;

    public function __construct(PDO $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function index(): void
    {
        $data = [
            'title' => 'Dashboard Home',
            'message' => 'Welcome to your dashboard!'
        ];

        Helpers::render(__DIR__ . '/views/index.php', $data, 'dashboard');
    }
}
