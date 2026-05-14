-- ============================================================
-- Seed extendido — datos completos para demo de sustentación
-- ============================================================
-- Limpia y repuebla la BD con un dataset rico:
--   * 4 docentes
--   * 20 estudiantes
--   * 6 cursos
--   * Inscripciones en 2 periodos (2026-1 y 2025-2)
--   * Cohortes con porcentajes balanceados
--   * Calificaciones para varios estudiantes (algunas notas faltantes
--     para mostrar el cálculo de la definitiva con 0)
-- ============================================================

TRUNCATE calificaciones, inscripciones, notas, cursos, estudiantes, docentes RESTART IDENTITY CASCADE;

-- ============================================================
-- DOCENTES (clave = "123456" en MD5)
-- ============================================================
INSERT INTO docentes (cod_doc, nomb_doc, clave) VALUES
    ('DOC001', 'Jesús Reyes Carvajal',     md5('123456')),
    ('DOC002', 'María López Torres',       md5('123456')),
    ('DOC003', 'Carlos Pérez Mora',        md5('123456')),
    ('DOC004', 'Ana Lucía Restrepo',       md5('123456'));

-- ============================================================
-- ESTUDIANTES (20)
-- ============================================================
INSERT INTO estudiantes (cod_est, nomb_est) VALUES
    ('6100021', 'Andrés Felipe Martínez'),
    ('6100022', 'Laura Valentina Gómez'),
    ('6100023', 'Juan Carlos Gomez'),
    ('6100024', 'Pilar Marquez'),
    ('6100025', 'Diego Alejandro Ríos'),
    ('6100026', 'Sofía Isabela Herrera'),
    ('6100027', 'Miguel Ángel Castro'),
    ('6100028', 'Daniela Fernández Vargas'),
    ('6100029', 'Sebastián Ortiz Pineda'),
    ('6100030', 'Camila Andrea Quintero'),
    ('6100031', 'Mateo Suárez Bedoya'),
    ('6100032', 'Isabella Rojas Cárdenas'),
    ('6100033', 'Santiago Mejía Henao'),
    ('6100034', 'Mariana Salazar López'),
    ('6100035', 'Tomás Alejandro Villa'),
    ('6100036', 'Valeria Cardona Toro'),
    ('6100037', 'Nicolás Restrepo Arias'),
    ('6100038', 'Antonia García Pulido'),
    ('6100039', 'Emiliano Tobón Jaramillo'),
    ('6100040', 'Luciana Patiño Echeverri');

-- ============================================================
-- CURSOS (6)
-- ============================================================
INSERT INTO cursos (cod_cur, nomb_cur, cod_doc) VALUES
    ('BD101',  'Base de Datos',                       'DOC001'),
    ('POO202', 'Programación Orientada a Objetos',    'DOC001'),
    ('ALG303', 'Algoritmos y Estructuras de Datos',   'DOC002'),
    ('MAT404', 'Matemáticas Discretas',               'DOC003'),
    ('RED505', 'Redes de Computadores',               'DOC004'),
    ('SO606',  'Sistemas Operativos',                 'DOC002');

-- ============================================================
-- INSCRIPCIONES 2026-1 (período actual)
-- ============================================================

-- BD101 — 10 estudiantes
INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) VALUES
    ('BD101', '6100021', 2026, 1),('BD101', '6100022', 2026, 1),
    ('BD101', '6100023', 2026, 1),('BD101', '6100024', 2026, 1),
    ('BD101', '6100025', 2026, 1),('BD101', '6100026', 2026, 1),
    ('BD101', '6100029', 2026, 1),('BD101', '6100031', 2026, 1),
    ('BD101', '6100035', 2026, 1),('BD101', '6100038', 2026, 1);

-- POO202 — 8 estudiantes
INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) VALUES
    ('POO202', '6100021', 2026, 1),('POO202', '6100026', 2026, 1),
    ('POO202', '6100027', 2026, 1),('POO202', '6100030', 2026, 1),
    ('POO202', '6100032', 2026, 1),('POO202', '6100036', 2026, 1),
    ('POO202', '6100039', 2026, 1),('POO202', '6100040', 2026, 1);

