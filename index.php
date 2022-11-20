<?php
session_start();

if (isset($_SESSION['loggedin'])) {
	header('Location: dashboard.php');
	exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <title>UPC Finance</title>
</head>
<body class="flex-body">
    <header>
        <h1>UPC Finance</h1>
    </header>
    
    <form id="log-in">
        <div class="login-card">
            <h3 class="title">Iniciar sesión</h3>
            <div class="custom-input">
                <i class="bi bi-envelope"></i>
                <div>
                    <label for="email">Ingresar Correo</label>
                    <input type="email" name="email" id="email" autocomplete="off" required>
                </div>
            </div>
            <div class="custom-input">
                <i class="bi bi-lock"></i>
                <div>
                    <label for="password">Ingresar Contraseña</label>
                    <input type="password" name="password" id="password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            <button type="reset" onclick="toggleRegister()" class="btn btn-primary">¿Aún no tienes una cuenta? Regístrate</button>
        </div>
    </form>

    <form id="register">
        <div class="login-card">
            <h3 class="title">Registrarse</h3>
            <div class="custom-input">
                <i class="bi bi-envelope"></i>
                <div>
                    <label for="email-register">Ingresar Correo</label>
                    <input type="email" name="email-register" id="email-register" autocomplete="off" required>
                </div>
            </div>
            <div class="inline-input">
                <div class="custom-input">
                    <i class="bi bi-person"></i>
                    <div>
                        <label for="name">Ingresar Nombre</label>
                        <input type="text" name="name" id="name" autocomplete="off" required>
                    </div>
                </div>
                <div class="custom-input">
                    <i class="bi bi-person"></i>
                    <div>
                        <label for="last-name">Ingresar Apellido</label>
                        <input type="text" name="last-name" id="last-name" autocomplete="off" required>
                    </div>
                </div>
            </div>
            <div class="custom-input">
                <i class="bi bi-lock"></i>
                <div>
                    <label for="password-register">Ingresar Contraseña</label>
                    <input type="password" name="password-register" id="password-register" required>
                </div>
            </div>
            <div class="custom-input">
                <i class="bi bi-lock"></i>
                <div>
                    <label for="repeat-password">Repetir Contraseña</label>
                    <input type="password" name="repeat-password" id="repeat-password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Registrar</button>
            <button type="reset" onclick="toggleRegister()" class="btn btn-primary">¿Ya tienes una cuenta? Inicia sesión</button>
        </div>
    </form>

    <div style="display: none;" id="nots" class="">
        <i class=""></i>
        <span>Se registró tu cuenta</span>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="js/hb-simple-nots.js"></script>
    <script src="js/main.js"></script>
</body>
</html>