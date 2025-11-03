<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Core\DB;

$config = require __DIR__ . '/../app/config/config.php';
$dbConfig = require __DIR__ . '/../app/config/db.php';

if (!is_array($dbConfig)) {
    throw new RuntimeException('Database config must be an array, got: ' . gettype($dbConfig));
}

$pdo = DB::getInstance($dbConfig)->getConnection();

$migrationsDir = __DIR__ . '/../migrations';
$upDir = $migrationsDir . '/up';
$downDir = $migrationsDir . '/down';
$logTable = 'migration_log';

function ensureLogTable(\PDO $pdo, string $table): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS {$table} (
            id SERIAL PRIMARY KEY,
            filename VARCHAR(255) NOT NULL UNIQUE,
            applied_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
        );
    ");
}

function getApplied(\PDO $pdo, string $table): array
{
    $stmt = $pdo->query("SELECT filename FROM {$table}");
    return $stmt->fetchAll(\PDO::FETCH_COLUMN);
}

function recordApplied(\PDO $pdo, string $table, string $file): void
{
    $stmt = $pdo->prepare("INSERT INTO {$table} (filename) VALUES (:f)");
    $stmt->execute(['f' => $file]);
}

function removeApplied(\PDO $pdo, string $table, string $file): void
{
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE filename = :f");
    $stmt->execute(['f' => $file]);
}

$cmd = $argv[1] ?? 'up';

ensureLogTable($pdo, $logTable);
$applied = getApplied($pdo, $logTable);

if ($cmd === 'up') {
    $files = array_diff(scandir($upDir), ['.', '..']);
    sort($files);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'sql') continue;
        if (in_array($file, $applied, true)) {
            continue;
        }
        echo "[UP] {$file}\n";
        $sql = file_get_contents($upDir . '/' . $file);
        $pdo->exec($sql);
        recordApplied($pdo, $logTable, $file);
    }
    echo "Migration UP complete.\n";

} elseif ($cmd === 'down') {
    $files = array_diff(scandir($downDir), ['.', '..']);
    rsort($files);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'sql') continue;
        if (!in_array($file, $applied, true)) {
            continue;
        }
        echo "[DOWN] {$file}\n";
        $sql = file_get_contents($downDir . '/' . $file);
        $pdo->exec($sql);
        removeApplied($pdo, $logTable, $file);
    }
    echo "Migration DOWN complete.\n";

} else {
    echo "Usage: php migrate.php [up|down]\n";
    exit(1);
}
