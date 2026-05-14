<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Nota.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Calificacion.php';

requireLogin();
$doc = getDocente();

if (empty($_SESSION['contexto']['cod_cur'])) {
    $_SESSION['flash_error'] = 'Debe seleccionar primero un curso, año y período.';
    header('Location: ' . BASE_URL . '/pages/cursos.php');
    exit;
}
$ctx = $_SESSION['contexto'];

if (!Curso::perteneceADocente($ctx['cod_cur'], $doc['cod_doc'])) {
    $_SESSION['flash_error'] = 'No tiene permisos sobre este curso.';
    header('Location: ' . BASE_URL . '/pages/cursos.php');
    exit;
}

// Determinar la nota/cohorte seleccionada
$cohortes  = Nota::listarPorCurso($ctx['cod_cur']);
$nota_id   = isset($_GET['nota']) ? (int) $_GET['nota'] : 0;
if (!$nota_id && !empty($cohortes)) $nota_id = (int) $cohortes[0]['nota'];

$nota = $nota_id ? Nota::obtener($nota_id) : null;
if ($nota && $nota['cod_cur'] !== $ctx['cod_cur']) $nota = null;

// Procesar guardado de notas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nota) {
    try {
        $cod_est = trim($_POST['cod_est'] ?? '');
        $valor   = $_POST['valor'] ?? '';

        if ($cod_est === '') throw new Exception('Estudiante no especificado.');
        if (!is_numeric($valor)) throw new Exception('Nota inválida.');
        $valor = (float) $valor;
        if ($valor < 0 || $valor > 5) throw new Exception('La nota debe estar entre 0.0 y 5.0.');

        if (!Inscripcion::existe($ctx['cod_cur'], $cod_est, $ctx['year'], $ctx['periodo'])) {
            throw new Exception('El estudiante no está inscrito en este curso.');
        }

        Calificacion::guardar(
            $nota['nota'], $valor, $ctx['cod_cur'],
            $cod_est, $ctx['year'], $ctx['periodo']
        );
        $_SESSION['flash_success'] = "Nota guardada para estudiante $cod_est.";
    } catch (Throwable $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
    header('Location: ' . BASE_URL . '/pages/registro_notas.php?nota=' . $nota['nota']);
    exit;
}

$inscritos = Inscripcion::listarPorCurso($ctx['cod_cur'], $ctx['year'], $ctx['periodo']);

// Construir mapa de calificaciones existentes
$mapa = [];
if ($nota) {
    foreach (Calificacion::listarPorNota($nota['nota'], $ctx['year'], $ctx['periodo']) as $c) {
        $mapa[$c['cod_est']] = (float) $c['valor'];
    }
}

$page_title  = 'Registro y Actualización de Notas';
$subbar_text = sprintf('REGISTRO DE NOTAS DE %s', $ctx['nomb_cur']);
require_once __DIR__ . '/../includes/layout_top.php';
?>

<?php if (empty($cohortes)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Este curso aún no tiene notas/cohortes definidos.
        <a href="<?= BASE_URL ?>/pages/notas_parciales.php">Defínalos primero aquí</a>.
    </div>
<?php elseif (empty($inscritos)): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill"></i>
        No hay estudiantes inscritos en este curso/período.
        <a href="<?= BASE_URL ?>/pages/inscripciones.php">Inscriba estudiantes aquí</a>.
    </div>
<?php else: ?>

<div class="card">
    <!-- Selector de cohorte -->
    <form method="GET" class="mb-16">
        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="cohorte">Cohorte / Nota parcial</label>
                <select name="nota" id="cohorte" class="form-control" onchange="this.form.submit()">
                    <?php foreach ($cohortes as $co): ?>
                        <option value="<?= $co['nota'] ?>" <?= $nota && $co['nota'] == $nota['nota'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($co['desc_nota']) ?>
                            (<?= number_format($co['porcentaje'], 2) ?>%)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($nota): ?>
                <div class="form-group">
                    <label>Descripción</label>
                    <div class="form-control" style="background:#f1f5f9;"><?= htmlspecialchars($nota['desc_nota']) ?></div>
                </div>
                <div class="form-group">
                    <label>Porcentaje</label>
                    <div class="form-control" style="background:#f1f5f9;"><?= number_format($nota['porcentaje'], 2) ?>%</div>
                </div>
            <?php endif; ?>
        </div>
    </form>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 120px;">Código</th>
                    <th>Nombre</th>
                    <th style="width: 220px;">Nota (0.0 – 5.0)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inscritos as $e): ?>
                    <?php $val = $mapa[$e['cod_est']] ?? null; ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($e['cod_est']) ?></strong></td>
                        <td><?= htmlspecialchars($e['nomb_est']) ?></td>
                        <td>
                            <form method="POST" style="display:flex; gap:6px; align-items:center;">
                                <input type="hidden" name="cod_est" value="<?= htmlspecialchars($e['cod_est']) ?>">
                                <input type="number" name="valor"
                                       class="form-control"
                                       style="width:90px;"
                                       min="0" max="5" step="0.01"
                                       value="<?= $val !== null ? number_format($val, 2, '.', '') : '' ?>"
                                       required>
                                <button type="submit" class="btn btn-icon btn-success" title="Guardar">
                                    <i class="bi bi-save-fill"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
