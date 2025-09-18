<?php
namespace App;

use PDO;

class Database {
  public static function path(): string {
    $envPath = getenv('DB_PATH');
    if ($envPath && $envPath !== '') return $envPath;
    $default = __DIR__ . '/../data/tasks.sqlite';
    return $default;
  }

  public static function pdo(): PDO {
    $path = self::path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
      mkdir($dir, 0777, true);
    }
    $create = !file_exists($path);
    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($create) {
      $pdo->exec('CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT,
        done INTEGER NOT NULL DEFAULT 0,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
      )');
    }
    return $pdo;
  }
}
