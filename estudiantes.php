<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Estudiante.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'crear') {
            $cod  = trim($_POST['cod_est']  ?? '');
            $nomb = trim($_POST['nomb_est'] ?? '');
            if ($cod === '' || $nomb === '') {
                throw new Exception('Código y nombre son obligatorios.');
            }
            if (Estudiante::obtener($cod)) {
                throw new Exception("Ya existe un estudiante con código $cod.");
            }
            Estudiante::crear($cod, $nomb);
            $_SESSION['flash_success'] = "Estudiante $cod creado correctamente.";
        }
        elseif ($accion === 'actualizar') {
            $cod  = trim($_POST['cod_est']  ?? '');
            $nomb = trim($_POST['nomb_est'] ?? '');
            if ($cod === '' || $nomb === '') {
                throw new Exception('Código y nombre son obligatorios.');
            }
            Estudiante::actualizar($cod, $nomb);
            $_SESSION['flash_success'] = 'Estudiante actualizado.';
        }
        elseif ($accion === 'eliminar') {
            $cod = trim($_POST['cod_est'] ?? '');
            Estudiante::eliminar($cod);
            $_SESSION['flash_success'] = 'Estudiante eliminado (junto con sus inscripciones y notas).';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Error de base de datos: ' . $e->getMessage();
    } catch (Throwable $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
    header('Location: ' . BASE_URL . '/pages/estudiantes.php');
    exit;
}

$estudiantes = Estudiante::listarTodos();
$page_title  = 'Gestión de Estudiantes';
$subbar_text = 'ADMINISTRACIÓN DE ESTUDIANTES';
require_once __DIR__ . '/../includes/layout_top.php';
?>

<div class="card">
    <div class="flex-between mb-16">
        <h3 class="card-title mb-0">
            <i class="bi bi-people-fill"></i> Estudiantes registrados
            <span class="badge badge-primary"><?= count($estudiantes) ?></span>
        </h3>
        <button class="btn btn-success" onclick="openModal('mdlCrearEst')">
            <i class="bi bi-person-plus-fill"></i> Nuevo estudiante
        </button>
    </div>

    <?php if (empty($estudiantes)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>No hay estudiantes registrados.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">No.</th>
                        <th>Código</th>
                        <th>Nombre completo</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estudiantes as $i => $e): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($e['cod_est']) ?></strong></td>
                            <td><?= htmlspecialchars($e['nomb_est']) ?></td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <button class="btn btn-icon btn-primary"
                                            onclick='openEditEst(<?= json_encode($e, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'
                                            title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accion"  value="eliminar">
                                        <input type="hidden" name="cod_est" value="<?= htmlspecialchars($e['cod_est']) ?>">
                                        <button type="submit"
                                                class="btn btn-icon btn-danger"
                                                data-confirm="¿Eliminar este estudiante? Se borrarán también sus inscripciones y notas."
                                                title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Modal crear -->
<div id="mdlCrearEst" class="modal-overlay">
    <div class="modal">
        <h3><i class="bi bi-person-plus-fill"></i> Nuevo estudiante</h3>
        <form method="POST">
            <input type="hidden" name="accion" value="crear">
            <div class="form-group">
                <label for="c_cod">Código</label>
                <input id="c_cod" type="text" name="cod_est"
                       class="form-control" maxlength="20"
                       placeholder="Ej: 6100029" required>
            </div>
            <div class="form-group">
                <label for="c_nomb">Nombre completo</label>
                <input id="c_nomb" type="text" name="nomb_est"
                       class="form-control" maxlength="100"
                       placeholder="Ej: Juan Pérez García" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('mdlCrearEst')">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Crear</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal editar -->
<div id="mdlEditEst" class="modal-overlay">
    <div class="modal">
        <h3><i class="bi bi-pencil-fill"></i> Editar estudiante</h3>
        <form method="POST">
            <input type="hidden" name="accion" value="actualizar">
            <div class="form-group">
                <label for="e_cod">Código (no editable)</label>
                <input id="e_cod" type="text" name="cod_est" class="form-control" readonly style="background:#f1f5f9;">
            </div>
            <div class="form-group">
                <label for="e_nomb">Nombre completo</label>
                <input id="e_nomb" type="text" name="nomb_est"
                       class="form-control" maxlength="100" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('mdlEditEst')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditEst(e) {
    document.getElementById('e_cod').value  = e.cod_est;
    document.getElementById('e_nomb').value = e.nomb_est;
    openModal('mdlEditEst');
}
</script>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
