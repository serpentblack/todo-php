<?php
require __DIR__.'/vendor/autoload.php';

use App\TaskRepository;

$repo = new TaskRepository();

// Acciones
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
  <title>MyToDo • PHP + SQLite</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* ====== PALETA (ajustada al logo) ======
       - brand        : verde oscuro
       - brand-2      : verde menta (check)
       - accents      : contornos y hovers
    */
    :root{
      --brand:    #114C3F;   /* verde profundo del borde del ícono */
      --brand-2:  #9AD6AC;   /* verde claro del check */
      --ink:      #0B1F1A;   /* texto principal */
      --muted:    #5B6B65;   /* texto secundario */
      --bg:       #F7FCF9;   /* fondo muy claro verdoso */
      --ring:     #2A6B58;   /* foco/borde activo */
    }

    body{ background: var(--bg); color: var(--ink); }

    /* Cabecera brand */
    .brand-bar{
      background: #ffffff;
      border-bottom: 1px solid rgba(17,76,63,0.08);
    }
    .brand-title{
      color: var(--brand);
      font-weight: 800;
      letter-spacing: .2px;
    }

    /* Tarjetas y cabeceras */
    .card{
      border: 1px solid rgba(17,76,63,0.08);
      box-shadow: 0 6px 18px rgba(17,76,63,0.06);
    }
    .card-header{
      background: linear-gradient(180deg, rgba(154,214,172,.25), rgba(154,214,172,.05));
      color: var(--brand);
      font-weight: 600;
    }

    /* Botones primarios al tono */
    .btn-primary{
      --bs-btn-bg: var(--brand);
      --bs-btn-border-color: var(--brand);
      --bs-btn-hover-bg: #0E3F35;
      --bs-btn-hover-border-color: #0E3F35;
      --bs-btn-active-bg: #0B352C;
      --bs-btn-active-border-color: #0B352C;
    }
    .btn-outline-secondary{
      --bs-btn-color: var(--brand);
      --bs-btn-border-color: var(--brand);
      --bs-btn-hover-bg: var(--brand);
      --bs-btn-hover-border-color: var(--brand);
      --bs-btn-hover-color: #fff;
    }
    .btn-outline-danger{
      --bs-btn-border-color: #CB3A31;
      --bs-btn-color: #CB3A31;
      --bs-btn-hover-bg: #CB3A31; --bs-btn-hover-color: #fff;
    }

    /* Inputs con foco en verde */
    .form-control:focus{
      border-color: var(--ring);
      box-shadow: 0 0 0 .2rem rgba(42,107,88,.15);
    }
    label.form-label{ color: var(--muted); }

    /* Elementos de la lista */
    .list-group-item{
      border-color: rgba(17,76,63,0.08);
    }
    .done{ text-decoration: line-through; color: var(--muted); }

    /* Badge/contador bonito */
    .badge-soft{
      background: rgba(154,214,172,.25);
      color: var(--brand);
      border: 1px solid rgba(17,76,63,0.15);
    }

    /* Link */
    a{ color: var(--brand); }
    a:hover{ color:#0E3F35; }
	/* Tamaño cómodo del logo + respuesta */
	.brand-logo{
	  width: 72px;        /* ↑ Ajusta aquí si lo quieres aún más grande (p.ej., 88px o 96px) */
	  height: 72px;
	  object-fit: contain;
	}

	@media (min-width: 992px){
	  .brand-logo{
		width: 84px;      /* un poco más grande en pantallas grandes */
		height: 84px;
	  }
	}

	.brand-bar .me-3{ margin-right: 1.25rem !important; } /* antes era 1rem */

  </style>
</head>
<body>
  <!-- CABECERA CON LOGO -->
  <header class="brand-bar">
    <div class="container py-3 d-flex align-items-center">
      <img src="assets/logo.png" alt="MyToDo" class="brand-logo me-3" />
      <div>
        <div class="brand-title h3 mb-0">MyToDo</div>
        <small class="text-muted">PHP + SQLite + Bootstrap</small>
      </div>
      <div class="ms-auto">
        <span class="badge badge-soft rounded-pill px-3 py-2">
          Tareas: <?= count($tasks) ?>
        </span>
      </div>
    </div>
  </header>

  <main class="container py-4">
    <!-- Formulario de alta -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="post" class="row gy-2 gx-2 align-items-end">
          <input type="hidden" name="action" value="add">
          <div class="col-12 col-md-4">
            <label class="form-label">Título*</label>
            <input name="title" class="form-control" required placeholder="Ej. Comprar café">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Descripción</label>
            <input name="description" class="form-control" placeholder="Opcional: detalles…">
          </div>
          <div class="col-12 col-md-2">
            <button class="btn btn-primary w-100">Agregar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Lista -->
    <div class="card">
      <div class="card-header d-flex align-items-center">
        <span class="me-2">Tareas</span>
        <span class="badge badge-soft rounded-pill"><?= count($tasks) ?></span>
      </div>
      <ul class="list-group list-group-flush">
        <?php if (!$tasks): ?>
          <li class="list-group-item text-muted">No hay tareas aún. ¡Crea la primera!</li>
        <?php endif; ?>
        <?php foreach ($tasks as $t): ?>
          <li class="list-group-item d-flex align-items-center justify-content-between">
            <div class="<?= $t['done'] ? 'done' : '' ?>">
              <strong>#<?= htmlspecialchars((string)$t['id']) ?></strong>
              &nbsp;<?= htmlspecialchars($t['title']) ?>
              <?php if ($t['description']): ?>
                <small class="text-muted">— <?= htmlspecialchars($t['description']) ?></small>
              <?php endif; ?>
            </div>
            <div class="btn-group">
              <form method="post" class="me-2">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button class="btn btn-outline-secondary btn-sm">
                  <?= $t['done'] ? 'Reactivar' : 'Completar' ?>
                </button>
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
      Hecho con ❤ en PHP 8 + SQLite + Bootstrap
    </p>
  </main>
</body>
</html>
