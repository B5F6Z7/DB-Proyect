<?php

require_once __DIR__ . '/../config/database.php';

class Inscripcion {

    public static function listarPorCurso(string $cod_cur, int $year, int $periodo): array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT e.cod_est, e.nomb_est
               FROM inscripciones i
               JOIN estudiantes  e ON e.cod_est = i.cod_est
              WHERE i.cod_cur = :c
                AND i.year    = :y
                AND i.periodo = :p
              ORDER BY e.nomb_est'
        );
        $stmt->execute([':c' => $cod_cur, ':y' => $year, ':p' => $periodo]);
        return $stmt->fetchAll();
    }

    public static function listarNoInscritos(string $cod_cur, int $year, int $periodo): array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT cod_est, nomb_est
               FROM estudiantes
              WHERE cod_est NOT IN (
                    SELECT cod_est FROM inscripciones
                     WHERE cod_cur = :c AND year = :y AND periodo = :p
              )
              ORDER BY nomb_est'
        );
        $stmt->execute([':c' => $cod_cur, ':y' => $year, ':p' => $periodo]);
        return $stmt->fetchAll();
    }

    public static function inscribir(string $cod_cur, string $cod_est, int $year, int $periodo): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO inscripciones (cod_cur, cod_est, year, periodo)
             VALUES (:c, :e, :y, :p)'
        );
        return $stmt->execute([':c' => $cod_cur, ':e' => $cod_est, ':y' => $year, ':p' => $periodo]);
    }

    public static function eliminar(string $cod_cur, string $cod_est, int $year, int $periodo): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'DELETE FROM inscripciones
              WHERE cod_cur = :c AND cod_est = :e AND year = :y AND periodo = :p'
        );
        return $stmt->execute([':c' => $cod_cur, ':e' => $cod_est, ':y' => $year, ':p' => $periodo]);
    }

    public static function existe(string $cod_cur, string $cod_est, int $year, int $periodo): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT 1 FROM inscripciones
              WHERE cod_cur = :c AND cod_est = :e AND year = :y AND periodo = :p'
        );
        $stmt->execute([':c' => $cod_cur, ':e' => $cod_est, ':y' => $year, ':p' => $periodo]);
        return (bool) $stmt->fetchColumn();
    }

    public static function contarPorCurso(string $cod_cur, int $year, int $periodo): int {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT COUNT(*) FROM inscripciones
              WHERE cod_cur = :c AND year = :y AND periodo = :p'
        );
        $stmt->execute([':c' => $cod_cur, ':y' => $year, ':p' => $periodo]);
        return (int) $stmt->fetchColumn();
    }
}