-- ALG303 — 7 estudiantes
INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) VALUES
    ('ALG303', '6100022', 2026, 1),('ALG303', '6100028', 2026, 1),
    ('ALG303', '6100031', 2026, 1),('ALG303', '6100033', 2026, 1),
    ('ALG303', '6100034', 2026, 1),('ALG303', '6100037', 2026, 1),
    ('ALG303', '6100040', 2026, 1);

-- RED505 — 6 estudiantes
INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) VALUES
    ('RED505', '6100023', 2026, 1),('RED505', '6100025', 2026, 1),
    ('RED505', '6100029', 2026, 1),('RED505', '6100034', 2026, 1),
    ('RED505', '6100036', 2026, 1),('RED505', '6100039', 2026, 1);

-- SO606 — 5 estudiantes
INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) VALUES
    ('SO606', '6100024', 2026, 1),('SO606', '6100027', 2026, 1),
    ('SO606', '6100033', 2026, 1),('SO606', '6100035', 2026, 1),
    ('SO606', '6100038', 2026, 1);

-- ============================================================
-- INSCRIPCIONES 2025-2 (período anterior — para mostrar histórico)
-- ============================================================
INSERT INTO inscripciones (cod_cur, cod_est, year, periodo) VALUES
    ('MAT404', '6100023', 2025, 2),('MAT404', '6100024', 2025, 2),
    ('MAT404', '6100025', 2025, 2),('MAT404', '6100028', 2025, 2),
    ('BD101',  '6100037', 2025, 2),('BD101',  '6100040', 2025, 2);

-- ============================================================
-- NOTAS / COHORTES (porcentajes suman 100% en cada curso)
-- ============================================================
INSERT INTO notas (desc_nota, porcentaje, posicion, cod_cur) VALUES
    -- BD101: 3 cortes = 30 + 30 + 40
    ('Parcial uno',     30.00, 1, 'BD101'),
    ('Parcial dos',     30.00, 2, 'BD101'),
    ('Examen final',    40.00, 3, 'BD101'),
    -- POO202: 4 cortes = 20 + 20 + 30 + 30
    ('Taller 1',        20.00, 1, 'POO202'),
    ('Taller 2',        20.00, 2, 'POO202'),
    ('Parcial',         30.00, 3, 'POO202'),
    ('Proyecto final',  30.00, 4, 'POO202'),
    -- ALG303: 2 cortes = 40 + 60
    ('Parcial',         40.00, 1, 'ALG303'),
    ('Final',           60.00, 2, 'ALG303'),
    -- MAT404: 3 cortes ≈ 33% cada uno
    ('Corte 1',         33.00, 1, 'MAT404'),
    ('Corte 2',         33.00, 2, 'MAT404'),
    ('Corte 3',         34.00, 3, 'MAT404'),
    -- RED505: 3 cortes = 25 + 25 + 50
    ('Quiz inicial',    25.00, 1, 'RED505'),
    ('Examen medio',    25.00, 2, 'RED505'),
    ('Examen final',    50.00, 3, 'RED505'),
    -- SO606: 4 cortes = 25 + 25 + 25 + 25
    ('Corte 1',         25.00, 1, 'SO606'),
    ('Corte 2',         25.00, 2, 'SO606'),
    ('Corte 3',         25.00, 3, 'SO606'),
    ('Corte 4',         25.00, 4, 'SO606');

-- ============================================================
-- CALIFICACIONES — IDs de nota generados por SERIAL en orden:
--   BD101:   1=P.uno  2=P.dos  3=E.final
--   POO202:  4=T1     5=T2     6=Parcial  7=Proyecto
--   ALG303:  8=Parcial 9=Final
--   MAT404: 10=C1   11=C2  12=C3
--   RED505: 13=Quiz 14=Medio 15=Final
--   SO606:  16=C1  17=C2  18=C3  19=C4
-- ============================================================

