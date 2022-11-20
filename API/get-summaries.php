<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: ../index.php');
	exit;
}

include './DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$sql = "SELECT * FROM resumenleasing WHERE id_usuario = '".$_SESSION['id']."'";
$stmt = $conn->prepare($sql);
$stmt->execute(); 
$operaciones = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$sql = "SELECT simbolo FROM moneda WHERE id  = ( SELECT id_moneda FROM leasing WHERE id ='".$row["id_leasing"]."')";
	$snd = $conn->prepare($sql);
	$snd->execute();
	$row['moneda'] = $snd->fetch(PDO::FETCH_ASSOC)['simbolo'];
    $operaciones[] = $row;
}

echo json_encode($operaciones);