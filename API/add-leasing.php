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
$valorbien = $_POST['valorbien'];
//$id_valorbien = insert('valorbien', $valorbien);
// REGISTRA PLAZO PAGO
$plazopago = $_POST['plazopago'];
//$id_plazopago = insert('plazopago', $plazopago);
// REGISTRA TASA
$tasa = $_POST['tasa'];
//$id_tasa = insert('tasa', $tasa);

// REGISTRA TASA LEASING
$tasaleasing = $_POST['tasaleasing'];
$tasaleasing['id_tasa'] = 3;//$id_tasa;
//$id_tasaleasing = insert('tasaleasing', $tasaleasing);

// REGISTRA PAGOS PORCENTUALES
$pagosporcentuales = $_POST['pagosporcentuales'];
//$id_pagosporcentuales = insert('pagosporcentuales', $pagosporcentuales);

// REGISTRA LEASING
/* $leasing = array(
    'id_usuario' => $_SESSION['id'],
    'id_moneda' => $_SESSION['id_moneda'],
    'id_valorbien' => $id_valorbien,
    'id_plazopago' => $id_plazopago,
    'id_tasaleasing' => $id_tasaleasing,
    'id_pagosporcentulaes' => $id_pagosporcentuales
); */

//$id_leasing = insert('leasing', $leasing);

// REGISTRA TODOS LOS PAGOS INICIALES Y POR PERIODOS
$pagosprevios = $_POST['pagosprevios'];
$gastos_iniciales = 0;
foreach ($pagosprevios as $pagoprevio) {
    if ($pagoprevio['tipo'] == 'I') {
        $gastos_iniciales += $pagoprevio['monto'];
    }
   // $pagoprevio['id_leasing'] = $id_leasing;
    $pagoprevio['desembolso'] = $pagoprevio['desembolso'] == 'Agregar al Préstamo' ? 'PR' : 'EF';
//    insert('pagoprevio', $pagoprevio);
}

// CÁLCULOS PARA REGISTRO DE CUOTAS

$saldo_financiar = $valorbien['precio_venta'] - $valorbien['cuota_inicial'];
$monto_prestamo = $saldo_financiar + $gastos_iniciales;
$cuotas_por_anio = $tasa['dias_anio'] / $plazopago['frecuencia'];
$total_cuotas = $plazopago['unidad'] == 'A' ? $plazopago['num_pagos'] * $cuotas_por_anio : $plazopago['num_pagos'];
$porcentaje_seguro_desgravamen = $pagosporcentuales['activacion'] * $plazopago['frecuencia'] / 30;
$seguro_riesgo = $pagosporcentuales['seguro_riesgo'] * $valorbien['precio_venta'] / ($cuotas_por_anio * 100);

$result = [
    'saldo_financiar' => $saldo_financiar,
    'monto_prestamo' => $monto_prestamo,
    'cuotas_por_anio' => $cuotas_por_anio,
    'total_cuotas' => $total_cuotas,
    'porcentaje_seguro_desgravamen' => $porcentaje_seguro_desgravamen,
    'seguro_riesgo' => $seguro_riesgo
];

print_r($result);
?>