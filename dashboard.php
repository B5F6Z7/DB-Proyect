<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Inscripcion.php';
require_once __DIR__ . '/../models/Nota.php';

requireLogin();
$doc = getDocente();

$cursos      = Curso::listarPorDocente($doc['cod_doc']);
$total_cur   = count($cursos);

// Contar estudiantes y cohortes totales del docente
$db = Database::connect();
$stmt = $db->prepare(
    'SELECT COUNT(DISTINCT i.cod_est)
       FROM inscripciones i
       JOIN cursos c ON c.cod_cur = i.cod_cur
      WHERE c.cod_doc = :d'
);
$stmt->execute([':d' => $doc['cod_doc']]);
$total_est = (int) $stmt->fetchColumn();

$stmt = $db->prepare(
    'SELECT COUNT(*) FROM notas n
       JOIN cursos c ON c.cod_cur = n.cod_cur
      WHERE c.cod_doc = :d'
);
$stmt->execute([':d' => $doc['cod_doc']]);
$total_notas = (int) $stmt->fetchColumn();

$stmt = $db->prepare(
    'SELECT COUNT(*) FROM calificaciones ca
       JOIN cursos c ON c.cod_cur = ca.cod_cur
      WHERE c.cod_doc = :d'
);
$stmt->execute([':d' => $doc['cod_doc']]);
$total_calif = (int) $stmt->fetchColumn();

$page_title = 'Dashboard';
$ctx_activo = $_SESSION['contexto'] ?? null;
require_once __DIR__ . '/../includes/layout_top.php';
?>

<?php if ($ctx_activo): ?>
    <div class="alert alert-info">
        <i class="bi bi-bookmark-check-fill"></i>
        Contexto activo:
        <strong><?= htmlspecialchars($ctx_activo['nomb_cur']) ?></strong>
        — <?= (int) $ctx_activo['year'] ?> /
        Período <?= $ctx_activo['periodo'] === 1 ? 'I' : 'II' ?>
        &nbsp;·&nbsp;
        <a href="<?= BASE_URL ?>/pages/cursos.php">Cambiar</a>
    </div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="bi bi-book-fill"></i></div>
        <div>
            <div class="stat-value"><?= $total_cur ?></div>
            <div class="stat-label">Cursos a cargo</div>
        </div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
        <div>
            <div class="stat-value"><?= $total_est ?></div>
            <div class="stat-label">Estudiantes inscritos</div>
        </div>
    </div>
    <div class="stat-card orange">
        <div class="stat-icon"><i class="bi bi-list-check"></i></div>
        <div>
            <div class="stat-value"><?= $total_notas ?></div>
            <div class="stat-label">Cohortes definidos</div>
        </div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon"><i class="bi bi-pencil-square"></i></div>
        <div>
            <div class="stat-value"><?= $total_calif ?></div>
            <div class="stat-label">Notas registradas</div>
        </div>
    </div>
</div>

<div class="card">
    <h3 class="card-title"><i class="bi bi-book"></i> Mis cursos</h3>
    <?php if (empty($cursos)): ?>
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>Aún no tiene cursos asignados.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre del curso</th>
                        <th class="text-right">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos as $c): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($c['cod_cur']) ?></strong></td>
                            <td><?= htmlspecialchars($c['nomb_cur']) ?></td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-primary"
                                   href="<?= BASE_URL ?>/pages/cursos.php?cod=<?= urlencode($c['cod_cur']) ?>">
                                   <i class="bi bi-arrow-right"></i> Seleccionar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
