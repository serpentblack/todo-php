<?php
require __DIR__.'/vendor/autoload.php';

use App\TaskRepository;

$repo = new TaskRepository();

// Manejo de acciones POST (add/toggle/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'add') {
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');
    if ($title !== '') {
      $repo->add($title, $desc ?: null);
    }
  } elseif ($action === 'toggle') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) $repo->toggle($id);
  } elseif ($action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) $repo->delete($id);
  }
  header('Location: '.$_SERVER['PHP_SELF']);
  exit;
}

$tasks = $repo->all();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MyToDo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.done{ text-decoration: line-through; color:#6c757d; }</style>
</head>
<body class="bg-light">
  <div class="container py-4">
    <h1 class="mb-4">✅ ToDo (PHP + SQLite)</h1>

    <div class="card mb-4">
      <div class="card-body">
        <form method="post" class="row gy-2 gx-2 align-items-end">
          <input type="hidden" name="action" value="add">
          <div class="col-12 col-md-4">
            <label class="form-label">Título*</label>
            <input name="title" class="form-control" required>
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Descripción</label>
            <input name="description" class="form-control">
          </div>
          <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100">Agregar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">Tareas (<?= count($tasks) ?>)</div>
      <ul class="list-group list-group-flush">
        <?php if (!$tasks): ?>
          <li class="list-group-item text-muted">No hay tareas aún.</li>
        <?php endif; ?>
        <?php foreach ($tasks as $t): ?>
          <li class="list-group-item d-flex align-items-center justify-content-between">
            <div class="<?= $t['done'] ? 'done' : '' ?>">
              <strong>#<?= htmlspecialchars((string)$t['id']) ?></strong>
              <?= htmlspecialchars($t['title']) ?>
              <?php if ($t['description']): ?>
                <small class="text-muted">— <?= htmlspecialchars($t['description']) ?></small>
              <?php endif; ?>
            </div>
            <div class="btn-group">
              <form method="post" class="me-2">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button class="btn btn-outline-secondary btn-sm"><?= $t['done'] ? 'Reactivar' : 'Completar' ?></button>
              </form>
              <form method="post" onsubmit="return confirm('¿Eliminar tarea #<?= (int)$t['id'] ?>?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button class="btn btn-outline-danger btn-sm">Eliminar</button>
              </form>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

    <p class="text-center text-muted mt-4">
      Desarrolloextremo, Cuando compartes aprendes...
    </p>
  </div>
</body>
</html>
