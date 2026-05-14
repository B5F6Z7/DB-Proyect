<?php

define('BASE_URL', '/notas');
define('APP_NAME', 'Registro de Notas');

// Mostrar errores solo en desarrollo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Zona horaria Colombia
date_default_timezone_set('America/Bogota');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';
