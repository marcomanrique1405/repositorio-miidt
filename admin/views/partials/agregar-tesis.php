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
                <input type="text" id="autor" placeholder="Nombre del autor">

                <label>Director de tesis:</label>
                <input type="text" id="director" placeholder="Buscar director">

                <div class="admin-dashboard__form-row">

                    <div>
                        <label>Año:</label>
                        <input type="text" id="fechaTesis" placeholder="DD/MM/AAAA" readonly>
                    </div>

                    <div>
                        <label>Línea de Investigación:</label>
                        <div class="admin-checkbox-group">
                            <label><input type="checkbox" value="CSR"> CSR</label>
                            <label><input type="checkbox" value="Geomatica"> Geomática</label>
                            <label><input type="checkbox" value="TIC"> TIC</label>
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
                        <button type="button" class="btn-subir">Subir imagen</button>
                    </div>

                    <div class="admin-dashboard__file-box">
                        <span>Portada institucional:</span>
                        <button type="button" class="btn-subir">Subir imagen</button>
                    </div>

                </div>

            </div>

            <!-- ===================== -->
            <!-- BOTONES -->
            <!-- ===================== -->
            <div class="admin-dashboard__form-actions">
                <button type="button" class="btn-cancelar" onclick="mostrarVistaLista()">Cancelar</button>
                <button type="button" class="btn-guardar" onclick="guardarTesis()">Agregar</button>
            </div>

        </div>

    </div>

</div>