<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Nota.php';

requireLogin();
$doc = getDocente();

if (empty($_SESSION['contexto']['cod_cur'])) {
    $_SESSION['flash_error'] = 'Debe seleccionar primero un curso.';
    header('Location: ' . BASE_URL . '/pages/cursos.php');
    exit;
}
$ctx = $_SESSION['contexto'];

if (!Curso::perteneceADocente($ctx['cod_cur'], $doc['cod_doc'])) {
    $_SESSION['flash_error'] = 'No tiene permisos sobre este curso.';
    header('Location: ' . BASE_URL . '/pages/cursos.php');
    exit;
}

// Acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'crear') {
            $desc = trim($_POST['desc_nota'] ?? '');
            $pct  = (float) ($_POST['porcentaje'] ?? 0);
            $pos  = (int)   ($_POST['posicion']   ?? 0);

            if ($desc === '')                throw new Exception('Descripción requerida.');
            if ($pct <= 0 || $pct > 100)     throw new Exception('Porcentaje debe estar entre 0 y 100.');
            if ($pos <= 0)                   throw new Exception('Posición debe ser mayor a 0.');

            Nota::crear($desc, $pct, $pos, $ctx['cod_cur']);
            $_SESSION['flash_success'] = 'Nota creada correctamente.';
        }
        elseif ($accion === 'actualizar') {
            $id   = (int)   ($_POST['nota']       ?? 0);
            $desc = trim($_POST['desc_nota'] ?? '');
            $pct  = (float) ($_POST['porcentaje'] ?? 0);
            $pos  = (int)   ($_POST['posicion']   ?? 0);

            if ($desc === '')                throw new Exception('Descripción requerida.');
            if ($pct <= 0 || $pct > 100)     throw new Exception('Porcentaje debe estar entre 0 y 100.');
            if ($pos <= 0)                   throw new Exception('Posición debe ser mayor a 0.');

            $existe = Nota::obtener($id);
            if (!$existe || $existe['cod_cur'] !== $ctx['cod_cur']) {
                throw new Exception('Nota no encontrada.');
            }
            Nota::actualizar($id, $desc, $pct, $pos);
            $_SESSION['flash_success'] = 'Nota actualizada correctamente.';
        }
        elseif ($accion === 'eliminar') {
            $id = (int) ($_POST['nota'] ?? 0);
            $existe = Nota::obtener($id);
            if ($existe && $existe['cod_cur'] === $ctx['cod_cur']) {
                Nota::eliminar($id);
                $_SESSION['flash_success'] = 'Nota eliminada (y sus calificaciones asociadas).';
            }
        }
    } catch (Throwable $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
    header('Location: ' . BASE_URL . '/pages/notas_parciales.php');
    exit;
}

$notas         = Nota::listarPorCurso($ctx['cod_cur']);
$acumulado     = Nota::porcentajeAcumulado($ctx['cod_cur']);
$siguiente_pos = Nota::siguientePosicion($ctx['cod_cur']);

$page_title  = 'Notas Parciales por Curso';
$subbar_text = sprintf('CURSO: %s', $ctx['nomb_cur']);
require_once __DIR__ . '/../includes/layout_top.php';
?>

<div class="card">
    <div class="flex-between mb-16">
        <h3 class="card-title mb-0"><i class="bi bi-list-check"></i> Crear notas de curso</h3>
        <button class="btn btn-success" onclick="openModal('mdlCrear')">
            <i class="bi bi-plus-circle-fill"></i> Nota
        </button>
    </div>

    <?php if ($acumulado > 0): ?>
        <div class="alert <?= $acumulado >= 100 ? 'alert-success' : 'alert-info' ?>">
            <i class="bi bi-pie-chart-fill"></i>
            Porcentaje acumulado del curso: <strong><?= number_format($acumulado, 2) ?>%</strong>
            <?php if ($acumulado < 100): ?>
                — falta definir <strong><?= number_format(100 - $acumulado, 2) ?>%</strong>.
            <?php else: ?>
                — el curso cubre el 100%.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($notas)): ?>
        <div class="empty-state">
            <i class="bi bi-clipboard-x"></i>
            <p>Este curso aún no tiene notas/cohortes definidos.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Posición</th>
                        <th>Descripción</th>
                        <th>Porcentaje</th>
                        <th class="text-right">Editar</th>
                        <th class="text-right">Borrar</th>
                        <th class="text-right">Registrar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notas as $n): ?>
                        <tr>
                            <td><span class="badge badge-primary"><?= $n['posicion'] ?></span></td>
                            <td><?= htmlspecialchars($n['desc_nota']) ?></td>
                            <td><strong><?= number_format($n['porcentaje'], 2) ?>%</strong></td>
                            <td class="text-right">
                                <button class="btn btn-icon btn-primary"
                                        onclick='openEditar(<?= json_encode($n, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'
                                        title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                            </td>
                            <td class="text-right">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="nota"   value="<?= $n['nota'] ?>">
                                    <button type="submit" class="btn btn-icon btn-danger"
                                            data-confirm="¿Eliminar esta nota? Se borrarán las calificaciones asociadas."
                                            title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="text-right">
                                <a class="btn btn-icon btn-success"
                                   href="<?= BASE_URL ?>/pages/registro_notas.php?nota=<?= $n['nota'] ?>"
                                   title="Registrar valores">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="alert alert-warning mt-16">
        <i class="bi bi-info-circle-fill"></i>
        Aquí puede adicionar, actualizar o eliminar la descripción de las notas de un curso.
        La posición indica el orden de aparición en la planilla.
    </div>
</div>

<!-- Modal crear -->
<div id="mdlCrear" class="modal-overlay">
    <div class="modal">
        <h3><i class="bi bi-plus-circle-fill"></i> Nueva nota / cohorte</h3>
        <form method="POST">
            <input type="hidden" name="accion" value="crear">
            <div class="form-group">
                <label for="c_desc">Descripción</label>
                <input id="c_desc" type="text" name="desc_nota" class="form-control"
                       placeholder="Ej: Parcial uno" maxlength="100" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="c_pct">Porcentaje (%)</label>
                    <input id="c_pct" type="number" name="porcentaje"
                           class="form-control" min="0.01" max="100" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="c_pos">Posición</label>
                    <input id="c_pos" type="number" name="posicion"
                           class="form-control" min="1" value="<?= $siguiente_pos ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('mdlCrear')">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal editar -->
<div id="mdlEditar" class="modal-overlay">
    <div class="modal">
        <h3><i class="bi bi-pencil-fill"></i> Editar nota</h3>
        <form method="POST">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" name="nota" id="e_id">
            <div class="form-group">
                <label for="e_desc">Descripción</label>
                <input id="e_desc" type="text" name="desc_nota" class="form-control" maxlength="100" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="e_pct">Porcentaje (%)</label>
                    <input id="e_pct" type="number" name="porcentaje"
                           class="form-control" min="0.01" max="100" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="e_pos">Posición</label>
                    <input id="e_pos" type="number" name="posicion"
                           class="form-control" min="1" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('mdlEditar')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditar(n) {
    document.getElementById('e_id').value   = n.nota;
    document.getElementById('e_desc').value = n.desc_nota;
    document.getElementById('e_pct').value  = n.porcentaje;
    document.getElementById('e_pos').value  = n.posicion;
    openModal('mdlEditar');
}
</script>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
