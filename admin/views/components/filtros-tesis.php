<div class="admin-dashboard__filters">

    <div class="admin-dashboard__filters-header">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
            <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"
                d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z"/>
        </svg>

        <h2 class="admin-dashboard__filters-title">Filtros de búsqueda</h2>
    </div>

    <div class="admin-dashboard__filters-card">
        <h3 class="admin-dashboard__filters-subtitle">LIES</h3>

        <label class="admin-dashboard__filters-option">
            <input type="checkbox" class="filtro-lies" value="CSR">
            <span>CSR</span>
        </label>

        <label class="admin-dashboard__filters-option">
            <input type="checkbox" class="filtro-lies" value="Geomatica">
            <span>Geomática</span>
        </label>

        <label class="admin-dashboard__filters-option">
            <input type="checkbox" class="filtro-lies" value="TICs">
            <span>TIC</span>
        </label>
    </div>

    <div class="admin-dashboard__filters-card">
        <h3 class="admin-dashboard__filters-subtitle">Estado</h3>

        <label class="admin-dashboard__filters-option">
            <input type="checkbox" class="filtro-estado" value="Fisico">
            <span>Físico</span>
        </label>

        <label class="admin-dashboard__filters-option">
            <input type="checkbox" class="filtro-estado" value="Digital">
            <span>Digital</span>
        </label>

        <label class="admin-dashboard__filters-option">
            <input type="checkbox" class="filtro-estado" value="Digital y Fisico">
            <span>Ambos</span>
        </label>
    </div>

    <div class="admin-dashboard__filters-card">
        <select id="filtro-anio" class="admin-dashboard__filters-select">
            <option value="">Año de publicación</option>
        </select>
    </div>

    <div class="admin-dashboard__filters-card">
        <h3 class="admin-dashboard__filters-subtitle">Director de tesis:</h3>

        <input
            type="text"
            id="filtro-director"
            class="admin-dashboard__filters-input"
            placeholder="Buscar director"
            autocomplete="off"
        >
    </div>

    <div class="admin-dashboard__filters-card admin-dashboard__filters-card--active">
        <span class="admin-dashboard__filters-active-label">Filtros activos:</span>
        <strong id="filtros-activos-texto" class="admin-dashboard__filters-active-value">Ninguno</strong>
    </div>

    <button type="button" id="btn-limpiar-filtros" class="admin-dashboard__filters-clear">
        Limpiar todos los filtros
    </button>

</div>