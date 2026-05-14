# Plan de Ejecución — Notas Parciales de Docentes

## Stack
- Backend: PHP puro (sin frameworks)
- BD: PostgreSQL
- Frontend: HTML5 + CSS3 custom + Bootstrap 5 CDN (solo CSS/UI, no es framework PHP)
- PDF: FPDF (librería PHP pura)
- SO: Linux

---

## FASE 1 — Base de Datos (SQL)
**Archivos:** `sql/schema.sql`, `sql/triggers.sql`, `sql/seed.sql`

- [ ] Crear tablas: estudiantes, docentes, cursos, inscripciones, notas, calificaciones
- [ ] Claves primarias, foráneas e integridad referencial
- [ ] CASCADE DELETE en inscripciones y calificaciones
- [ ] Trigger 1: calcular nota definitiva automáticamente al insertar/actualizar calificación
- [ ] Trigger 2: validar que porcentajes de notas por curso no superen 100%
- [ ] Datos de prueba (seed)

---

## FASE 2 — Configuración y Conexión PHP
**Archivos:** `config/database.php`, `config/session.php`

- [ ] Clase de conexión PDO a PostgreSQL
- [ ] Manejo de sesiones (login de docente)
- [ ] Constantes del sistema

---

## FASE 3 — Modelos (Capa de datos)
**Archivos:** `models/*.php`

- [ ] Docente.php — login, datos del docente
- [ ] Curso.php — CRUD cursos
- [ ] Estudiante.php — CRUD estudiantes
- [ ] Inscripcion.php — inscribir/eliminar estudiantes por curso/año/periodo
- [ ] Nota.php — CRUD notas parciales (cohortes) por curso
- [ ] Calificacion.php — registrar/actualizar valor de nota por estudiante

---

## FASE 4 — Includes y Layout
**Archivos:** `includes/header.php`, `includes/navbar.php`, `includes/footer.php`

- [ ] Layout base con sidebar y topbar
- [ ] Navbar con info del docente logueado
- [ ] Dashboard principal con tarjetas de resumen
- [ ] Mensajes de éxito/error globales

---

## FASE 5 — Pantallas (según PDF del proyecto)

### 5.1 Login
- [ ] `index.php` — formulario de login docente (cod_doc + clave)

### 5.2 Dashboard
- [ ] `pages/dashboard.php` — resumen de cursos, estudiantes inscritos, notas pendientes

### 5.3 Selección de curso, año y período
- [ ] `pages/cursos.php` — dropdown cursos, campo año, radio periodo I/II

### 5.4 Inscripción de estudiantes
- [ ] `pages/inscripciones.php` — listado inscritos + agregar/eliminar estudiantes

### 5.5 Notas parciales por curso (cohortes)
- [ ] `pages/notas_parciales.php` — CRUD de cohortes (posición, descripción, porcentaje)

### 5.6 Registro y actualización de notas
- [ ] `pages/registro_notas.php` — ingresar/editar nota por estudiante y cohorte

### 5.7 Reporte de notas
- [ ] `pages/reporte.php` — tabla dinámica cohortes + nota definitiva
- [ ] `pdf/generar_pdf.php` — exportar reporte a PDF con FPDF

---

## FASE 6 — Validaciones
- [ ] Campos numéricos no negativos (JS + PHP)
- [ ] Porcentajes entre 0 y 100
- [ ] Notas entre 0.0 y 5.0
- [ ] Campos requeridos
- [ ] Año válido (4 dígitos, no futuro lejano)

---

## FASE 7 — UI/UX (Dashboard bonito)
- [ ] Paleta de colores institucional (azul/blanco/verde)
- [ ] Sidebar fijo con íconos
- [ ] Tablas con hover, paginación
- [ ] Cards de estadísticas en dashboard
- [ ] Modales para agregar/editar datos
- [ ] Responsive design
- [ ] Animaciones suaves

---

## Orden de ejecución recomendado

1. FASE 1 → SQL completo con triggers
2. FASE 2 → Conexión y sesión
3. FASE 3 → Modelos
4. FASE 5.1 → Login funcional
5. FASE 4 → Layout
6. FASE 5.2 → Dashboard
7. FASE 5.3 → 5.7 → Pantallas en orden
8. FASE 6 → Validaciones
9. FASE 7 → Pulir UI

---

## Estructura de archivos

```
NotasParciales/
├── index.php                  # Login
├── config/
│   ├── database.php           # Conexión PDO PostgreSQL
│   └── session.php            # Control de sesión
├── assets/
│   ├── css/style.css          # Estilos custom
│   ├── js/main.js             # Validaciones JS
│   └── img/                   # Íconos y logos
├── includes/
│   ├── header.php             # Head HTML + CSS
│   ├── navbar.php             # Sidebar + topbar
│   └── footer.php             # Cierre HTML + JS
├── models/
│   ├── Docente.php
│   ├── Curso.php
│   ├── Estudiante.php
│   ├── Inscripcion.php
│   ├── Nota.php
│   └── Calificacion.php
├── pages/
│   ├── dashboard.php
│   ├── cursos.php
│   ├── inscripciones.php
│   ├── notas_parciales.php
│   ├── registro_notas.php
│   └── reporte.php
├── pdf/
│   └── generar_pdf.php
└── sql/
    ├── schema.sql             # DDL completo
    ├── triggers.sql           # Disparadores
    └── seed.sql               # Datos de prueba
```
