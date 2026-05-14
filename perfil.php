<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Docente.php';

requireLogin();
$doc = getDocente();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual    = $_POST['actual']    ?? '';
    $nueva     = $_POST['nueva']     ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    try {
        if ($actual === '' || $nueva === '' || $confirmar === '') {
            throw new Exception('Todos los campos son obligatorios.');
        }
        if (!Docente::autenticar($doc['cod_doc'], $actual)) {
            throw new Exception('La contraseña actual es incorrecta.');
        }
        if (strlen($nueva) < 6) {
            throw new Exception('La nueva contraseña debe tener al menos 6 caracteres.');
        }
        if ($nueva !== $confirmar) {
            throw new Exception('La confirmación no coincide con la nueva contraseña.');
        }
        Docente::cambiarClave($doc['cod_doc'], $nueva);
        $_SESSION['flash_success'] = 'Contraseña actualizada correctamente.';
    } catch (Throwable $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
    header('Location: ' . BASE_URL . '/pages/perfil.php');
    exit;
}

$page_title  = 'Mi Perfil';
$subbar_text = 'INFORMACIÓN PERSONAL Y SEGURIDAD';
require_once __DIR__ . '/../includes/layout_top.php';
?>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">

    <div class="card">
        <h3 class="card-title"><i class="bi bi-person-circle"></i> Información</h3>
        <div class="form-group">
            <label>Código</label>
            <div class="form-control" style="background:#f1f5f9;"><?= htmlspecialchars($doc['cod_doc']) ?></div>
        </div>
        <div class="form-group">
            <label>Nombre</label>
            <div class="form-control" style="background:#f1f5f9;"><?= htmlspecialchars($doc['nomb_doc']) ?></div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title"><i class="bi bi-shield-lock-fill"></i> Cambiar contraseña</h3>
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label for="actual">Contraseña actual</label>
                <input id="actual" type="password" name="actual" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="nueva">Nueva contraseña</label>
                <input id="nueva" type="password" name="nueva" class="form-control" minlength="6" required>
            </div>
            <div class="form-group">
                <label for="confirmar">Confirmar nueva contraseña</label>
                <input id="confirmar" type="password" name="confirmar" class="form-control" minlength="6" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg"></i> Actualizar contraseña
            </button>
        </form>
    </div>

</div>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
