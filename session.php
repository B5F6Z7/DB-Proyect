<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool {
    return isset($_SESSION['docente']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

function getDocente(): array {
    return $_SESSION['docente'] ?? [];
}

function setDocente(array $docente): void {
    $_SESSION['docente'] = $docente;
}

function logout(): void {
    session_destroy();
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}
