<?php

require_once __DIR__ . '/../config/database.php';

class Curso {

    public static function listarPorDocente(string $cod_doc): array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT cod_cur, nomb_cur
               FROM cursos
              WHERE cod_doc = :d
              ORDER BY nomb_cur'
        );
        $stmt->execute([':d' => $cod_doc]);
        return $stmt->fetchAll();
    }

    public static function obtener(string $cod_cur): ?array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT cod_cur, nomb_cur, cod_doc
               FROM cursos
              WHERE cod_cur = :c'
        );
        $stmt->execute([':c' => $cod_cur]);
        return $stmt->fetch() ?: null;
    }

    public static function perteneceADocente(string $cod_cur, string $cod_doc): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT 1 FROM cursos WHERE cod_cur = :c AND cod_doc = :d'
        );
        $stmt->execute([':c' => $cod_cur, ':d' => $cod_doc]);
        return (bool) $stmt->fetchColumn();
    }

    public static function crear(string $cod_cur, string $nomb_cur, string $cod_doc): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO cursos (cod_cur, nomb_cur, cod_doc)
             VALUES (:c, :n, :d)'
        );
        return $stmt->execute([':c' => $cod_cur, ':n' => $nomb_cur, ':d' => $cod_doc]);
    }

    public static function actualizar(string $cod_cur, string $nomb_cur): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'UPDATE cursos SET nomb_cur = :n WHERE cod_cur = :c'
        );
        return $stmt->execute([':n' => $nomb_cur, ':c' => $cod_cur]);
    }

    public static function eliminar(string $cod_cur): bool {
        $db = Database::connect();
        $stmt = $db->prepare('DELETE FROM cursos WHERE cod_cur = :c');
        return $stmt->execute([':c' => $cod_cur]);
    }
}
