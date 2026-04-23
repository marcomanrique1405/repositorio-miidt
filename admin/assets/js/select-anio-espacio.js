document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('filtro-anio');
    if (!select) return;

    let menu = null;

    function posicionarMenu() {
        if (!menu) return;

        const rect = select.getBoundingClientRect();

        menu.style.left = rect.left + 'px';
        menu.style.top = (rect.bottom + 2) + 'px';
        menu.style.width = rect.width + 'px';
    }

    function abrirMenu() {
        if (menu) return;

        const rect = select.getBoundingClientRect();

        menu = document.createElement('div');
        menu.style.position = 'fixed';
        menu.style.left = rect.left + 'px';
        menu.style.top = (rect.bottom + 2) + 'px';
        menu.style.width = rect.width + 'px';
        menu.style.maxHeight = '220px';
        menu.style.overflowY = 'auto';
        menu.style.background = '#fff';
        menu.style.border = '1px solid #c9c9c9';
        menu.style.borderRadius = '10px';
        menu.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
        menu.style.zIndex = '999999';

        const anioActual = new Date().getFullYear();

        for (let anio = anioActual; anio >= 2016; anio--) {

            const option = document.createElement('div');
            option.textContent = anio;

            option.style.padding = '10px 12px';
            option.style.cursor = 'pointer';
            option.style.fontSize = '13px';
            option.style.color = '#333';

            if (String(select.value) === String(anio)) {
                option.style.background = '#1f3c88';
                option.style.color = '#fff';
            }

            option.addEventListener('mouseenter', () => {
                option.style.background = '#1f3c88';
                option.style.color = '#fff';
            });

            option.addEventListener('mouseleave', () => {
                if (String(select.value) === String(anio)) return;
                option.style.background = '#fff';
                option.style.color = '#333';
            });

            option.addEventListener('click', (e) => {
                e.stopPropagation();

                select.value = anio;
                select.dispatchEvent(new Event('change', { bubbles: true }));

                menu.remove();
                menu = null;
            });

            menu.appendChild(option);
        }

        document.body.appendChild(menu);

        // 🔥 REPOSICIONAR EN TIEMPO REAL
        window.addEventListener('scroll', posicionarMenu, true);
        window.addEventListener('resize', posicionarMenu);
    }

    select.addEventListener('mousedown', (e) => {
        e.preventDefault();
        e.stopPropagation();
        abrirMenu();
    });

    select.addEventListener('focus', () => {
        select.blur();
    });

});