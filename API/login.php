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

            $sql = "INSERT INTO usuario (nombres, apellidos, correo, contrasena)
                    VALUES ('$name', '$lastName', '$email', '$password')";
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
                $_SESSION['name'] = $usuario['nombres'];
                $_SESSION['id'] = $usuario['id'];
                
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