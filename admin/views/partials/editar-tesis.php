<div id="editarTesisPanel">

    <input type="hidden" id="editarIdTesis">

    <div class="admin-dashboard__form-wrapper">

        <!-- HEADER -->
        <div class="admin-dashboard__form-header">
            Editar tesis
        </div>

        <div class="admin-dashboard__form-body">

            <!-- ===================== -->
            <!-- TARJETA 1 -->
            <!-- ===================== -->
            <div class="admin-dashboard__form-card">

                <div class="admin-dashboard__form-section-title">
                    Datos de la tesis
                </div>

                <label>Título de tesis:</label>
                <input type="text" id="editarTitulo" placeholder="Escribe el título">

                <label>Autor:</label>
                <input type="text" id="editarAutor" placeholder="Nombre del autor" readonly>

                <input type="hidden" id="editarAutorMatriculaHidden">
                <input type="hidden" id="editarAutorNombreHidden">
                <input type="hidden" id="editarAutorApellidoPaternoHidden">
                <input type="hidden" id="editarAutorApellidoMaternoHidden">
                <input type="hidden" id="editarAutorCorreoHidden">
                <input type="hidden" id="editarAutorSexoHidden">

                <label>Director de tesis:</label>
                <div class="director-autocomplete-wrap">

                    <span class="director-input-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M9.5 3a6.5 6.5 0 0 1 5.17 10.45l4.44 4.44a1 1 0 0 1-1.42 1.42l-4.44-4.44A6.5 6.5 0 1 1 9.5 3m0 2a4.5 4.5 0 1 0 0 9a4.5 4.5 0 0 0 0-9"/>
                        </svg>
                    </span>

                    <input type="text" id="editarDirector" placeholder="Buscar director" autocomplete="off">
                    <input type="hidden" id="editarIdDirector">
                    <input type="hidden" id="editarIdDirectorLinea">
                    <div id="editarDirectorSugerencias" class="director-sugerencias"></div>
                </div>

                <div class="admin-dashboard__form-row">

                    <div>
                        <label>Año:</label>
                        <input type="text" id="editarFechaTesis" placeholder="DD/MM/AAAA" readonly>
                    </div>

                    <div>
                        <label>Línea de Investigación:</label>
                        <div class="admin-checkbox-group">
                            <label><input type="checkbox" value="CSR" data-editar-id-linea="1"> CSR</label>
                            <label><input type="checkbox" value="Geomatica" data-editar-id-linea="3"> Geomática</label>
                            <label><input type="checkbox" value="TIC" data-editar-id-linea="2"> TIC</label>
                        </div>
                    </div>

                    <div>
                        <label>Estado:</label>
                        <div class="admin-checkbox-group">
                            <label><input type="checkbox" value="Fisico" data-editar-estado="Fisico"> Físico</label>
                            <label><input type="checkbox" value="Digital" data-editar-estado="Digital"> Digital</label>
                        </div>
                    </div>

                </div>

            </div>

            <!-- ===================== -->
            <!-- TARJETA 2 -->
            <!-- ===================== -->
            <div class="admin-dashboard__form-card">

                <div class="admin-dashboard__form-section-title">
                    Archivos y recursos
                </div>

                <label>Link del archivo (tesis):</label>
                <input type="text" id="editarUrl" placeholder="Pega la url aquí">

                <div class="admin-dashboard__form-files">

                    <div class="admin-dashboard__file-box">
                        <span>Pasta física:</span>
                        <input type="file" id="editarPastaFisicaInput" accept="image/*" hidden>
                        <button type="button" id="editarBtnPastaFisica" class="btn-subir">
                            Conservar imagen
                        </button>
                    </div>

                    <div class="admin-dashboard__file-box">
                        <span>Portada institucional:</span>
                        <input type="file" id="editarPortadaInstitucionalInput" accept="image/*" hidden>
                        <button type="button" id="editarBtnPortadaInstitucional" class="btn-subir">
                            Conservar imagen
                        </button>
                    </div>

                </div>

            </div>

            <!-- ===================== -->
            <!-- BOTONES -->
            <!-- ===================== -->
            <div class="admin-dashboard__form-actions">
                <button type="button" class="btn-cancelar" onclick="mostrarVistaLista()">Cancelar</button>
                <button type="button" class="btn-guardar" onclick="actualizarTesis()">Actualizar</button>
            </div>

        </div>

    </div>

</div>