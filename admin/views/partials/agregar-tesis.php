<div id="agregarTesisPanel">

    <div class="admin-dashboard__form-wrapper">

        <!-- HEADER -->
        <div class="admin-dashboard__form-header">
            Alta de tesis
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
                <input type="text" id="titulo" placeholder="Escribe el título">

                <label>Autor:</label>
                <input type="text" id="autor" placeholder="Nombre del autor" readonly>

                <input type="hidden" id="autorMatriculaHidden">
                <input type="hidden" id="autorNombreHidden">
                <input type="hidden" id="autorApellidoPaternoHidden">
                <input type="hidden" id="autorApellidoMaternoHidden">
                <input type="hidden" id="autorCorreoHidden">
                <input type="hidden" id="autorSexoHidden">

                <label>Director de tesis:</label>
                <div class="director-autocomplete-wrap">

                    <span class="director-input-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M9.5 3a6.5 6.5 0 0 1 5.17 10.45l4.44 4.44a1 1 0 0 1-1.42 1.42l-4.44-4.44A6.5 6.5 0 1 1 9.5 3m0 2a4.5 4.5 0 1 0 0 9a4.5 4.5 0 0 0 0-9"/>
                        </svg>
                    </span>

                    <input type="text" id="director" placeholder="Buscar director" autocomplete="off">
                    <input type="hidden" id="idDirector">
                    <input type="hidden" id="idDirectorLinea">
                    <div id="director-sugerencias" class="director-sugerencias"></div>
                </div>

                <div class="admin-dashboard__form-row">

                    <div>
                        <label>Año:</label>
                        <input type="text" id="fechaTesis" placeholder="DD/MM/AAAA" readonly>
                    </div>

                    <div>
                        <label>Línea de Investigación:</label>
                        <div class="admin-checkbox-group">
                            <label><input type="checkbox" value="CSR" data-id-linea="1"> CSR</label>
                            <label><input type="checkbox" value="Geomatica" data-id-linea="3"> Geomática</label>
                            <label><input type="checkbox" value="TIC" data-id-linea="2"> TIC</label>
                        </div>
                    </div>

                    <div>
                        <label>Estado:</label>
                        <div class="admin-checkbox-group">
                            <label><input type="checkbox" value="Fisico"> Físico</label>
                            <label><input type="checkbox" value="Digital"> Digital</label>
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
                <input type="text" id="url" placeholder="Pega la url aquí">

                <div class="admin-dashboard__form-files">

                    <div class="admin-dashboard__file-box">
                        <span>Pasta física:</span>
                        <input type="file" id="pastaFisicaInput" accept="image/*" hidden>
                        <button type="button" id="btnPastaFisica" class="btn-subir">Subir imagen</button>
                    </div>

                    <div class="admin-dashboard__file-box">
                        <span>Portada institucional:</span>
                        <input type="file" id="portadaInstitucionalInput" accept="image/*" hidden>
                        <button type="button" id="btnPortadaInstitucional" class="btn-subir">Subir imagen</button>
                    </div>

                </div>

            </div>

            <!-- ===================== -->
            <!-- BOTONES -->
            <!-- ===================== -->
            <div class="admin-dashboard__form-actions">
                <button type="button" class="btn-cancelar" onclick="cancelarAgregarTesis()">Cancelar</button>
                <button type="button" class="btn-guardar" onclick="guardarTesis()">Agregar</button>
            </div>

        </div>

    </div>

</div>