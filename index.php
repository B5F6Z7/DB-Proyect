<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/models/Docente.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod   = trim($_POST['cod_doc'] ?? '');
    $clave = $_POST['clave']        ?? '';

    if ($cod === '' || $clave === '') {
        $error = 'Debe ingresar código y contraseña.';
    } else {
        $doc = Docente::autenticar($cod, $clave);
        if ($doc) {
            setDocente($doc);
            header('Location: ' . BASE_URL . '/pages/dashboard.php');
            exit;
        }
        $error = 'Credenciales incorrectas.';
    }
}

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/pages/dashboard.php');
    exit;
}

$page_title = 'Iniciar Sesión';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión | <?= APP_NAME ?></title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <form class="login-card" method="POST" action="" autocomplete="off">
            <div class="icon-top"><i class="bi bi-mortarboard-fill"></i></div>
            <h1>Registro de Notas</h1>
            <p class="sub">Acceso para docentes</p>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="cod_doc"><i class="bi bi-person-badge"></i> Código del docente</label>
                <input type="text" id="cod_doc" name="cod_doc"
                       class="form-control"
                       value="<?= htmlspecialchars($_POST['cod_doc'] ?? '') ?>"
                       placeholder="Ej: DOC001" required>
            </div>

            <div class="form-group">
                <label for="clave"><i class="bi bi-lock-fill"></i> Contraseña</label>
                <input type="password" id="clave" name="clave"
                       class="form-control"
                       placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-16">
                <i class="bi bi-box-arrow-in-right"></i> Ingresar
            </button>

            <p class="text-center text-muted mt-16" style="font-size:12px;">
                Demo: <strong>DOC001</strong> / <strong>123456</strong>
            </p>
        </form>
    </div>
</body>
</html>
