<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Curso.php';
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

$rep = Calificacion::reporte($ctx['cod_cur'], $ctx['year'], $ctx['periodo']);

$page_title  = 'Reporte de Notas';
$subbar_text = sprintf('CURSO: %s — %d / Período %s',
    $ctx['nomb_cur'], $ctx['year'], $ctx['periodo'] === 1 ? 'I' : 'II');
require_once __DIR__ . '/../includes/layout_top.php';
?>

<div class="card">
    <div class="flex-between mb-16">
        <h3 class="card-title mb-0"><i class="bi bi-bar-chart-fill"></i> Planilla de notas</h3>
        <div style="display:flex; gap:8px;">
            <a class="btn btn-secondary" href="<?= BASE_URL ?>/pages/cursos.php">
                <i class="bi bi-chevron-double-left"></i> Cambiar curso
            </a>
            <?php if (!empty($rep['cohortes']) && !empty($rep['filas'])): ?>
                <a class="btn btn-danger"
                   href="<?= BASE_URL ?>/pdf/generar_pdf.php"
                   target="_blank">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Generar PDF
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($rep['cohortes'])): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            No hay cohortes definidos para este curso.
        </div>
    <?php elseif (empty($rep['filas'])): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
            No hay estudiantes inscritos en este curso/período.
        </div>
    <?php else: ?>
        <div class="table-wrapper" style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" style="vertical-align:middle;">Código</th>
                        <th rowspan="2" style="vertical-align:middle;">Nombre</th>
                        <?php foreach ($rep['cohortes'] as $co): ?>
                            <th class="text-center"><?= htmlspecialchars($co['desc_nota']) ?></th>
                        <?php endforeach; ?>
                        <th rowspan="2" class="text-center" style="vertical-align:middle; background:#1e3a8a; color:white;">DEFINITIVA</th>
                    </tr>
                    <tr>
                        <?php foreach ($rep['cohortes'] as $co): ?>
                            <th class="text-center" style="background:#fef3c7; color:#92400e;">
                                <?= number_format($co['porcentaje'], 0) ?>%
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rep['filas'] as $f): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($f['cod_est']) ?></strong></td>
                            <td><?= htmlspecialchars($f['nomb_est']) ?></td>
                            <?php foreach ($rep['cohortes'] as $co): ?>
                                <td class="text-center">
                                    <?= number_format($f['valores'][$co['nota']] ?? 0, 2) ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="text-center <?= $f['definitiva'] >= 3.0 ? 'nota-aprobado' : 'nota-reprobado' ?>">
                                <?= number_format($f['definitiva'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-16 text-muted" style="font-size:12px;">
            <i class="bi bi-info-circle"></i>
            Nota definitiva = sumatoria(valor × porcentaje) / 100.
            En verde aprueban (≥ 3.0), en rojo reprueban.
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
