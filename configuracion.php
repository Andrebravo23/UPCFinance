<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>UPC Finance | Dashboard</title>
</head>
<body>
    <header>
        <h1>UPC Finance</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a><!--
         --><a href="nuevo-leasing.php">Nuevo Leasing</a><!--
         --><a class="active" href="#">Configuración</a><!--
         --><a href="API/login.php?action=logout"><i class="bi bi-power"></i></a>
        </nav>
    </header>
    <div class="container">
        <form id="configuration">
            <section class="title">
                <h2>Configuraciones de la Cuenta</h2>
            </section>
            <section class="inline-input">
                <div class="custom-input">
                    <i class="bi bi-envelope"></i>
                    <div>
                        <label for="email">Ingresar Correo</label>
                        <input type="email" name="email" id="email" autocomplete="off" value="<?=$_SESSION['correo']?>" required>
                    </div>
                </div>
                <div class="custom-input">
                    <i class="bi bi-person"></i>
                    <div>
                        <label for="name">Ingresar Nombre</label>
                        <input type="text" name="name" id="name" autocomplete="off" value="<?=$_SESSION['nombre']?>" required>
                    </div>
                </div>
                <div class="custom-input">
                    <i class="bi bi-person"></i>
                    <div>
                        <label for="last-name">Ingresar Apellido</label>
                        <input type="text" name="last-name" id="last-name" autocomplete="off" value="<?=$_SESSION['apellido']?>" required>
                    </div>
                </div>
            </section>
            <section class="inline-input">
                <div class="custom-input">
                    <i class="bi bi-lock"></i>
                    <div>
                        <label for="current-password">Ingresar Contraseña</label>
                        <input type="password" name="current-password" id="current-password" autocomplete="off" required>
                    </div>
                </div>
                <div class="custom-input">
                    <i class="bi bi-lock"></i>
                    <div>
                        <label for="new-password">Ingresar Nueva Contraseña</label>
                        <input type="password" name="new-password" id="new-password" autocomplete="off" required>
                    </div>
                </div>
                <div class="custom-input">
                    <i class="bi bi-lock"></i>
                    <div>
                        <label for="new-password-repeat">Repetir Nueva Contraseña</label>
                        <input type="password" name="new-password-repeat" id="new-password-repeat" autocomplete="off" required>
                    </div>
                </div>
            </section>
            <section class="title">
                <h2>Preferencias</h2>
            </section>
            <section class="inline-input">
                <div class="custom-input">
                    <i class="bi bi-envelope"></i>
                    <div>
                        <label for="moneda">Seleccionar Moneda</label>
                        <select name="moneda" id="moneda">
                            <option value="PEN" <?php if ($_SESSION['moneda'] == 'PEN') echo 'selected'; ?>>Sol Peruano (PEN)</option>
                            <option value="USD" <?php if ($_SESSION['moneda'] == 'USD') echo 'selected'; ?>>Dólar Americano (USD)</option>
                        </select>
                    </div>
                </div>
                <div class="custom-input">
                    <i class="bi bi-envelope"></i>
                    <div>
                        <label for="tipo_tasa">Seleccionar Tipo de Tasa</label>
                        <select name="tipo_tasa" id="tipo_tasa">
                            <option value="E" <?php if ($_SESSION['tipo_tasa'] == 'E') echo 'selected'; ?>>Efectiva</option>
                            <option value="N" <?php if ($_SESSION['tipo_tasa'] == 'N') echo 'selected'; ?>>Nominal</option>
                        </select>
                    </div>
                </div>
            </section>
            <div style="margin: 24px 0 24px;" class="btn-container">
                <button style="margin: 0 0 0 auto" class="btn btn-primary" disabled>Guardar cambios</button>
            </div>
        </form>
    </div>
</body>
</html>