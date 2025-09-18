<?php
namespace App;

use PDO;

class TaskRepository {
  private PDO $pdo;

  public function __construct(?PDO $pdo = null) {
    $this->pdo = $pdo ?? Database::pdo();
  }

  public function all(): array {
    $stmt = $this->pdo->query('SELECT * FROM tasks ORDER BY done ASC, id DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function add(string $title, ?string $description = null): void {
    $stmt = $this->pdo->prepare('INSERT INTO tasks (title, description) VALUES (?, ?)');
    $stmt->execute([$title, $description]);
  }

  public function toggle(int $id): void {
    $stmt = $this->pdo->prepare('UPDATE tasks SET done = 1 - done WHERE id = ?');
    $stmt->execute([$id]);
  }

  public function delete(int $id): void {
    $stmt = $this->pdo->prepare('DELETE FROM tasks WHERE id = ?');
    $stmt->execute([$id]);
  }
}
