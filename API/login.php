<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER['REQUEST_METHOD'];
switch($method) {
    case "POST":
        if ($_GET['action'] == 'register')
        {
            $email = $_POST['email-register'];
            $name = $_POST['name'];
            $lastName = $_POST['last-name'];
            $password = password_hash($_POST['password-register'], 1);

            $sql = "INSERT INTO configuracion (id_moneda, tipo_tasa)
                    VALUES (1, 'E')";
            $stmt = $conn->prepare($sql);
            $configuration_id = '';
            try {
                $result = $stmt->execute();
                $configuration_id = $conn->lastInsertId();
            } catch (\Throwable $th) {
                echo json_encode([
                    'result' => 0,
                    'message' => 'Ha habido un error al intentar registrarte'
                ]);
                die;
            }

            $sql = "INSERT INTO usuario (id_configuracion, nombres, apellidos, correo, contrasena)
                    VALUES ('$configuration_id', '$name', '$lastName', '$email', '$password')";
            $stmt = $conn->prepare($sql);
            try {
                $result = $stmt->execute();
                echo json_encode([
                    'result' => 1,
                    'message' => 'Registraste tu cuenta'
                ]);
            } catch (\Throwable $th) {
                $sql_error = $stmt->errorInfo()[1];
                if ($sql_error == 1062) {
                    echo json_encode([
                        'result' => 0,
                        'message' => 'El email ingresado ya fue registrado'
                    ]);
                } else {
                    echo json_encode([
                        'result' => 0,
                        'message' => 'Ha habido un error al intentar registrarte'
                    ]);
                }
            }
        }
        else
        {
            session_start();
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $sql = "SELECT * FROM usuario WHERE correo = '$email'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$usuario) {
                echo json_encode([
                    'result' => 0,
                    'message' => 'Usuario no encontrado'
                ]);
                die;
            }

            if (password_verify($password, $usuario['contrasena'])) {
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['nombre'] = $usuario['nombres'];
                $_SESSION['apellido'] = $usuario['apellidos'];
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['id_configuracion'] = $usuario['id_configuracion'];
                
                $sql = "SELECT id_moneda, tipo_tasa FROM configuracion WHERE id = '".$usuario['id_configuracion']."'";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $configuracion = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $_SESSION['tipo_tasa'] = $configuracion['tipo_tasa'];
                $_SESSION['id_moneda'] = $configuracion['id_moneda'];
                
                $sql = "SELECT simbolo FROM moneda WHERE id = '".$configuracion['id_moneda']."'";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $moneda = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION['moneda'] = $moneda['simbolo'];
                
                echo json_encode([
                    'result' => 1,
                    'message' => 'Sesión iniciada'
                ]);
            } else {
                echo json_encode([
                    'result' => 0,
                    'message' => 'Contraseña incorrecta'
                ]);
            }
        }
        break;
    case "GET": 
        if ($_GET['action'] == 'logout') {
            session_start();
            session_destroy();
	        header('Location: ../index.php');
	        exit;
        }
    default:
        exit('Method not supported');
        break;
}