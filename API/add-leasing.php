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

// REGISTRA VALOR BIEN
$id_valorbien = insert('valorbien', $_POST['valorbien']);
// REGISTRA PLAZO PAGO
$id_plazopago = insert('plazopago', $_POST['plazopago']);
// REGISTRA TASA
$id_tasa = insert('tasa', $_POST['tasa']);

// REGISTRA TASA LEASING
$_tasaleasing = $_POST['tasaleasing'];
$_tasaleasing['id_tasa'] = 3;//$id_tasa;
$id_tasaleasing = insert('tasaleasing', $_tasaleasing);

// REGISTRA PAGOS PORCENTUALES
$id_pagosporcentuales = insert('pagosporcentuales', $_POST['pagosporcentuales']);

// REGISTRA LEASING
$_leasing = array(
    'id_usuario' => $_SESSION['id'],
    'id_moneda' => $_SESSION['id_moneda'],
    'id_valorbien' => $id_valorbien,
    'id_plazopago' => $id_plazopago,
    'id_tasaleasing' => $id_tasaleasing,
    'id_pagosporcentulaes' => $id_pagosporcentuales
);

$id_leasing = insert('leasing', $_leasing);

// REGISTRA TODOS LOS PAGOS INICIALES Y POR PERIODOS
foreach ($_POST['pagosprevios'] as $pagoprevio) {
    $pagoprevio['id_leasing'] = $id_leasing;
    $pagoprevio['desembolso'] = $pagoprevio['desembolso'] == 'Agregar al Préstamo' ? 'PR' : 'EF';
    insert('pagoprevio', $pagoprevio);
}

// CÁLCULOS PARA REGISTRO DE CUOTAS

?>