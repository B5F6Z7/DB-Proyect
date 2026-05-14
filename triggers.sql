-- ============================================================
-- DISPARADORES (TRIGGERS) — Notas Parciales de Docentes
-- PostgreSQL 16
-- ============================================================

-- ============================================================
-- TRIGGER 1: Validar que los porcentajes de un curso no superen 100%
--
-- Se activa ANTES de insertar o actualizar una nota/cohorte.
-- Suma todos los porcentajes del curso y verifica que al agregar
-- el nuevo no se supere el 100%.
-- ============================================================

CREATE OR REPLACE FUNCTION fn_validar_porcentaje_curso()
RETURNS TRIGGER AS $$
DECLARE
    v_total      NUMERIC;
    v_excluir_id INTEGER := 0;  -- en INSERT no hay fila anterior; SERIAL empieza en 1
BEGIN
    -- En UPDATE excluimos la fila actual para no contarla dos veces
    IF TG_OP = 'UPDATE' THEN
        v_excluir_id := OLD.nota;
    END IF;

    SELECT COALESCE(SUM(porcentaje), 0)
      INTO v_total
      FROM notas
     WHERE cod_cur = NEW.cod_cur
       AND nota   != v_excluir_id;

    IF (v_total + NEW.porcentaje) > 100 THEN
        RAISE EXCEPTION
            'El porcentaje total del curso "%" superaría 100%%. Acumulado actual: %, se intenta agregar: %.',
            NEW.cod_cur, v_total, NEW.porcentaje;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tg_validar_porcentaje_curso
BEFORE INSERT OR UPDATE OF porcentaje, cod_cur ON notas
FOR EACH ROW
EXECUTE FUNCTION fn_validar_porcentaje_curso();


-- ============================================================
-- TRIGGER 2: Validar que la nota (cohorte) registrada en
-- calificaciones pertenezca al mismo curso de la inscripción
--
-- Previene inconsistencias entre cod_cur de la inscripción
-- y el cod_cur de la nota/cohorte que se está calificando.
-- ============================================================

CREATE OR REPLACE FUNCTION fn_validar_nota_pertenece_curso()
RETURNS TRIGGER AS $$
DECLARE
    v_cur_de_nota VARCHAR(20);
BEGIN
    -- Obtener el curso al que pertenece la nota/cohorte
    SELECT cod_cur
      INTO v_cur_de_nota
      FROM notas
     WHERE nota = NEW.nota;

    IF NOT FOUND THEN
        RAISE EXCEPTION
            'La nota/cohorte con id % no existe.', NEW.nota;
    END IF;

    IF v_cur_de_nota <> NEW.cod_cur THEN
        RAISE EXCEPTION
            'La nota/cohorte % pertenece al curso "%" pero se intenta registrar en el curso "%". Operación cancelada.',
            NEW.nota, v_cur_de_nota, NEW.cod_cur;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER tg_validar_nota_pertenece_curso
BEFORE INSERT OR UPDATE OF nota, cod_cur ON calificaciones
FOR EACH ROW
EXECUTE FUNCTION fn_validar_nota_pertenece_curso();
