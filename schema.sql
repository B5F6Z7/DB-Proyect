-- ============================================================
-- PROYECTO: Notas Parciales de Docentes
-- Motor: PostgreSQL 16
-- Diagrama base: NotasU.png
-- ============================================================

-- Crear base de datos (ejecutar como superusuario antes de conectar)
-- CREATE DATABASE notas_parciales;

-- Limpiar schema si ya existe (útil para re-ejecutar)
DROP TABLE IF EXISTS calificaciones CASCADE;
DROP TABLE IF EXISTS inscripciones  CASCADE;
DROP TABLE IF EXISTS notas          CASCADE;
DROP TABLE IF EXISTS cursos         CASCADE;
DROP TABLE IF EXISTS estudiantes    CASCADE;
DROP TABLE IF EXISTS docentes       CASCADE;

-- ============================================================
-- TABLA: docentes
-- ============================================================
CREATE TABLE docentes (
    cod_doc  VARCHAR(20)  NOT NULL,
    nomb_doc VARCHAR(100) NOT NULL,
    clave    VARCHAR(255) NOT NULL,          -- contraseña hasheada (MD5/bcrypt)
    CONSTRAINT pk_docentes PRIMARY KEY (cod_doc)
);

-- ============================================================
-- TABLA: estudiantes
-- ============================================================
CREATE TABLE estudiantes (
    cod_est  VARCHAR(20)  NOT NULL,
    nomb_est VARCHAR(100) NOT NULL,
    CONSTRAINT pk_estudiantes PRIMARY KEY (cod_est)
);

-- ============================================================
-- TABLA: cursos
-- ============================================================
CREATE TABLE cursos (
    cod_cur  VARCHAR(20)  NOT NULL,
    nomb_cur VARCHAR(100) NOT NULL,
    cod_doc  VARCHAR(20)  NOT NULL,
    CONSTRAINT pk_cursos  PRIMARY KEY (cod_cur),
    CONSTRAINT fk_cursos_docente FOREIGN KEY (cod_doc)
        REFERENCES docentes(cod_doc)
        ON UPDATE CASCADE
        ON DELETE RESTRICT          -- no borrar docente si tiene cursos
);

-- ============================================================
-- TABLA: inscripciones
-- PK compuesta: identifica a un estudiante en un curso/año/periodo
-- ============================================================
CREATE TABLE inscripciones (
    cod_cur VARCHAR(20) NOT NULL,
    cod_est VARCHAR(20) NOT NULL,
    year    SMALLINT    NOT NULL,
    periodo SMALLINT    NOT NULL,
    CONSTRAINT pk_inscripciones   PRIMARY KEY (cod_cur, cod_est, year, periodo),
    CONSTRAINT ck_insc_year       CHECK (year >= 2000 AND year <= 2100),
    CONSTRAINT ck_insc_periodo    CHECK (periodo IN (1, 2)),
    CONSTRAINT fk_insc_curso      FOREIGN KEY (cod_cur)
        REFERENCES cursos(cod_cur)
        ON UPDATE CASCADE
        ON DELETE CASCADE,          -- borrar curso → borrar inscripciones
    CONSTRAINT fk_insc_estudiante FOREIGN KEY (cod_est)
        REFERENCES estudiantes(cod_est)
        ON UPDATE CASCADE
        ON DELETE CASCADE           -- borrar estudiante → borrar inscripciones
);

-- ============================================================
-- TABLA: notas  (cohortes/parciales definidos por el docente)
-- El campo "nota" es la PK serial tal como aparece en el diagrama
-- ============================================================
CREATE TABLE notas (
    nota       SERIAL       NOT NULL,
    desc_nota  VARCHAR(100) NOT NULL,
    porcentaje NUMERIC(5,2) NOT NULL,
    posicion   SMALLINT     NOT NULL,
    cod_cur    VARCHAR(20)  NOT NULL,
    CONSTRAINT pk_notas          PRIMARY KEY (nota),
    CONSTRAINT ck_nota_porcentaje CHECK (porcentaje > 0 AND porcentaje <= 100),
    CONSTRAINT ck_nota_posicion   CHECK (posicion > 0),
    CONSTRAINT uq_nota_posicion   UNIQUE (cod_cur, posicion),  -- sin posiciones duplicadas por curso
    CONSTRAINT fk_nota_curso      FOREIGN KEY (cod_cur)
        REFERENCES cursos(cod_cur)
        ON UPDATE CASCADE
        ON DELETE CASCADE           -- borrar curso → borrar sus notas/cohortes
);

-- ============================================================
-- TABLA: calificaciones
-- Registra el valor que obtuvo un estudiante en una nota/cohorte
-- ============================================================
CREATE TABLE calificaciones (
    cod_cal SERIAL       NOT NULL,
    nota    INTEGER      NOT NULL,       -- FK → notas.nota (el cohorte)
    valor   NUMERIC(4,2) NOT NULL,       -- nota del estudiante (0.00 – 5.00)
    fecha   DATE         NOT NULL DEFAULT CURRENT_DATE,
    cod_cur VARCHAR(20)  NOT NULL,
    cod_est VARCHAR(20)  NOT NULL,
    year    SMALLINT     NOT NULL,
    periodo SMALLINT     NOT NULL,
    CONSTRAINT pk_calificaciones  PRIMARY KEY (cod_cal),
    CONSTRAINT ck_calif_valor     CHECK (valor >= 0.00 AND valor <= 5.00),
    CONSTRAINT ck_calif_year      CHECK (year >= 2000 AND year <= 2100),
    CONSTRAINT ck_calif_periodo   CHECK (periodo IN (1, 2)),
    -- un estudiante solo puede tener UNA calificacion por cohorte/año/periodo
    CONSTRAINT uq_calificacion    UNIQUE (nota, cod_est, year, periodo),
    CONSTRAINT fk_calif_nota      FOREIGN KEY (nota)
        REFERENCES notas(nota)
        ON UPDATE CASCADE
        ON DELETE CASCADE,          -- borrar cohorte → borrar sus calificaciones
    CONSTRAINT fk_calif_inscripcion FOREIGN KEY (cod_cur, cod_est, year, periodo)
        REFERENCES inscripciones(cod_cur, cod_est, year, periodo)
        ON UPDATE CASCADE
        ON DELETE CASCADE           -- borrar inscripcion → borrar sus calificaciones
);

-- ============================================================
-- ÍNDICES para optimizar las consultas más frecuentes
-- ============================================================
CREATE INDEX idx_cursos_docente       ON cursos(cod_doc);
CREATE INDEX idx_insc_curso_year      ON inscripciones(cod_cur, year, periodo);
CREATE INDEX idx_notas_curso          ON notas(cod_cur);
CREATE INDEX idx_calif_est_curso      ON calificaciones(cod_est, cod_cur, year, periodo);
CREATE INDEX idx_calif_nota           ON calificaciones(nota);
