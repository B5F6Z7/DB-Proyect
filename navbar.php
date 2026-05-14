<?php
$doc      = getDocente();
$current  = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="brand">
        <div class="brand-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
        <div class="brand-text">
            Registro de Notas
            <small>Gestión académica</small>
        </div>
    </div>

    <ul class="nav-menu">
        <li>
            <a href="<?= BASE_URL ?>/pages/dashboard.php"
               class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/pages/cursos.php"
               class="<?= $current === 'cursos.php' ? 'active' : '' ?>">
                <i class="bi bi-funnel-fill"></i> Seleccionar Curso
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/pages/inscripciones.php"
               class="<?= $current === 'inscripciones.php' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i> Inscripciones
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/pages/notas_parciales.php"
               class="<?= $current === 'notas_parciales.php' ? 'active' : '' ?>">
                <i class="bi bi-list-check"></i> Notas Parciales
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/pages/registro_notas.php"
               class="<?= $current === 'registro_notas.php' ? 'active' : '' ?>">
                <i class="bi bi-pencil-square"></i> Registro de Notas
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/pages/reporte.php"
               class="<?= $current === 'reporte.php' ? 'active' : '' ?>">
                <i class="bi bi-bar-chart-fill"></i> Reporte
            </a>
        </li>

        <li style="margin-top:12px; padding:8px 24px; font-size:11px;
                   text-transform:uppercase; letter-spacing:1px;
                   color:rgba(255,255,255,.4);">
            Administración
        </li>
        <li>
            <a href="<?= BASE_URL ?>/pages/estudiantes.php"
               class="<?= $current === 'estudiantes.php' ? 'active' : '' ?>">
                <i class="bi bi-person-lines-fill"></i> Estudiantes
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/pages/gestionar_cursos.php"
               class="<?= $current === 'gestionar_cursos.php' ? 'active' : '' ?>">
                <i class="bi bi-book-fill"></i> Mis Cursos (CRUD)
            </a>
        </li>
        <li>
            <a href="<?= BASE_URL ?>/pages/perfil.php"
               class="<?= $current === 'perfil.php' ? 'active' : '' ?>">
                <i class="bi bi-person-gear"></i> Mi Perfil
            </a>
        </li>
    </ul>

    <div class="user-card">
        <div class="name"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($doc['nomb_doc']) ?></div>
        <div class="code"><?= htmlspecialchars($doc['cod_doc']) ?></div>
        <a class="logout" href="<?= BASE_URL ?>/logout.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a>
    </div>
</aside>
