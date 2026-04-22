<div class="admin-dashboard__filters">

    <div class="admin-dashboard__filters-header">
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