-- BD101 / Parcial uno
INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo) VALUES
    (1, 4.30, '2026-03-10', 'BD101', '6100021', 2026, 1),
    (1, 3.80, '2026-03-10', 'BD101', '6100022', 2026, 1),
    (1, 4.30, '2026-03-10', 'BD101', '6100023', 2026, 1),
    (1, 3.20, '2026-03-10', 'BD101', '6100024', 2026, 1),
    (1, 2.90, '2026-03-10', 'BD101', '6100025', 2026, 1),
    (1, 4.80, '2026-03-10', 'BD101', '6100026', 2026, 1),
    (1, 3.50, '2026-03-10', 'BD101', '6100029', 2026, 1),
    (1, 2.10, '2026-03-10', 'BD101', '6100031', 2026, 1),
    (1, 4.60, '2026-03-10', 'BD101', '6100035', 2026, 1),
    (1, 3.90, '2026-03-10', 'BD101', '6100038', 2026, 1);

-- BD101 / Parcial dos
INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo) VALUES
    (2, 3.50, '2026-04-14', 'BD101', '6100021', 2026, 1),
    (2, 4.10, '2026-04-14', 'BD101', '6100022', 2026, 1),
    (2, 2.80, '2026-04-14', 'BD101', '6100023', 2026, 1),
    (2, 4.00, '2026-04-14', 'BD101', '6100024', 2026, 1),
    (2, 3.60, '2026-04-14', 'BD101', '6100025', 2026, 1),
    (2, 4.50, '2026-04-14', 'BD101', '6100026', 2026, 1),
    (2, 3.00, '2026-04-14', 'BD101', '6100035', 2026, 1);
-- BD101 / Examen final intencionalmente parcial — para mostrar columna con ceros
INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo) VALUES
    (3, 4.20, '2026-05-08', 'BD101', '6100021', 2026, 1),
    (3, 3.80, '2026-05-08', 'BD101', '6100026', 2026, 1);

-- POO202
INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo) VALUES
    (4, 4.80, '2026-03-05', 'POO202', '6100021', 2026, 1),
    (4, 3.60, '2026-03-05', 'POO202', '6100026', 2026, 1),
    (4, 4.20, '2026-03-05', 'POO202', '6100027', 2026, 1),
    (4, 3.90, '2026-03-05', 'POO202', '6100030', 2026, 1),
    (4, 2.80, '2026-03-05', 'POO202', '6100032', 2026, 1),
    (5, 4.50, '2026-03-25', 'POO202', '6100021', 2026, 1),
    (5, 3.20, '2026-03-25', 'POO202', '6100026', 2026, 1),
    (5, 4.00, '2026-03-25', 'POO202', '6100036', 2026, 1),
    (6, 3.80, '2026-04-20', 'POO202', '6100021', 2026, 1),
    (6, 4.10, '2026-04-20', 'POO202', '6100027', 2026, 1);

-- MAT404 (2025-2 — período anterior)
INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo) VALUES
    (10, 3.10, '2025-09-20', 'MAT404', '6100023', 2025, 2),
    (10, 4.50, '2025-09-20', 'MAT404', '6100024', 2025, 2),
    (10, 3.80, '2025-09-20', 'MAT404', '6100025', 2025, 2),
    (10, 2.60, '2025-09-20', 'MAT404', '6100028', 2025, 2),
    (11, 4.20, '2025-10-15', 'MAT404', '6100023', 2025, 2),
    (11, 3.90, '2025-10-15', 'MAT404', '6100024', 2025, 2),
    (12, 4.00, '2025-11-20', 'MAT404', '6100023', 2025, 2);

-- RED505
INSERT INTO calificaciones (nota, valor, fecha, cod_cur, cod_est, year, periodo) VALUES
    (13, 3.80, '2026-03-12', 'RED505', '6100023', 2026, 1),
    (13, 4.20, '2026-03-12', 'RED505', '6100025', 2026, 1),
    (13, 3.50, '2026-03-12', 'RED505', '6100029', 2026, 1),
    (14, 4.00, '2026-04-15', 'RED505', '6100023', 2026, 1);
