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

function insert($tablename, $data)
{
    $columns = [];
    $values = [];
    foreach ($data as $key => $value) {
        array_push($columns, $key);
        array_push($values, "'$value'");
    };
    $columns = implode(', ', $columns);
    $values = implode(', ', $values);
    
    $sql = "INSERT INTO $tablename ($columns) VALUES ($values);";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute();
    return $conn->lastInsertId();
}

// REGISTRA PRESTAMO
$prestamo = $_POST['prestamo'];
$id_prestamo = insert('prestamo', $prestamo);

// REGISTRA SEGUROS
$seguros = $_POST['seguros'];
$id_seguros = insert('seguros', $seguros);

// REGISTRA TASA
$tasa = $_POST['tasa'];
$id_tasa = insert('tasa', $tasa);

// REGISTRA TASA LEASING
$tasaleasing = $_POST['tasaleasing'];
$tasaleasing['id_tasa'] = $id_tasa;
$id_tasaleasing = insert('tasaleasing', $tasaleasing);

// REGISTRA LEASING
$leasing = array(
    'id_usuario' => $_SESSION['id'],
    'id_moneda' => $_SESSION['id_moneda'],
    'id_prestamo' => $id_prestamo,
    'id_tasaleasing' => $id_tasaleasing,
    'id_seguros' => $id_seguros
);
$id_leasing = insert('leasing', $leasing);

// REGISTRA TODOS LOS PAGOS INICIALES Y POR PERIODOS
$pagosprevios = $_POST['pagosprevios'];
foreach ($pagosprevios as $pagoprevio) {
    $pagoprevio['id_leasing'] = $id_leasing;
    $pagoprevio['desembolso'] = $pagoprevio['desembolso'] == 'Agregar al Préstamo' ? 'PR' : 'EF';
    insert('pagoprevio', $pagoprevio);
}
?>