document.addEventListener('DOMContentLoaded', () => {

    /* ==========================================
       AUTOR - AGREGAR TESIS
       Este bloque queda igual al que ya te funciona
    ========================================== */

    const inputAutor = document.getElementById('autor');

    if (inputAutor) {
        let popup = null;

        function cerrarPopupAutor() {
            if (popup) {
                popup.remove();
                popup = null;
            }
        }

        function setHiddenValue(id, value) {
            const input = document.getElementById(id);
            if (input) {
                input.value = value;
            }
        }

        function abrirPopupAutor() {
            cerrarPopupAutor();

            const tarjetaDatos = inputAutor.closest('.admin-dashboard__form-card');
            if (!tarjetaDatos) return;

            popup = document.createElement('div');
            popup.className = 'autor-popup';

            popup.innerHTML = `
                <h3 class="autor-popup__title">Datos personales</h3>

                <label>Nombres:</label>
                <input type="text" id="autorNombre" placeholder="María">

                <label>Apellido paterno:</label>
                <input type="text" id="autorApellidoPaterno" placeholder="López">

                <label>Apellido materno:</label>
                <input type="text" id="autorApellidoMaterno" placeholder="García">

                <label>Sexo:</label>
                <div class="autor-popup__checkboxes">
                    <label><input type="checkbox" name="sexoAutor" value="F"> F</label>
                    <label><input type="checkbox" name="sexoAutor" value="M"> M</label>
                </div>

                <div class="autor-popup__divider"></div>

                <h3 class="autor-popup__title">Datos académicos</h3>

                <label>Matrícula:</label>
                <input type="text" id="autorMatricula" placeholder="12345678">

                <label>Correo institucional:</label>
                <input type="text" id="autorCorreo" placeholder="12345678@uagro.mx">

                <div class="autor-popup__actions">
                    <button type="button" class="autor-popup__cancelar">Cancelar</button>
                    <button type="button" class="autor-popup__guardar">Guardar</button>
                </div>
            `;

            tarjetaDatos.appendChild(popup);

            popup.querySelector('.autor-popup__cancelar').addEventListener('click', cerrarPopupAutor);

            popup.querySelectorAll('input[name="sexoAutor"]').forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (this.checked) {
                        popup.querySelectorAll('input[name="sexoAutor"]').forEach(item => {
                            if (item !== this) item.checked = false;
                        });
                    }
                });
            });

            popup.querySelector('.autor-popup__guardar').addEventListener('click', () => {
                const nombre = document.getElementById('autorNombre').value.trim();
                const apellidoPaterno = document.getElementById('autorApellidoPaterno').value.trim();
                const apellidoMaterno = document.getElementById('autorApellidoMaterno').value.trim();
                const matricula = document.getElementById('autorMatricula').value.trim();
                const correo = document.getElementById('autorCorreo').value.trim();

                const sexoSeleccionado = popup.querySelector('input[name="sexoAutor"]:checked');
                const sexo = sexoSeleccionado ? sexoSeleccionado.value : '';

                if (!nombre || !apellidoPaterno || !matricula || !sexo) {
                    alert('Completa nombre, apellido paterno, matrícula y sexo del autor.');
                    return;
                }

                inputAutor.value = `${nombre} ${apellidoPaterno} ${apellidoMaterno}`.trim();

                setHiddenValue('autorNombreHidden', nombre);
                setHiddenValue('autorApellidoPaternoHidden', apellidoPaterno);
                setHiddenValue('autorApellidoMaternoHidden', apellidoMaterno);
                setHiddenValue('autorMatriculaHidden', matricula);
                setHiddenValue('autorCorreoHidden', correo);
                setHiddenValue('autorSexoHidden', sexo);

                cerrarPopupAutor();
            });
        }

        inputAutor.addEventListener('click', abrirPopupAutor);
        inputAutor.addEventListener('keydown', e => e.preventDefault());
    }

    /* ==========================================
       AUTOR - EDITAR TESIS
       Agregado nuevo sin tocar lo anterior
    ========================================== */

    const inputEditarAutor = document.getElementById('editarAutor');

    if (inputEditarAutor) {
        let popupEditar = null;

        function cerrarPopupEditarAutor() {
            if (popupEditar) {
                popupEditar.remove();
                popupEditar = null;
            }
        }

        function getHiddenValueEditar(id) {
            const input = document.getElementById(id);
            return input ? input.value.trim() : '';
        }

        function setHiddenValueEditar(id, value) {
            const input = document.getElementById(id);
            if (input) {
                input.value = value;
            }
        }

        function escapeAttr(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function abrirPopupEditarAutor() {
            cerrarPopupEditarAutor();

            const tarjetaDatos = inputEditarAutor.closest('.admin-dashboard__form-card');
            if (!tarjetaDatos) return;

            const nombreActual = getHiddenValueEditar('editarAutorNombreHidden');
            const apellidoPaternoActual = getHiddenValueEditar('editarAutorApellidoPaternoHidden');
            const apellidoMaternoActual = getHiddenValueEditar('editarAutorApellidoMaternoHidden');
            const matriculaActual = getHiddenValueEditar('editarAutorMatriculaHidden');
            const correoActual = getHiddenValueEditar('editarAutorCorreoHidden');
            const sexoActual = getHiddenValueEditar('editarAutorSexoHidden');

            popupEditar = document.createElement('div');
            popupEditar.className = 'autor-popup';

            popupEditar.innerHTML = `
                <h3 class="autor-popup__title">Datos personales</h3>

                <label>Nombres:</label>
                <input type="text" id="editarAutorNombre" placeholder="María" value="${escapeAttr(nombreActual)}">

                <label>Apellido paterno:</label>
                <input type="text" id="editarAutorApellidoPaterno" placeholder="López" value="${escapeAttr(apellidoPaternoActual)}">

                <label>Apellido materno:</label>
                <input type="text" id="editarAutorApellidoMaterno" placeholder="García" value="${escapeAttr(apellidoMaternoActual)}">

                <label>Sexo:</label>
                <div class="autor-popup__checkboxes">
                    <label><input type="checkbox" name="editarSexoAutor" value="F" ${sexoActual === 'F' ? 'checked' : ''}> F</label>
                    <label><input type="checkbox" name="editarSexoAutor" value="M" ${sexoActual === 'M' ? 'checked' : ''}> M</label>
                </div>

                <div class="autor-popup__divider"></div>

                <h3 class="autor-popup__title">Datos académicos</h3>

                <label>Matrícula:</label>
                <input type="text" id="editarAutorMatricula" placeholder="12345678" value="${escapeAttr(matriculaActual)}">

                <label>Correo institucional:</label>
                <input type="text" id="editarAutorCorreo" placeholder="12345678@uagro.mx" value="${escapeAttr(correoActual)}">

                <div class="autor-popup__actions">
                    <button type="button" class="autor-popup__cancelar">Cancelar</button>
                    <button type="button" class="autor-popup__guardar">Guardar</button>
                </div>
            `;

            tarjetaDatos.appendChild(popupEditar);

            popupEditar.querySelector('.autor-popup__cancelar').addEventListener('click', cerrarPopupEditarAutor);

            popupEditar.querySelectorAll('input[name="editarSexoAutor"]').forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (this.checked) {
                        popupEditar.querySelectorAll('input[name="editarSexoAutor"]').forEach(item => {
                            if (item !== this) item.checked = false;
                        });
                    }
                });
            });

            popupEditar.querySelector('.autor-popup__guardar').addEventListener('click', () => {
                const nombre = document.getElementById('editarAutorNombre').value.trim();
                const apellidoPaterno = document.getElementById('editarAutorApellidoPaterno').value.trim();
                const apellidoMaterno = document.getElementById('editarAutorApellidoMaterno').value.trim();
                const matricula = document.getElementById('editarAutorMatricula').value.trim();
                const correo = document.getElementById('editarAutorCorreo').value.trim();

                const sexoSeleccionado = popupEditar.querySelector('input[name="editarSexoAutor"]:checked');
                const sexo = sexoSeleccionado ? sexoSeleccionado.value : '';

                if (!nombre || !apellidoPaterno || !matricula || !sexo) {
                    alert('Completa nombre, apellido paterno, matrícula y sexo del autor.');
                    return;
                }

                inputEditarAutor.value = `${nombre} ${apellidoPaterno} ${apellidoMaterno}`.trim();

                setHiddenValueEditar('editarAutorNombreHidden', nombre);
                setHiddenValueEditar('editarAutorApellidoPaternoHidden', apellidoPaterno);
                setHiddenValueEditar('editarAutorApellidoMaternoHidden', apellidoMaterno);
                setHiddenValueEditar('editarAutorMatriculaHidden', matricula);
                setHiddenValueEditar('editarAutorCorreoHidden', correo);
                setHiddenValueEditar('editarAutorSexoHidden', sexo);

                cerrarPopupEditarAutor();
            });
        }

        inputEditarAutor.addEventListener('click', abrirPopupEditarAutor);
        inputEditarAutor.addEventListener('keydown', e => e.preventDefault());
    }

});