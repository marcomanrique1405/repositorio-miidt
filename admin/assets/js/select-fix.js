document.addEventListener('DOMContentLoaded', () => {

    const select = document.getElementById('filtro-anio');
    const sidebar = document.querySelector('.admin-dashboard__sidebar-placeholder');

    if (!select || !sidebar) return;

    let scrollOriginal = 0;

    //  cuando abre el select
    select.addEventListener('mousedown', () => {

        scrollOriginal = sidebar.scrollTop;

        setTimeout(() => {
            select.scrollIntoView({
                block: 'center',
                behavior: 'smooth'
            });
        }, 0);
    });

    //  cuando selecciona una opción
    select.addEventListener('change', () => {

        setTimeout(() => {
            sidebar.scrollTo({
                top: scrollOriginal,
                behavior: 'smooth'
            });
        }, 100);
    });

});