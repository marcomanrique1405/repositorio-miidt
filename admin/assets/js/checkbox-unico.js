document.addEventListener('DOMContentLoaded', () => {

    // ===== LIES (solo uno) =====
    document.querySelectorAll('.filtro-lies').forEach(checkbox => {
        checkbox.addEventListener('click', function () {

            document.querySelectorAll('.filtro-lies').forEach(el => {
                el.checked = false;
            });

            this.checked = true;
        });
    });

    // ===== ESTADO (solo uno) =====
    document.querySelectorAll('.filtro-estado').forEach(checkbox => {
        checkbox.addEventListener('click', function () {

            document.querySelectorAll('.filtro-estado').forEach(el => {
                el.checked = false;
            });

            this.checked = true;
        });
    });

});