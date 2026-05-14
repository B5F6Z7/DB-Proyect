<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Estudiante.php';
require_once __DIR__ . '/../models/Inscripcion.php';

requireLogin();
$doc = getDocente();

// Exigir contexto seleccionado
if (empty($_SESSION['contexto']['cod_cur'])) {
    $_SESSION['flash_error'] = 'Debe seleccionar primero un curso, año y período.';
    header('Location: ' . BASE_URL . '/pages/cursos.php');
    exit;
}
$ctx = $_SESSION['contexto'];

// Verificar que el curso pertenece al docente
if (!Curso::perteneceADocente($ctx['cod_cur'], $doc['cod_doc'])) {
    $_SESSION['flash_error'] = 'No tiene permisos sobre este curso.';
    header('Location: ' . BASE_URL . '/pages/cursos.php');
    exit;
}

// Acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'inscribir') {
            $cod_est = trim($_POST['cod_est'] ?? '');
            if ($cod_est === '') {
                throw new Exception('Seleccione un estudiante.');
            }
            if (Inscripcion::existe($ctx['cod_cur'], $cod_est, $ctx['year'], $ctx['periodo'])) {
                throw new Exception('El estudiante ya está inscrito.');
            }
            Inscripcion::inscribir($ctx['cod_cur'], $cod_est, $ctx['year'], $ctx['periodo']);
            $_SESSION['flash_success'] = 'Estudiante inscrito correctamente.';
        } elseif ($accion === 'eliminar') {
            $cod_est = trim($_POST['cod_est'] ?? '');
            Inscripcion::eliminar($ctx['cod_cur'], $cod_est, $ctx['year'], $ctx['periodo']);
            $_SESSION['flash_success'] = 'Estudiante eliminado de la inscripción (y sus calificaciones).';
        }
    } catch (Throwable $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
    header('Location: ' . BASE_URL . '/pages/inscripciones.php');
    exit;
}

$inscritos    = Inscripcion::listarPorCurso($ctx['cod_cur'], $ctx['year'], $ctx['periodo']);
$disponibles  = Inscripcion::listarNoInscritos($ctx['cod_cur'], $ctx['year'], $ctx['periodo']);

$page_title  = 'Inscripción de Estudiantes';
$subbar_text = sprintf('CURSO: %s — %d / Período %s',
    $ctx['nomb_cur'], $ctx['year'], $ctx['periodo'] === 1 ? 'I' : 'II');
require_once __DIR__ . '/../includes/layout_top.php';
?>

<div class="card">
    <div class="flex-between mb-16">
        <h3 class="card-title mb-0"><i class="bi bi-people-fill"></i> Estudiantes Inscritos</h3>
        <button class="btn btn-success" onclick="openModal('mdlInscribir')">
            <i class="bi bi-person-plus-fill"></i> Inscribir estudiante
        </button>
    </div>

    <?php if (empty($inscritos)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>No hay estudiantes inscritos en este curso aún.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">No.</th>
                        <th>Código</th>
                        <th>Nombres</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscritos as $i => $e): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($e['cod_est']) ?></strong></td>
                            <td><?= htmlspecialchars($e['nomb_est']) ?></td>
                            <td class="text-right">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="accion"  value="eliminar">
                                    <input type="hidden" name="cod_est" value="<?= htmlspecialchars($e['cod_est']) ?>">
                                    <button type="submit"
                                            class="btn btn-icon btn-danger"
                                            data-confirm="¿Eliminar este estudiante? Se borrarán también sus calificaciones."
                                            title="Eliminar inscripción">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Modal inscribir -->
<div id="mdlInscribir" class="modal-overlay">
    <div class="modal">
        <h3><i class="bi bi-person-plus-fill"></i> Inscribir estudiante</h3>
        <form method="POST">
            <input type="hidden" name="accion" value="inscribir">
            <div class="form-group">
                <label for="cod_est">Estudiante</label>
                <select id="cod_est" name="cod_est" class="form-control" required>
                    <option value="">— Seleccione —</option>
                    <?php foreach ($disponibles as $e): ?>
                        <option value="<?= htmlspecialchars($e['cod_est']) ?>">
                            <?= htmlspecialchars($e['cod_est']) ?> — <?= htmlspecialchars($e['nomb_est']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($disponibles)): ?>
                    <p class="text-muted" style="font-size:12px; margin-top:6px;">
                        No quedan estudiantes por inscribir en este curso/período.
                    </p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('mdlInscribir')">Cancelar</button>
                <button type="submit" class="btn btn-success" <?= empty($disponibles) ? 'disabled' : '' ?>>
                    <i class="bi bi-check-lg"></i> Inscribir
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
