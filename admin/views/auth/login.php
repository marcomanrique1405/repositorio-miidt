<?php 
declare(strict_types=1);

$base = defined('ADMIN_BASE')
    ? ADMIN_BASE
    : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

$base = ($base === '/' ? '' : $base);

$error = $error ?? '';
$usernameValue = $usernameValue ?? '';
$csrfToken = $csrfToken ?? ($_SESSION['csrf_token'] ?? '');

/*
    Si hay error, no dejamos que PHP vuelva a pintar el usuario.
    Esto ayuda a que se borre junto con la contraseña.
*/
$usernameValueMostrar = !empty($error) ? '' : $usernameValue;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Login</title>
    <link rel="icon" href="<?= htmlspecialchars($base) ?>/assets/img/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/vendor/bootstrap/css/bootstrap.min.css"> -->
    <link rel="stylesheet" href="<?= htmlspecialchars($base) ?>/assets/css/auth/style.css">
</head>
<body class="admin-login">
<main class="admin-login__wrap">
    <div class="container">
        <div class="admin-login__panel mx-auto">
            <div class="admin-login__brand">
                <img
                    src="<?= htmlspecialchars($base) ?>/assets/img/logo.webp"
                    class="admin-login__brand-img"
                    alt="UAGro"
                >
            </div>

            <?php if (!empty($error)) : ?>
                <div class="admin-login__alert" role="alert">
                    <?= htmlspecialchars((string)$error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= htmlspecialchars($base) ?>/index.php/login" class="admin-login__form" autocomplete="off">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars((string)$csrfToken) ?>"
                >

                <div class="admin-login__field">
                    <label for="username" class="admin-login__label">Usuario:</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="admin-login__input"
                        value="<?= htmlspecialchars((string)$usernameValueMostrar) ?>"
                        maxlength="80"
                        autocomplete="new-password"
                        autocapitalize="off"
                        spellcheck="false"
                        required
                    >
                </div><br>

                <div class="admin-login__field">
                    <label for="password" class="admin-login__label">Contraseña:</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="admin-login__input"
                        value=""
                        maxlength="255"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit" class="admin-login__btn">
                    Iniciar sesión
                </button>

            </form>

            <div class="admin-login__divider"></div>
        </div>
    </div>
</main>

<script>
function limpiarCamposLogin() {
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');

    if (usernameInput) {
        usernameInput.value = '';
        usernameInput.setAttribute('value', '');
    }

    if (passwordInput) {
        passwordInput.value = '';
        passwordInput.setAttribute('value', '');
    }
}

const usernameInput = document.getElementById('username');

if (usernameInput) {
    usernameInput.addEventListener('input', () => {
        usernameInput.value = usernameInput.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñÜü]/g, '');
    });
}

/*
    Si hay alerta de error, se limpian ambos campos.
    Se repite varias veces porque Brave/Chrome a veces autocompleta
    después de que el DOM ya cargó.
*/
document.addEventListener('DOMContentLoaded', () => {
    const alertLogin = document.querySelector('.admin-login__alert');

    if (alertLogin) {
        limpiarCamposLogin();

        setTimeout(limpiarCamposLogin, 50);
        setTimeout(limpiarCamposLogin, 200);
        setTimeout(limpiarCamposLogin, 500);
        setTimeout(limpiarCamposLogin, 1000);
    }

    setTimeout(() => {
        const alert = document.querySelector('.admin-login__alert');
        if (alert) alert.remove();
    }, 4000);
});
</script>

</body>
</html>