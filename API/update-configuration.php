<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.php');
	exit;
}

$user_data = $_POST['usuario'];
$user_config = $_POST['configuracion'];

if ($user_data['newpassword'] != '') {
    $sql = "SELECT contrasena FROM usuario WHERE id = '".$_SESSION['id']."'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($user_data['password'], $usuario['contrasena'])) {
        echo json_encode([
            'result' => 0,
            'message' => 'La contraseña no es correcta'
        ]);
        die;
    } else {
        $user_data['contrasena'] = password_hash($user_data['newpassword'], 1);
    }
}

function executeUpdate($conn, $table, $data_array, $id) {
    $updates = [];
    foreach ($data_array as $key => $value) {
        $updates[] = "$key = '$value'";
    }
    $updates = implode(', ', $updates);
    $sql = "UPDATE $table SET $updates WHERE id = '$id';";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    } catch (\Throwable $th) {
        echo json_encode([
            'result' => 0,
            'message' => 'Ha ocurrido un problema'
        ]);
    }
}

unset($user_data['password']);
unset($user_data['newpassword']);
unset($user_data['newpasswordrepeat']);

executeUpdate($conn, 'usuario', $user_data, $_SESSION['id']);

$_SESSION['nombre'] = $user_data['nombres'];
$_SESSION['apellido'] = $user_data['apellidos'];

executeUpdate($conn, 'configuracion', $user_config, $_SESSION['id_configuracion']);

$_SESSION['tipo_tasa'] = $user_config['tipo_tasa'];
$_SESSION['id_moneda'] = $user_config['id_moneda'];

$sql = "SELECT simbolo FROM moneda WHERE id = '".$user_config['id_moneda']."'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$_SESSION['moneda'] = $stmt->fetch(PDO::FETCH_ASSOC)['simbolo'];

echo json_encode([
    'result' => 1
]);