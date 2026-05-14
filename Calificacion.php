<?php

require_once __DIR__ . '/../config/database.php';

class Calificacion {

    public static function obtener(int $nota, string $cod_est, int $year, int $periodo): ?array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT cod_cal, nota, valor, fecha, cod_cur, cod_est, year, periodo
               FROM calificaciones
              WHERE nota = :n AND cod_est = :e AND year = :y AND periodo = :p'
        );
        $stmt->execute([':n' => $nota, ':e' => $cod_est, ':y' => $year, ':p' => $periodo]);
        return $stmt->fetch() ?: null;
    }

    public static function listarPorNota(int $nota, int $year, int $periodo): array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT c.cod_cal, c.cod_est, e.nomb_est, c.valor, c.fecha
               FROM calificaciones c
               JOIN estudiantes    e ON e.cod_est = c.cod_est
              WHERE c.nota = :n AND c.year = :y AND c.periodo = :p
              ORDER BY e.nomb_est'
        );
        $stmt->execute([':n' => $nota, ':y' => $year, ':p' => $periodo]);
        return $stmt->fetchAll();
    }

    /**
     * Guarda o actualiza el valor de un cohorte para un estudiante (UPSERT).
     */
    public static function guardar(int $nota, float $valor, string $cod_cur,
                                    string $cod_est, int $year, int $periodo): bool {
        $db = Database::connect();
        $stmt = $db->prepare(
            'INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo)
             VALUES (:n, :v, CURRENT_DATE, :c, :e, :y, :p)
             ON CONFLICT (nota, cod_est, year, periodo)
             DO UPDATE SET valor = EXCLUDED.valor, fecha = CURRENT_DATE'
        );
        return $stmt->execute([
            ':n' => $nota, ':v' => $valor, ':c' => $cod_cur,
            ':e' => $cod_est, ':y' => $year, ':p' => $periodo
        ]);
    }

    public static function eliminar(int $cod_cal): bool {
        $db = Database::connect();
        $stmt = $db->prepare('DELETE FROM calificaciones WHERE cod_cal = :c');
        return $stmt->execute([':c' => $cod_cal]);
    }

    /**
     * Reporte detallado: por cada estudiante inscrito, sus notas por cohorte
     * y la nota definitiva (suma ponderada).
     *
     * Devuelve estructura:
     *   [
     *     'cohortes' => [ {nota, desc_nota, porcentaje, posicion}, ... ],
     *     'filas'    => [ {cod_est, nomb_est, valores:{nota=>valor}, definitiva}, ... ]
     *   ]
     */
    public static function reporte(string $cod_cur, int $year, int $periodo): array {
        $db = Database::connect();

        // 1. Cohortes del curso
        $stmt = $db->prepare(
            'SELECT nota, desc_nota, porcentaje, posicion
               FROM notas
              WHERE cod_cur = :c
              ORDER BY posicion'
        );
        $stmt->execute([':c' => $cod_cur]);
        $cohortes = $stmt->fetchAll();

        // 2. Estudiantes inscritos
        $stmt = $db->prepare(
            'SELECT e.cod_est, e.nomb_est
               FROM inscripciones i
               JOIN estudiantes  e ON e.cod_est = i.cod_est
              WHERE i.cod_cur = :c AND i.year = :y AND i.periodo = :p
              ORDER BY e.nomb_est'
        );
        $stmt->execute([':c' => $cod_cur, ':y' => $year, ':p' => $periodo]);
        $estudiantes = $stmt->fetchAll();

        // 3. Calificaciones existentes
        $stmt = $db->prepare(
            'SELECT nota, cod_est, valor
               FROM calificaciones
              WHERE cod_cur = :c AND year = :y AND periodo = :p'
        );
        $stmt->execute([':c' => $cod_cur, ':y' => $year, ':p' => $periodo]);
        $califs = $stmt->fetchAll();

        // Indexar calificaciones por estudiante y nota
        $mapa = [];
        foreach ($califs as $cf) {
            $mapa[$cf['cod_est']][$cf['nota']] = (float) $cf['valor'];
        }

        // Armar filas con definitiva calculada
        $filas = [];
        foreach ($estudiantes as $e) {
            $valores    = [];
            $definitiva = 0.0;
            foreach ($cohortes as $co) {
                $v = $mapa[$e['cod_est']][$co['nota']] ?? 0.0;
                $valores[$co['nota']] = $v;
                $definitiva += $v * ((float) $co['porcentaje'] / 100);
            }
            $filas[] = [
                'cod_est'    => $e['cod_est'],
                'nomb_est'   => $e['nomb_est'],
                'valores'    => $valores,
                'definitiva' => round($definitiva, 2),
            ];
        }

        return ['cohortes' => $cohortes, 'filas' => $filas];
    }
}
