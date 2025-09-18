<?php
use PHPUnit\Framework\TestCase;
use App\TaskRepository;
use App\Database;
use PDO;

final class TaskRepositoryTest extends TestCase {
  private string $dbPath;
  private PDO $pdo;

  protected function setUp(): void {
    $this->dbPath = sys_get_temp_dir() . '/todo-test-' . uniqid() . '.sqlite';
    putenv('DB_PATH='.$this->dbPath);
    $this->pdo = Database::pdo();
  }

  protected function tearDown(): void {
    if (file_exists($this->dbPath)) { @unlink($this->dbPath); }
    putenv('DB_PATH'); // clear
  }

  public function testAddAndList(): void {
    $repo = new TaskRepository($this->pdo);
    $repo->add('Probar', 'Unit test');
    $all = $repo->all();
    $this->assertCount(1, $all);
    $this->assertSame('Probar', $all[0]['title']);
  }

  public function testToggleAndDelete(): void {
    $repo = new TaskRepository($this->pdo);
    $repo->add('A', null);
    $task = $repo->all()[0];
    $repo->toggle((int)$task['id']);
    $t2 = $repo->all()[0];
    $this->assertEquals(1, (int)$t2['done']);
    $repo->delete((int)$task['id']);
    $this->assertCount(0, $repo->all());
  }
}
