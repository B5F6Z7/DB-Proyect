-- ============================================================
-- DATOS DE PRUEBA — Notas Parciales de Docentes
-- Contraseñas: almacenadas como MD5 para pruebas iniciales.
-- En producción el hash lo genera PHP con password_hash().
-- Clave de todos los docentes de prueba: "123456"
-- MD5('123456') = e10adc3949ba59abbe56e057f20f883e
-- ============================================================

-- ============================================================
-- DOCENTES
-- ============================================================
INSERT INTO docentes (cod_doc, nomb_doc, clave) VALUES
    ('DOC001', 'Jesús Reyes Carvajal',    md5('123456')),
    ('DOC002', 'María López Torres',       md5('123456')),
    ('DOC003', 'Carlos Pérez Mora',        md5('123456'));

-- ============================================================
-- ESTUDIANTES
-- ============================================================
INSERT INTO estudiantes (cod_est, nomb_est) VALUES
    ('6100021', 'Andrés Felipe Martínez'),
    ('6100022', 'Laura Valentina Gómez'),
    ('6100023', 'Juan Carlos Gomez'),
    ('6100024', 'Pilar Marquez'),
    ('6100025', 'Diego Alejandro Ríos'),
    ('6100026', 'Sofía Isabela Herrera'),
    ('6100027', 'Miguel Ángel Castro'),
    ('6100028', 'Daniela Fernández');

-- ============================================================
-- CURSOS
-- ============================================================
INSERT INTO cursos (cod_cur, nomb_cur, cod_doc) VALUES
    ('BD101',  'Base de Datos',              'DOC001'),
    ('POO202', 'Programación Orientada a Objetos', 'DOC001'),
    ('ALG303', 'Algoritmos',                 'DOC002'),
    ('MAT404', 'Matemáticas Discretas',      'DOC003');

-- ============================================================
-- INSCRIPCIONES (año 2026, periodo 1)
-- ============================================================
INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) VALUES
    -- Base de Datos 2026-1
    ('BD101',  '6100021', 2026, 1),
    ('BD101',  '6100022', 2026, 1),
    ('BD101',  '6100023', 2026, 1),
    ('BD101',  '6100024', 2026, 1),
    ('BD101',  '6100025', 2026, 1),
    -- POO 2026-1
    ('POO202', '6100021', 2026, 1),
    ('POO202', '6100026', 2026, 1),
    ('POO202', '6100027', 2026, 1),
    -- Algoritmos 2026-1
    ('ALG303', '6100022', 2026, 1),
    ('ALG303', '6100028', 2026, 1),
    -- Matemáticas 2025-2 (periodo anterior)
    ('MAT404', '6100023', 2025, 2),
    ('MAT404', '6100024', 2025, 2);

-- ============================================================
-- NOTAS / COHORTES (porcentajes deben sumar <= 100 por curso)
-- ============================================================
INSERT INTO notas (desc_nota, porcentaje, posicion, cod_cur) VALUES
    -- Base de Datos: 30 + 30 + 40 = 100%
    ('Parcial uno',   30.00, 1, 'BD101'),
    ('Parcial dos',   30.00, 2, 'BD101'),
    ('Examen final',  40.00, 3, 'BD101'),
    -- POO: 25 + 25 + 50 = 100%
    ('Taller 1',      25.00, 1, 'POO202'),
    ('Taller 2',      25.00, 2, 'POO202'),
    ('Proyecto final',50.00, 3, 'POO202'),
    -- Algoritmos: 40 + 60 = 100%
    ('Parcial',       40.00, 1, 'ALG303'),
    ('Final',         60.00, 2, 'ALG303'),
    -- Matemáticas: 33 + 33 + 34 = 100%
    ('Corte 1',       33.00, 1, 'MAT404'),
    ('Corte 2',       33.00, 2, 'MAT404'),
    ('Corte 3',       34.00, 3, 'MAT404');

-- ============================================================
-- CALIFICACIONES (algunas notas de prueba)
-- Las notas.nota IDs son asignados por SERIAL:
--   1=Parcial uno BD101, 2=Parcial dos BD101, 3=Examen final BD101
--   4=Taller1 POO, 5=Taller2 POO, 6=Proyecto POO
--   7=Parcial ALG, 8=Final ALG
--   9=Corte1 MAT, 10=Corte2 MAT, 11=Corte3 MAT
-- ============================================================
INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo) VALUES
    -- BD101 - Parcial uno (nota=1)
    (1, 4.30, '2026-03-10', 'BD101', '6100021', 2026, 1),
    (1, 3.80, '2026-03-10', 'BD101', '6100022', 2026, 1),
    (1, 4.30, '2026-03-10', 'BD101', '6100023', 2026, 1),
    (1, 3.20, '2026-03-10', 'BD101', '6100024', 2026, 1),
    (1, 2.90, '2026-03-10', 'BD101', '6100025', 2026, 1),
    -- BD101 - Parcial dos (nota=2)
    (2, 3.50, '2026-04-14', 'BD101', '6100021', 2026, 1),
    (2, 4.10, '2026-04-14', 'BD101', '6100022', 2026, 1),
    (2, 2.80, '2026-04-14', 'BD101', '6100023', 2026, 1),
    -- BD101 - Examen final (nota=3) — aún sin registrar para mostrar 0 en reporte
    -- POO - Taller 1 (nota=4)
    (4, 4.80, '2026-03-05', 'POO202', '6100021', 2026, 1),
    (4, 3.60, '2026-03-05', 'POO202', '6100026', 2026, 1),
    -- MAT - Corte 1 (nota=9)
    (9, 3.10, '2025-09-20', 'MAT404', '6100023', 2025, 2),
    (9, 4.50, '2025-09-20', 'MAT404', '6100024', 2025, 2);
