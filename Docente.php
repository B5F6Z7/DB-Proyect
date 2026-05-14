<?php

require_once __DIR__ . '/../config/database.php';

class Docente {

    public static function autenticar(string $cod_doc, string $clave): ?array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT cod_doc, nomb_doc, clave
               FROM docentes
              WHERE cod_doc = :cod'
        );
        $stmt->execute([':cod' => $cod_doc]);
        $doc = $stmt->fetch();

        if (!$doc) return null;

        // Soporta claves antiguas en MD5 (seed) y nuevas con password_hash
        $ok = (strlen($doc['clave']) === 32 && ctype_xdigit($doc['clave']))
            ? hash_equals($doc['clave'], md5($clave))
            : password_verify($clave, $doc['clave']);

        if (!$ok) return null;

        unset($doc['clave']);
        return $doc;
    }

    public static function obtener(string $cod_doc): ?array {
        $db = Database::connect();
        $stmt = $db->prepare(
            'SELECT cod_doc, nomb_doc FROM docentes WHERE cod_doc = :c'
        );
        $stmt->execute([':c' => $cod_doc]);
        return $stmt->fetch() ?: null;
    }

    public static function cambiarClave(string $cod_doc, string $nueva): bool {
        $db = Database::connect();
        $hash = password_hash($nueva, PASSWORD_BCRYPT);
        $stmt = $db->prepare(
            'UPDATE docentes SET clave = :h WHERE cod_doc = :c'
        );
        return $stmt->execute([':h' => $hash, ':c' => $cod_doc]);
    }
}
