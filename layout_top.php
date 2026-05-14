<?php require_once __DIR__ . '/header.php'; ?>
<div class="app-layout">
    <?php require_once __DIR__ . '/navbar.php'; ?>
    <main class="main-content">
        <div class="topbar">
            <h2><?= htmlspecialchars($page_title ?? '') ?></h2>
            <div class="date">
                <?php
                    $meses = ['enero','febrero','marzo','abril','mayo','junio',
                              'julio','agosto','septiembre','octubre','noviembre','diciembre'];
                    echo date('d') . ' de ' . $meses[(int) date('n') - 1] . ' de ' . date('Y');
                ?>
            </div>
        </div>
        <?php if (!empty($subbar_text)): ?>
            <div class="subbar"><?= htmlspecialchars($subbar_text) ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_success'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
