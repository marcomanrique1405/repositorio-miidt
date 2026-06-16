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
       ESTILOS DEL CALENDARIO
       Solo afecta calendarios de tesis
    =============================== */
    function insertarEstilosCalendarioTesis() {
        if (document.getElementById('calendario-tesis-estilos')) return;

        const style = document.createElement('style');
        style.id = 'calendario-tesis-estilos';

        style.textContent = `
            .calendario-tesis.flatpickr-calendar {
                width: 380px !important;
                padding: 18px 22px 22px 22px !important;
                border: none !important;
                border-radius: 28px !important;
                background: #ffffff !important;
                box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22) !important;
                font-family: 'Roboto Condensed', sans-serif !important;
                overflow: visible !important;
                margin-top: -70px !important;
            }

            .calendario-tesis .flatpickr-months {
                height: 42px !important;
                margin-bottom: 12px !important;
                position: relative !important;
                overflow: visible !important;
                z-index: 999999 !important;
            }

            .calendario-tesis .flatpickr-month {
                height: 42px !important;
                overflow: visible !important;
                position: relative !important;
                z-index: 999999 !important;
            }

            .calendario-tesis .flatpickr-current-month {
                width: 100% !important;
                height: 42px !important;
                left: 0 !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
                overflow: visible !important;
                position: relative !important;
                z-index: 9999999 !important;
            }

            .calendario-tesis .flatpickr-current-month .flatpickr-monthDropdown-months {
                position: relative !important;
                width: 155px !important;
                height: 38px !important;
                padding: 0 26px 0 0 !important;
                border: none !important;
                outline: none !important;
                background-color: transparent !important;
                color: #111111 !important;
                font-family: 'Roboto Condensed', sans-serif !important;
                font-size: 22px !important;
                font-weight: 700 !important;
                line-height: 38px !important;
                cursor: pointer !important;
                appearance: none !important;
                -webkit-appearance: none !important;
                background-image:
                    linear-gradient(45deg, transparent 50%, #111 50%),
                    linear-gradient(135deg, #111 50%, transparent 50%);
                background-position:
                    calc(100% - 13px) 17px,
                    calc(100% - 7px) 17px;
                background-size: 6px 6px, 6px 6px;
                background-repeat: no-repeat;
                z-index: 9999999 !important;
            }

            .calendario-tesis .flatpickr-current-month .flatpickr-monthDropdown-months.calendario-mes-activo {
                background-image:
                    linear-gradient(135deg, transparent 50%, #111 50%),
                    linear-gradient(45deg, #111 50%, transparent 50%);
            }

            .calendario-tesis .calendario-meses-menu {
                position: absolute !important;
                top: 38px !important;
                left: 0 !important;
                width: 160px !important;
                max-height: none !important;
                padding: 6px 0 !important;
                background: #ffffff !important;
                border-radius: 0 0 14px 14px !important;
                box-shadow: 0 12px 22px rgba(0, 0, 0, 0.16) !important;
                overflow: visible !important;
                z-index: 99999999 !important;
                display: none !important;
                opacity: 1 !important;
                pointer-events: auto !important;
            }

            .calendario-tesis .calendario-meses-menu.visible {
                display: block !important;
            }

            .calendario-tesis .calendario-mes-opcion {
                width: 100% !important;
                padding: 7px 10px !important;
                border: none !important;
                background: #ffffff !important;
                color: #333333 !important;
                font-family: 'Roboto Condensed', sans-serif !important;
                font-size: 16px !important;
                text-align: left !important;
                cursor: pointer !important;
                position: relative !important;
                z-index: 99999999 !important;
            }

            .calendario-tesis .calendario-mes-opcion:hover,
            .calendario-tesis .calendario-mes-opcion.activo {
                background: #7f8cff !important;
                color: #111111 !important;
            }

            .calendario-tesis .calendario-mes-opcion.deshabilitado {
                color: #c5c5c5 !important;
                cursor: not-allowed !important;
                background: #ffffff !important;
            }

            .calendario-tesis .flatpickr-current-month .numInputWrapper {
                width: 88px !important;
                height: 38px !important;
                position: absolute !important;
                right: 36px !important;
                top: 0 !important;
                z-index: 999999 !important;
            }

            .calendario-tesis .flatpickr-current-month input.cur-year {
                width: 88px !important;
                height: 38px !important;
                padding: 0 !important;
                border: none !important;
                background: transparent !important;
                color: #111111 !important;
                font-family: 'Roboto Condensed', sans-serif !important;
                font-size: 22px !important;
                font-weight: 700 !important;
                text-align: center !important;
                cursor: text !important;
            }

            .calendario-tesis .numInputWrapper span {
                display: none !important;
            }

            .calendario-tesis .flatpickr-prev-month,
            .calendario-tesis .flatpickr-next-month {
                top: 2px !important;
                width: 30px !important;
                height: 36px !important;
                padding: 0 !important;
                color: #111111 !important;
                fill: #111111 !important;
                opacity: 1 !important;
                font-size: 24px !important;
                line-height: 36px !important;
                z-index: 999999 !important;
            }

            .calendario-tesis .flatpickr-prev-month {
                left: auto !important;
                right: 126px !important;
            }

            .calendario-tesis .flatpickr-next-month {
                right: 0 !important;
            }

            .calendario-tesis .flatpickr-prev-month:hover,
            .calendario-tesis .flatpickr-next-month:hover {
                color: #a80000 !important;
                fill: #a80000 !important;
            }

            .calendario-tesis .flatpickr-weekdays {
                height: 36px !important;
                margin-bottom: 4px !important;
                position: relative !important;
                z-index: 1 !important;
            }

            .calendario-tesis span.flatpickr-weekday {
                color: #1f3c88 !important;
                font-family: 'Roboto Condensed', sans-serif !important;
                font-size: 18px !important;
                font-weight: 400 !important;
            }

            .calendario-tesis .flatpickr-innerContainer,
            .calendario-tesis .flatpickr-rContainer,
            .calendario-tesis .flatpickr-days,
            .calendario-tesis .dayContainer {
                width: 100% !important;
                min-width: 100% !important;
                max-width: 100% !important;
                position: relative !important;
                z-index: 1 !important;
            }

            .calendario-tesis .dayContainer {
                display: flex !important;
                flex-wrap: wrap !important;
                justify-content: space-between !important;
            }

            .calendario-tesis .flatpickr-day {
                width: calc(100% / 7) !important;
                max-width: calc(100% / 7) !important;
                flex-basis: calc(100% / 7) !important;
                height: 44px !important;
                line-height: 44px !important;
                margin: 0 !important;
                border: none !important;
                border-radius: 50% !important;
                color: #222222 !important;
                font-family: 'Roboto Condensed', sans-serif !important;
                font-size: 19px !important;
                font-weight: 400 !important;
            }

            .calendario-tesis .flatpickr-day.prevMonthDay,
            .calendario-tesis .flatpickr-day.nextMonthDay {
                color: #9a9a9a !important;
            }

            .calendario-tesis .flatpickr-day:hover {
                background: #eeeeee !important;
                color: #222222 !important;
            }

            .calendario-tesis .flatpickr-day.today {
                border: none !important;
            }

            .calendario-tesis .flatpickr-day.selected,
            .calendario-tesis .flatpickr-day.startRange,
            .calendario-tesis .flatpickr-day.endRange {
                background: #a80000 !important;
                border-color: #a80000 !important;
                color: #ffffff !important;
                box-shadow: none !important;
            }

            .calendario-tesis .flatpickr-day.selected:hover {
                background: #a80000 !important;
                color: #ffffff !important;
            }

            .calendario-tesis .flatpickr-day.flatpickr-disabled,
            .calendario-tesis .flatpickr-day.flatpickr-disabled:hover {
                color: #c8c8c8 !important;
                background: transparent !important;
                cursor: not-allowed !important;
            }

            @media (max-width: 520px) {
                .calendario-tesis.flatpickr-calendar {
                    width: calc(100vw - 34px) !important;
                    padding: 18px 18px 22px 18px !important;
                    border-radius: 24px !important;
                    margin-top: -55px !important;
                }

                .calendario-tesis .flatpickr-current-month .flatpickr-monthDropdown-months {
                    width: 140px !important;
                    font-size: 20px !important;
                }

                .calendario-tesis .flatpickr-current-month input.cur-year {
                    font-size: 20px !important;
                }

                .calendario-tesis span.flatpickr-weekday {
                    font-size: 16px !important;
                }

                .calendario-tesis .flatpickr-day {
                    height: 40px !important;
                    line-height: 40px !important;
                    font-size: 17px !important;
                }
            }
        `;

        document.head.appendChild(style);
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

    const mesesCalendarioTesis = [
        'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    function asegurarFechaNoFutura(instance) {
        if (!instance) return;

        const hoy = new Date();
        const anioActual = hoy.getFullYear();
        const mesActual = hoy.getMonth();

        if (instance.currentYear > anioActual) {
            instance.changeYear(anioActual);
            instance.changeMonth(mesActual, false);
            return;
        }

        if (instance.currentYear === anioActual && instance.currentMonth > mesActual) {
            instance.changeMonth(mesActual, false);
        }
    }

    function prepararControlAnio(instance) {
        if (!instance || !instance.calendarContainer) return;

        const inputAnio = instance.calendarContainer.querySelector('.flatpickr-current-month input.cur-year');

        if (!inputAnio || inputAnio.dataset.anioListo === '1') return;

        inputAnio.dataset.anioListo = '1';

        const anioActual = new Date().getFullYear();

        inputAnio.setAttribute('max', String(anioActual));
        inputAnio.setAttribute('min', '1900');

        inputAnio.addEventListener('input', () => {
            const valor = parseInt(inputAnio.value, 10);

            if (!Number.isNaN(valor) && valor > anioActual) {
                inputAnio.value = String(anioActual);
            }
        });

        inputAnio.addEventListener('change', () => {
            const valor = parseInt(inputAnio.value, 10);

            if (Number.isNaN(valor)) {
                instance.changeYear(anioActual);
                return;
            }

            if (valor > anioActual) {
                instance.changeYear(anioActual);
                return;
            }

            instance.changeYear(valor);
            asegurarFechaNoFutura(instance);
        });

        inputAnio.addEventListener('blur', () => {
            asegurarFechaNoFutura(instance);
        });
    }

    function crearMenuMeses(instance, selectorMes) {
        const contenedorMes = selectorMes.closest('.flatpickr-current-month');

        if (!contenedorMes) return null;

        let menu = contenedorMes.querySelector('.calendario-meses-menu');

        if (!menu) {
            menu = document.createElement('div');
            menu.className = 'calendario-meses-menu';
            contenedorMes.appendChild(menu);
        }

        menu.innerHTML = '';

        const hoy = new Date();
        const anioActual = hoy.getFullYear();
        const mesActual = hoy.getMonth();

        mesesCalendarioTesis.forEach((mes, index) => {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.className = 'calendario-mes-opcion';
            boton.textContent = mes;

            const esMesActivo = index === instance.currentMonth;
            const esMesFuturo = instance.currentYear === anioActual && index > mesActual;

            if (esMesActivo) {
                boton.classList.add('activo');
            }

            if (esMesFuturo) {
                boton.classList.add('deshabilitado');
                boton.disabled = true;
            }

            boton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                if (boton.disabled) return;

                instance.changeMonth(index, false);

                menu.classList.remove('visible');
                selectorMes.classList.remove('calendario-mes-activo');
            });

            menu.appendChild(boton);
        });

        return menu;
    }

    function prepararMenuMeses(instance) {
        if (!instance || !instance.calendarContainer) return;

        const selectorMes = instance.calendarContainer.querySelector('.flatpickr-monthDropdown-months');

        if (!selectorMes || selectorMes.dataset.mesMenuListo === '1') return;

        selectorMes.dataset.mesMenuListo = '1';

        let timerOcultar = null;

        function mostrarMenu() {
            clearTimeout(timerOcultar);

            const menu = crearMenuMeses(instance, selectorMes);

            if (!menu) return;

            selectorMes.classList.add('calendario-mes-activo');
            menu.classList.add('visible');

            menu.addEventListener('mouseenter', () => {
                clearTimeout(timerOcultar);
            });

            menu.addEventListener('mouseleave', () => {
                timerOcultar = setTimeout(() => {
                    menu.classList.remove('visible');
                    selectorMes.classList.remove('calendario-mes-activo');
                }, 120);
            });
        }

        function ocultarMenu() {
            const menu = selectorMes
                .closest('.flatpickr-current-month')
                ?.querySelector('.calendario-meses-menu');

            timerOcultar = setTimeout(() => {
                if (menu) menu.classList.remove('visible');
                selectorMes.classList.remove('calendario-mes-activo');
            }, 120);
        }

        selectorMes.addEventListener('mouseenter', mostrarMenu);
        selectorMes.addEventListener('mouseleave', ocultarMenu);

        selectorMes.addEventListener('mousedown', (e) => {
            e.preventDefault();
            e.stopPropagation();
            mostrarMenu();
        });

        selectorMes.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            mostrarMenu();
        });
    }

    function prepararCalendarioTesis(instance) {
        if (!instance || !instance.calendarContainer) return;

        instance.calendarContainer.classList.add('calendario-tesis');

        prepararControlAnio(instance);
        prepararMenuMeses(instance);
        asegurarFechaNoFutura(instance);
    }

    insertarEstilosCalendarioTesis();

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
        position: 'below left',
        maxDate: 'today',
        onReady: function (selectedDates, dateStr, instance) {
            prepararCalendarioTesis(instance);
        },
        onOpen: function (selectedDates, dateStr, instance) {
            prepararCalendarioTesis(instance);
        },
        onMonthChange: function (selectedDates, dateStr, instance) {
            prepararCalendarioTesis(instance);
        },
        onYearChange: function (selectedDates, dateStr, instance) {
            prepararCalendarioTesis(instance);
        }
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