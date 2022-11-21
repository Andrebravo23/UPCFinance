<?php
session_start();

if (!isset($_SESSION['loggedin'])) {
	header('Location: index.php');
	exit;
}

include './API/DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

function selectById($conn, $tableName, $condition, $asArray = false) {
    $sql = "SELECT * FROM $tableName WHERE $condition";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    if ($asArray) {
        $rows = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    } else {
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$leasing = selectById($conn, 'leasing', "id = ".$_GET['id']);
$prestamo = selectById($conn, 'prestamo', "id = ".$leasing['id_prestamo']);
$pagosprevios = selectById($conn, 'pagosprevios', "id_leasing = ".$_GET['id'], true);
$seguros = selectById($conn, 'seguros', "id = ".$leasing['id_seguros']);
$tasaleasing = selectById($conn, 'tasaleasing', "id = ".$leasing['id_tasaleasing']);
$tasa = selectById($conn, 'tasa', "id = ".$tasaleasing['id_tasa']);
$moneda = selectById($conn, 'moneda', "id = ".$leasing['id_moneda']);

$allData = json_encode([
    'prestamo' => $prestamo, 
    'pagosprevios' => $pagosprevios, 
    'seguros' => $seguros, 
    'tasaleasing' => $tasaleasing, 
    'tasa' => $tasa,
    'moneda' => $moneda
]);

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
            <a class="active" href="dashboard.php">Dashboard</a><!--
         --><a href="nuevo-leasing.php">Nuevo Leasing</a><!--
         --><a href="configuracion.php">Configuración</a><!--
         --><a href="API/login.php?action=logout"><i class="bi bi-power"></i></a>
        </nav>
    </header>

    <section id="results" class="results-container">
        <div class="results-menu">
            <h3>Resultados de la Operación:</h3>
            <div class="results-btns">
                <button onclick="toggleSummary()" id="show-summary" class="btn btn-primary">Resumen</button><!--
             --><button onclick="toggleSummary()" id="show-table" class="btn btn-primary disabled">Flujo</button>
            </div>
        </div>

        <div id="summary">
            <table style="width: 100%" id="results-summary" class="display">
                <thead>
                    <th>Descripción</th>
                    <th>Unidad</th>
                    <th>Monto</th>
                </thead>

            </table>
        </div>

        <div id="fees" style="display:none">
            <table id="fees-table" class="display">
                <thead>
                    <th>N°</th>
                    <th>TEA</th>
                    <th>TEP</th>
                    <th>Saldo Inicial</th>
                    <th>Interés</th>
                    <th>Cuota</th>
                    <th>Amortización</th>
                    <th>Seg. Desgravamen</th>
                    <th>Seg. Riesgo</th>
                    <th>Gastos Periódicos</th>
                    <th>Saldo Final</th>
                    <th>Flujo</th>
                </thead>

            </table>
        </div>

        <div style="margin: 64px 0 24px;" class="btn-container">
            <a href="dashboard.php" class="btn btn-primary">Volver al dashboard</a>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        var leasingData = <?= $allData ?>;
    </script>
    <script src="js/finance.js"></script>
    <script src="js/leasing-details.js"></script>
    <script>
        generateSummary(leasingData);
    </script>
</body>
</html>