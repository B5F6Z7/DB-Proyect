<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Curso.php';
require_once __DIR__ . '/../models/Calificacion.php';

requireLogin();
$doc = getDocente();

if (empty($_SESSION['contexto']['cod_cur'])) {
    http_response_code(400);
    exit('Contexto no seleccionado.');
}
$ctx = $_SESSION['contexto'];

if (!Curso::perteneceADocente($ctx['cod_cur'], $doc['cod_doc'])) {
    http_response_code(403);
    exit('Sin permisos.');
}

$rep = Calificacion::reporte($ctx['cod_cur'], $ctx['year'], $ctx['periodo']);

// ============================================================
// Generar el reporte como HTML imprimible.
// Al abrir el PDF se dispara automáticamente window.print(),
// y el usuario puede "Guardar como PDF" desde el navegador.
// Esto evita dependencias externas (FPDF/TCPDF).
// ============================================================
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Notas - <?= htmlspecialchars($ctx['nomb_cur']) ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 24px;
            color: #1e293b;
        }
        h1 { color: #1e3a8a; margin: 0 0 4px; font-size: 22px; }
        .info { color: #64748b; margin-bottom: 24px; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: center; }
        th { background: #1e3a8a; color: white; }
        th.porcentaje { background: #fef3c7; color: #92400e; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .nombre { text-align: left; }
        .definitiva { background: #dbeafe; font-weight: 700; }
        .aprobado { color: #059669; }
        .reprobado { color: #dc2626; }
        .footer-info { margin-top: 16px; font-size: 11px; color: #64748b; }
        .no-print { margin-bottom: 16px; }
        button {
            background: #1e3a8a; color: white; border: none;
            padding: 10px 20px; border-radius: 6px; cursor: pointer;
            font-size: 14px;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Imprimir / Guardar como PDF</button>
        <button onclick="window.close()" style="background:#64748b;">Cerrar</button>
    </div>

    <h1>Reporte de Notas</h1>
    <div class="info">
        <strong>Curso:</strong> <?= htmlspecialchars($ctx['nomb_cur']) ?>
        (<?= htmlspecialchars($ctx['cod_cur']) ?>) &nbsp;|&nbsp;
        <strong>Año:</strong> <?= (int) $ctx['year'] ?> &nbsp;|&nbsp;
        <strong>Período:</strong> <?= $ctx['periodo'] === 1 ? 'I' : 'II' ?> &nbsp;|&nbsp;
        <strong>Docente:</strong> <?= htmlspecialchars($doc['nomb_doc']) ?> &nbsp;|&nbsp;
        <strong>Fecha:</strong> <?= date('d/m/Y') ?>
    </div>

    <?php if (empty($rep['cohortes']) || empty($rep['filas'])): ?>
        <p>No hay datos suficientes para generar el reporte.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th rowspan="2">CÓDIGO</th>
                    <th rowspan="2">NOMBRE</th>
                    <?php foreach ($rep['cohortes'] as $co): ?>
                        <th><?= htmlspecialchars($co['desc_nota']) ?></th>
                    <?php endforeach; ?>
                    <th rowspan="2" class="definitiva">DEFINITIVA</th>
                </tr>
                <tr>
                    <?php foreach ($rep['cohortes'] as $co): ?>
                        <th class="porcentaje"><?= number_format($co['porcentaje'], 0) ?>%</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rep['filas'] as $f): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($f['cod_est']) ?></strong></td>
                        <td class="nombre"><?= htmlspecialchars($f['nomb_est']) ?></td>
                        <?php foreach ($rep['cohortes'] as $co): ?>
                            <td><?= number_format($f['valores'][$co['nota']] ?? 0, 2) ?></td>
                        <?php endforeach; ?>
                        <td class="definitiva <?= $f['definitiva'] >= 3.0 ? 'aprobado' : 'reprobado' ?>">
                            <?= number_format($f['definitiva'], 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="footer-info">
            Total estudiantes: <?= count($rep['filas']) ?> &nbsp;|&nbsp;
            Aprobados: <?= count(array_filter($rep['filas'], fn($f) => $f['definitiva'] >= 3.0)) ?> &nbsp;|&nbsp;
            Reprobados: <?= count(array_filter($rep['filas'], fn($f) => $f['definitiva'] < 3.0)) ?>
        </div>
    <?php endif; ?>

    <script>
        // Auto-abrir el diálogo de impresión al cargar
        window.addEventListener('load', () => setTimeout(() => window.print(), 400));
    </script>
</body>
</html>
