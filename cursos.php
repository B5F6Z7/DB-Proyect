<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Curso.php';

requireLogin();
$doc    = getDocente();
$cursos = Curso::listarPorDocente($doc['cod_doc']);

$post_values = [];  // valores re-mostrados en caso de error

// Procesar selección de curso/año/periodo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cod_cur = trim($_POST['cod_cur'] ?? '');
    $year    = (int) ($_POST['year']    ?? 0);
    $periodo = (int) ($_POST['periodo'] ?? 0);

    $errores = [];
    if ($cod_cur === '' || !Curso::perteneceADocente($cod_cur, $doc['cod_doc'])) {
        $errores[] = 'Debe seleccionar un curso válido.';
    }
    if ($year < 2000 || $year > 2100) {
        $errores[] = 'El año debe estar entre 2000 y 2100.';
    }
    if (!in_array($periodo, [1, 2], true)) {
        $errores[] = 'Debe seleccionar un período válido.';
    }

    if (empty($errores)) {
        $cur_data = Curso::obtener($cod_cur);
        $_SESSION['contexto'] = [
            'cod_cur'  => $cod_cur,
            'nomb_cur' => $cur_data ? $cur_data['nomb_cur'] : '',
            'year'     => $year,
            'periodo'  => $periodo,
        ];
        $_SESSION['flash_success'] = 'Contexto seleccionado correctamente.';
        header('Location: ' . BASE_URL . '/pages/inscripciones.php');
        exit;
    }
    $_SESSION['flash_error'] = implode(' ', $errores);
    $post_values = compact('cod_cur', 'year', 'periodo');
}

// Pre-llenar formulario:
//   1) Si vienen valores en POST con error, los preserva.
//   2) Si llega ?cod=X desde el dashboard, lo pre-selecciona.
//   3) Si existe contexto en sesión, lo usa.
//   4) Si nada de lo anterior, valores por defecto.
$ctx = $_SESSION['contexto'] ?? [];
$sel_cur  = $post_values['cod_cur'] ?? $_GET['cod'] ?? $ctx['cod_cur'] ?? '';
$sel_year = $post_values['year']    ?? $ctx['year']    ?? (int) date('Y');
$sel_per  = $post_values['periodo'] ?? $ctx['periodo'] ?? 1;

$page_title  = 'Cursos del Docente';
$subbar_text = 'INFORMACIÓN DE DOCENTES';
require_once __DIR__ . '/../includes/layout_top.php';
?>

<div class="card" style="max-width: 600px;">
    <h3 class="card-title"><i class="bi bi-book-fill"></i> Cursos de docente</h3>

    <form method="POST" action="">
        <div class="form-group">
            <label for="cod_cur"><i class="bi bi-journal"></i> Curso</label>
            <select id="cod_cur" name="cod_cur" class="form-control" required>
                <option value="">— Seleccione un curso —</option>
                <?php foreach ($cursos as $c): ?>
                    <option value="<?= htmlspecialchars($c['cod_cur']) ?>"
                        <?= $c['cod_cur'] === $sel_cur ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nomb_cur']) ?> (<?= htmlspecialchars($c['cod_cur']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="year"><i class="bi bi-calendar3"></i> Año</label>
                <input type="number" id="year" name="year"
                       class="form-control"
                       value="<?= htmlspecialchars((string) $sel_year) ?>"
                       min="2000" max="2100" required>
            </div>

            <div class="form-group">
                <label><i class="bi bi-clock-history"></i> Período</label>
                <div class="radio-group">
                    <label><input type="radio" name="periodo" value="1" <?= $sel_per == 1 ? 'checked' : '' ?>> Período I</label>
                    <label><input type="radio" name="periodo" value="2" <?= $sel_per == 2 ? 'checked' : '' ?>> Período II</label>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-arrow-right-circle"></i> Ver listado
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
