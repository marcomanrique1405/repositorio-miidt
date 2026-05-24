/* ==========================================
   CALENDARIO TESIS - FLATPICKR
   Aplica para Alta y Editar tesis
========================================== */

document.addEventListener('DOMContentLoaded', function () {
    if (typeof flatpickr === 'undefined') {
        console.error('Flatpickr no está cargado.');
        return;
    }

    /* ===============================
       IDIOMA ESPAÑOL PERSONALIZADO
    =============================== */
    flatpickr.localize({
        weekdays: {
            shorthand: ['Dom', 'Lun', 'Mar', 'Miér', 'Jue', 'Vier', 'Sáb'],
            longhand: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
        },
        months: {
            shorthand: [
                'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
            ],
            longhand: [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ]
        },
        firstDayOfWeek: 0
    });

    /* ===============================
       CONFIGURACIÓN GENERAL
    =============================== */
    const configCalendarioTesis = {
        dateFormat: 'd/m/Y',
        allowInput: false,
        disableMobile: true,
        monthSelectorType: 'dropdown',
        prevArrow: '‹',
        nextArrow: '›',
        position: 'below left'
    };

    /* ===============================
       CALENDARIO DE ALTA DE TESIS
    =============================== */
    const fechaTesis = document.getElementById('fechaTesis');

    if (fechaTesis) {
        flatpickr(fechaTesis, configCalendarioTesis);
    }

    /* ===============================
       CALENDARIO DE EDITAR TESIS
    =============================== */
    const editarFechaTesis = document.getElementById('editarFechaTesis');

    if (editarFechaTesis) {
        flatpickr(editarFechaTesis, configCalendarioTesis);
    }
});