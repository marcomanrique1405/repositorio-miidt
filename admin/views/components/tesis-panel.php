<div class="admin-dashboard__tesis-panel"> 

    <div class="admin-dashboard__tesis-toolbar">
        <button type="button" id="btnAgregarTesis" class="btn-agregar">
            + Agregar
        </button>

        <button type="button" id="btn-filtros-toggle" class="btn-filtros">
            Filtros
        </button>
    </div>

    <div class="admin-dashboard__tesis-search">
        <div class="admin-dashboard__tesis-search-box">
            <span class="admin-dashboard__tesis-search-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <path fill="currentColor" d="m21.53 20.47l-3.66-3.66A9 9 0 1 0 16.81 17.87l3.66 3.66a.75.75 0 1 0 1.06-1.06M4.5 10.5a6 6 0 1 1 6 6a6 6 0 0 1-6-6"/>
                </svg>
            </span>

            <input
                type="text"
                id="buscador-tesis"
                class="admin-dashboard__tesis-search-input"
                placeholder="Buscar por título, autor o director de tesis..."
                autocomplete="off"
            >
        </div>
    </div>

    <section class="admin-dashboard__tesis-results">
        <div class="admin-dashboard__tesis-results-header">
            <h2 class="admin-dashboard__tesis-results-title">Resultado de la búsqueda</h2>
            <span class="admin-dashboard__tesis-results-count" id="tesis-total">0 Tesis</span>
        </div>

        <div class="admin-dashboard__tesis-list" id="contenedor-tesis"></div>
    </section>

</div>