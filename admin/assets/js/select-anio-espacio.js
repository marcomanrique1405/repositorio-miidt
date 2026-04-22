document.addEventListener('DOMContentLoaded', () => {

    const select = document.getElementById('filtro-anio');
    if (!select) return;

    let menu = null;

    select.addEventListener('mousedown', (e) => {

        e.preventDefault();

        if (menu) {
            menu.remove();
            menu = null;
            return;
        }

        const rect = select.getBoundingClientRect();

        menu = document.createElement('div');
        menu.style.position = 'fixed';
        menu.style.left = rect.left + 'px';
        menu.style.top = (rect.bottom + 2) + 'px'; // más pegado al select
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
            option.style.transition = '0.15s';

            if (select.value == anio) {
                option.style.background = '#1f3c88';
                option.style.color = '#fff';
            }

            option.addEventListener('mouseenter', () => {
                option.style.background = '#1f3c88';
                option.style.color = '#fff';
            });

            option.addEventListener('mouseleave', () => {
                if (select.value == anio) return;
                option.style.background = '#fff';
                option.style.color = '#333';
            });

            option.addEventListener('click', () => {
                select.value = anio;
                select.dispatchEvent(new Event('change', { bubbles: true }));

                if (menu) {
                    menu.remove();
                    menu = null;
                }
            });

            menu.appendChild(option);
        }

        document.body.appendChild(menu);

    });

    document.addEventListener('click', (e) => {
        if (!menu) return;

        if (e.target !== select && !menu.contains(e.target)) {
            menu.remove();
            menu = null;
        }
    });

});