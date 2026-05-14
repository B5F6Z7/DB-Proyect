<?php

require_once __DIR__ . '/../config/database.php';

class Estudiante {

    public static function listarTodos(): array {
        $db = Database::connect();
        return $db->query(
            'SELECT cod_est, nomb_est FROM estudiantes ORDER BY nomb_est'
        )->fetchAll();
    }

    public static function obtener(string $cod_est): ?array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT cod_est, nomb_est FROM estudiantes WHERE cod_est = :c'
        );
        $stmt->execute([':c' => $cod_est]);
        return $stmt->fetch() ?: null;
    }

    public static function buscar(string $termino): array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT cod_est, nomb_est
               FROM estudiantes
              WHERE cod_est ILIKE :t OR nomb_est ILIKE :t
              ORDER BY nomb_est
              LIMIT 50'
        );
        $stmt->execute([':t' => "%$termino%"]);
        return $stmt->fetchAll();
    }

    public static function crear(string $cod_est, string $nomb_est): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO estudiantes (cod_est, nomb_est) VALUES (:c, :n)'
        );
        return $stmt->execute([':c' => $cod_est, ':n' => $nomb_est]);
    }

    public static function actualizar(string $cod_est, string $nomb_est): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'UPDATE estudiantes SET nomb_est = :n WHERE cod_est = :c'
        );
        return $stmt->execute([':n' => $nomb_est, ':c' => $cod_est]);
    }

    public static function eliminar(string $cod_est): bool {
        $db = Database::connect();
        $stmt = $db->prepare('DELETE FROM estudiantes WHERE cod_est = :c');
        return $stmt->execute([':c' => $cod_est]);
    }
}
