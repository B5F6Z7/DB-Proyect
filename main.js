/* ============================================================
   Funciones globales JS — validaciones de entrada
   ============================================================ */

// Validar inputs numéricos al perder el foco (no mientras se tipea, para no
// interferir con valores intermedios como "0.5" o "10" antes de un decimal).
document.addEventListener('blur', e => {
    if (!e.target.matches('input[type="number"]')) return;
    const min = parseFloat(e.target.min);
    const max = parseFloat(e.target.max);
    const v   = parseFloat(e.target.value);
    if (isNaN(v)) return;
    if (!isNaN(min) && v < min) e.target.value = min;
    if (!isNaN(max) && v > max) e.target.value = max;
}, true);

// Confirmación antes de eliminar
document.addEventListener('click', e => {
    const el = e.target.closest('[data-confirm]');
    if (el && !confirm(el.dataset.confirm)) e.preventDefault();
});

// Modales
function openModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.add('active');
}
function closeModal(id) {
    const m = document.getElementById(id);
    if (m) m.classList.remove('active');
}
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});
