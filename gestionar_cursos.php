<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Curso.php';

requireLogin();
$doc = getDocente();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    try {
        if ($accion === 'crear') {
            $cod  = trim($_POST['cod_cur']  ?? '');
            $nomb = trim($_POST['nomb_cur'] ?? '');
            if ($cod === '' || $nomb === '') {
                throw new Exception('Código y nombre son obligatorios.');
            }
            if (Curso::obtener($cod)) {
                throw new Exception("Ya existe un curso con código $cod.");
            }
            Curso::crear($cod, $nomb, $doc['cod_doc']);
            $_SESSION['flash_success'] = "Curso $cod creado correctamente.";
        }
        elseif ($accion === 'actualizar') {
            $cod  = trim($_POST['cod_cur']  ?? '');
            $nomb = trim($_POST['nomb_cur'] ?? '');
            if (!Curso::perteneceADocente($cod, $doc['cod_doc'])) {
                throw new Exception('No puede modificar cursos de otros docentes.');
            }
            Curso::actualizar($cod, $nomb);
            $_SESSION['flash_success'] = 'Curso actualizado.';
        }
        elseif ($accion === 'eliminar') {
            $cod = trim($_POST['cod_cur'] ?? '');
            if (!Curso::perteneceADocente($cod, $doc['cod_doc'])) {
                throw new Exception('No puede eliminar cursos de otros docentes.');
            }
            Curso::eliminar($cod);
            $_SESSION['flash_success'] = 'Curso eliminado (inscripciones, notas y calificaciones también).';
        }
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = 'Error de base de datos: ' . $e->getMessage();
    } catch (Throwable $e) {
        $_SESSION['flash_error'] = $e->getMessage();
    }
    header('Location: ' . BASE_URL . '/pages/gestionar_cursos.php');
    exit;
}

$cursos = Curso::listarPorDocente($doc['cod_doc']);
$page_title  = 'Gestión de Cursos';
$subbar_text = 'ADMINISTRACIÓN DE CURSOS DEL DOCENTE';
require_once __DIR__ . '/../includes/layout_top.php';
?>

<div class="card">
    <div class="flex-between mb-16">
        <h3 class="card-title mb-0">
            <i class="bi bi-book-fill"></i> Mis cursos
            <span class="badge badge-primary"><?= count($cursos) ?></span>
        </h3>
        <button class="btn btn-success" onclick="openModal('mdlCrearCur')">
            <i class="bi bi-plus-circle-fill"></i> Nuevo curso
        </button>
    </div>

    <?php if (empty($cursos)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>No tiene cursos creados. Cree el primero con el botón "Nuevo curso".</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">No.</th>
                        <th>Código</th>
                        <th>Nombre del curso</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos as $i => $c): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($c['cod_cur']) ?></strong></td>
                            <td><?= htmlspecialchars($c['nomb_cur']) ?></td>
                            <td class="text-right">
                                <div class="table-actions" style="justify-content:flex-end;">
                                    <button class="btn btn-icon btn-primary"
                                            onclick='openEditCur(<?= json_encode($c, JSON_HEX_APOS|JSON_HEX_QUOT) ?>)'
                                            title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="accion"  value="eliminar">
                                        <input type="hidden" name="cod_cur" value="<?= htmlspecialchars($c['cod_cur']) ?>">
                                        <button type="submit"
                                                class="btn btn-icon btn-danger"
                                                data-confirm="¿Eliminar este curso? Se borrarán inscripciones, notas y calificaciones asociadas."
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

<!-- Modal crear curso -->
<div id="mdlCrearCur" class="modal-overlay">
    <div class="modal">
        <h3><i class="bi bi-plus-circle-fill"></i> Nuevo curso</h3>
        <form method="POST">
            <input type="hidden" name="accion" value="crear">
            <div class="form-group">
                <label for="cc_cod">Código del curso</label>
                <input id="cc_cod" type="text" name="cod_cur" class="form-control"
                       maxlength="20" placeholder="Ej: MAT505" required>
            </div>
            <div class="form-group">
                <label for="cc_nomb">Nombre del curso</label>
                <input id="cc_nomb" type="text" name="nomb_cur" class="form-control"
                       maxlength="100" placeholder="Ej: Cálculo Diferencial" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('mdlCrearCur')">Cancelar</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Crear</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal editar curso -->
<div id="mdlEditCur" class="modal-overlay">
    <div class="modal">
        <h3><i class="bi bi-pencil-fill"></i> Editar curso</h3>
        <form method="POST">
            <input type="hidden" name="accion" value="actualizar">
            <div class="form-group">
                <label for="ec_cod">Código (no editable)</label>
                <input id="ec_cod" type="text" name="cod_cur" class="form-control" readonly style="background:#f1f5f9;">
            </div>
            <div class="form-group">
                <label for="ec_nomb">Nombre del curso</label>
                <input id="ec_nomb" type="text" name="nomb_cur" class="form-control" maxlength="100" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('mdlEditCur')">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCur(c) {
    document.getElementById('ec_cod').value  = c.cod_cur;
    document.getElementById('ec_nomb').value = c.nomb_cur;
    openModal('mdlEditCur');
}
</script>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
