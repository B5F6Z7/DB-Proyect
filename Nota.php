<?php

require_once __DIR__ . '/../config/database.php';

class Nota {

    public static function listarPorCurso(string $cod_cur): array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT nota, desc_nota, porcentaje, posicion, cod_cur
               FROM notas
              WHERE cod_cur = :c
              ORDER BY posicion'
        );
        $stmt->execute([':c' => $cod_cur]);
        return $stmt->fetchAll();
    }

    public static function obtener(int $nota): ?array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT nota, desc_nota, porcentaje, posicion, cod_cur
               FROM notas WHERE nota = :n'
        );
        $stmt->execute([':n' => $nota]);
        return $stmt->fetch() ?: null;
    }

    public static function porcentajeAcumulado(string $cod_cur, ?int $excluir = null): float {
        $db = Database::connect();
        if ($excluir === null) {
            $stmt = $db->prepare(
                'SELECT COALESCE(SUM(porcentaje), 0)
                   FROM notas WHERE cod_cur = :c'
            );
            $stmt->execute([':c' => $cod_cur]);
        } else {
            $stmt = $db->prepare(
                'SELECT COALESCE(SUM(porcentaje), 0)
                   FROM notas WHERE cod_cur = :c AND nota <> :n'
            );
            $stmt->execute([':c' => $cod_cur, ':n' => $excluir]);
        }
        return (float) $stmt->fetchColumn();
    }

    public static function crear(string $desc, float $porcentaje, int $posicion, string $cod_cur): int {
        $db = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO notas (desc_nota, porcentaje, posicion, cod_cur)
             VALUES (:d, :p, :pos, :c)
             RETURNING nota'
        );
        $stmt->execute([':d' => $desc, ':p' => $porcentaje, ':pos' => $posicion, ':c' => $cod_cur]);
        return (int) $stmt->fetchColumn();
    }

    public static function actualizar(int $nota, string $desc, float $porcentaje, int $posicion): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'UPDATE notas
                SET desc_nota = :d, porcentaje = :p, posicion = :pos
              WHERE nota = :n'
        );
        return $stmt->execute([':d' => $desc, ':p' => $porcentaje, ':pos' => $posicion, ':n' => $nota]);
    }

    public static function eliminar(int $nota): bool {
        $db = Database::connect();
        $stmt = $db->prepare('DELETE FROM notas WHERE nota = :n');
        return $stmt->execute([':n' => $nota]);
    }

    public static function siguientePosicion(string $cod_cur): int {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT COALESCE(MAX(posicion), 0) + 1 FROM notas WHERE cod_cur = :c'
        );
        $stmt->execute([':c' => $cod_cur]);
        return (int) $stmt->fetchColumn();
    }
}
