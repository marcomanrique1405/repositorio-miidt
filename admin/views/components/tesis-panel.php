<div class="admin-dashboard__tesis-panel"> 

    <div class="admin-dashboard__tesis-toolbar">
        <button type="button" id="btnAgregarTesis" class="btn-agregar">
            + Agregar
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

        <div class="admin-dashboard__tesis-list" id="contenedor-tesis">

            <div class="tesis-item">
                <div class="tesis-item__cover">
                    <img src="ruta-imagen.jpg" alt="Portada de tesis">
                </div>

                <div class="tesis-item__content">
                    <h3 class="tesis-item__title">
                        Aplicación de sistemas de información geográfica para la identificación de zonas vulnerables ante riesgos sísmicos en comunidades marginadas.
                    </h3>

                    <p class="tesis-item__meta"><strong>Autor:</strong> María López García</p>
                    <p class="tesis-item__meta"><strong>Director:</strong> Dr. Roberto Arroyo Matus</p>
                    <p class="tesis-item__meta"><strong>LIES:</strong> Construcción Sismo Resistente</p>
                    <p class="tesis-item__meta"><strong>Estado:</strong> Físico, Digital</p>

                    <div class="tesis-item__actions">
                        <button type="button" class="tesis-item__edit">Editar</button>
                        <button type="button" class="tesis-item__delete">Eliminar</button>
                    </div>
                </div>

                <div class="tesis-item__year">2026</div>
            </div>

        </div>
    </section>

</div>