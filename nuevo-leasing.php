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
         --><a class="active" href="#">Nuevo Leasing</a><!--
         --><a href="configuracion.php">Configuración</a><!--
         --><a href="API/login.php?action=logout"><i class="bi bi-power"></i></a>
        </nav>
    </header>

    <form id="leasing-form" class="container max">
        <section class="title">
            <h2>Ingrese los datos en los campos</h2>
        </section>
        <!-- DATOS DEL PRESTAMO -->
        <section class="leasing-group">
            <h3>Datos del préstamo</h3>
            <div class="inline-input dotted">
                <div class="text-select">
                    <label for="PV">Precio de Venta</label>
                    <div class="input">
                        <input id="PV" name="PV" type="text" required>
                        <select name="PV-moneda" id="PV-moneda">
                            <option value="USD" <?php if ($_SESSION['moneda'] == 'USD') echo 'selected'; ?>>USD</option>
                            <option value="PEN" <?php if ($_SESSION['moneda'] == 'PEN') echo 'selected'; ?> >PEN</option>
                        </select>
                    </div>
                </div>
                <div class="text-select">
                    <label for="CI">Cuota Inicial</label>
                    <div class="input">
                        <input id="CI" name="CI" type="text" required>
                        <select name="CI-medida" id="CI-medida">
                            <option value="P">%</option>
                            <option value="E">$</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="inline-input dotted breath">
                <div class="text-select">
                    <label for="num-pagos">Número de Pagos</label>
                    <div class="input">
                        <input id="num-pagos" name="num-pagos" type="text" required>
                        <select name="num-pagos-unit" id="num-pagos-unit">
                            <option value="C">CUOTAS</option>
                            <option value="A">AÑOS</option>
                        </select>
                    </div>
                </div>
                <div class="text-select">
                    <label for="frec-pago">Frecuencia de Pago</label>
                    <div class="input">
                        <select class="full" name="frec-pago" id="frec-pago">
                            <option value="1">DIARIO</option>
                            <option value="15">QUINCENAL</option>
                            <option value="30">MENSUAL</option>
                            <option value="60">BIMESTRAL</option>
                            <option value="90">TRIMESTRAL</option>
                            <option value="120">CUATRIMESTRAL</option>
                            <option value="180">SEMESTRAL</option>
                            <option value="360">ANUAL</option>
                        </select>
                    </div>
                </div>
                <div class="text-select">
                    <label for="dias-anio">Días por año</label>
                    <div class="input">
                        <input id="dias-anio" name="dias-anio" type="text" required>
                    </div>
                </div>
            </div>
        </section>
        <!-- DATOS DE PAGOS INICIALES -->
        <section class="leasing-group">
            <h3>Datos de los Pagos Iniciales</h3>
            <div class="inline-input dotted">
                <div class="text-select">
                    <label for="monto">Monto y Concepto</label>
                    <div class="input">
                        <input id="monto" name="monto" type="text">
                        <select name="concepto" id="concepto">
                            <option value="Costes Notariales">Costes Notariales</option>
                            <option value="Costes Registrales">Costes Registrales</option>
                            <option value="Tasación">Tasación</option>
                            <option value="Estudio de Títulos">Estudio de Títulos</option>
                            <option value="Otros Costes">Otros Costes</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="inline-input dotted">
                <table>
                    <thead>
                        <th>Monto</th>
                        <th>Concepto</th>
                        <th></th>
                    </thead>
                    <tbody id="pagos-iniciales">

                    </tbody>
                </table>
            </div>
            <div class="inline-input dotted btn-container">
                <button onclick="agregarPagoInicial()" type="button" class="btn btn-primary">Agregar Pago Inicial</button>
            </div>
        </section>
        <!-- DATOS DE LOS PAGOS POR PERIODO -->
        <section class="leasing-group">
            <h3>Datos de los Pagos por Periodo</h3>
            <div class="inline-input dotted">
                <div class="text-select">
                    <label for="monto-por-periodo">Monto y Concepto</label>
                    <div class="input">
                        <input id="monto-por-periodo" name="monto-por-periodo" type="text">
                        <select name="concepto-por-periodo" id="concepto-por-periodo">
                            <option value="Portes">Portes</option>
                            <option value="Gastos Administrativos">Gastos Administrativos</option>
                            <option value="Comisión">Comisión</option>
                            <option value="Penalidad">Penalidad</option>
                            <option value="Comunicación">Comunicación</option>
                            <option value="Seguridad">Seguridad</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="inline-input dotted">
                <table>
                    <thead>
                        <th>Monto</th>
                        <th>Concepto</th>
                        <th></th>
                    </thead>
                    <tbody id="pagos-periodo">

                    </tbody>
                </table>
            </div>
            <div class="inline-input dotted btn-container">
                <button onclick="agregarPagoPorPeriodo()" type="button" class="btn btn-primary">Agregar Pago por Periodo</button>
            </div>
        </section>
        <!-- DATOS DE LOS SEGUROS -->
        <section class="leasing-group">
            <h3>Datos de los Seguros</h3>
            <div class="inline-input dotted">
                <div class="text-select">
                    <label for="seg-desgravamen">Seguro de Desgravamen</label>
                    <div class="input">
                        <input id="seg-desgravamen" name="seg-desgravamen" type="text" required>
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
                <div class="text-select">
                    <label for="seg-riesgo">Seguro de Riesgo</label>
                    <div class="input">
                        <input id="seg-riesgo" name="seg-riesgo" type="text" required>
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
            </div>
        </section>
        <!-- DATOS DE LAS TASAS -->
        <section class="leasing-group">
            <h3>Datos de las Tasas</h3>
            <div class="inline-input dotted">
                <?php if ($_SESSION['tipo_tasa'] == 'E'): ?>
                    <div class="text-select">
                        <label for="tasa-leasing">Tasa Efectiva Anual</label>
                        <div class="input">
                            <input id="tasa-leasing" name="tasa-leasing" type="text" required>
                            <i class="bi bi-percent"></i>
                        </div>
                    </div>
                    <input type="hidden" name="capitalizacion" id="capitalizacion" value="">
                <?php else: ?>
                    <div class="text-select">
                        <label for="tasa-leasing">Tasa Nominal Anual</label>
                        <div class="input">
                            <input id="tasa-leasing" name="tasa-leasing" type="text" required>
                            <i class="bi bi-percent"></i>
                        </div>
                    </div>
                    <div class="text-select">
                        <label for="capitalizacion">Periodo de Capitalización</label>
                        <div class="input">
                            <select class="full" name="capitalizacion" id="capitalizacion">
                                <option value="1">DIARIO</option>
                                <option value="15">QUINCENAL</option>
                                <option value="30">MENSUAL</option>
                                <option value="60">BIMESTRAL</option>
                                <option value="90">TRIMESTRAL</option>
                                <option value="120">CUATRIMESTRAL</option>
                                <option value="180">SEMESTRAL</option>
                                <option value="360">ANUAL</option>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="text-select">
                    <label for="wacc">Tasa de Descuento</label>
                    <div class="input">
                        <input id="wacc" name="wacc" type="text" required>
                        <i class="bi bi-percent"></i>
                    </div>
                </div>
            </div>
        </section>
        
        <div style="margin: 96px 0 24px;" class="btn-container">
            <button class="btn btn-primary">Generar Nuevo Leasing<br>Financiero</button>
        </div>
    </form>

    <section style="display: none" id="results" class="results-container">
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
            <button onclick="guardarOperacion()" class="btn btn-primary">Registrar Operación</button>
        </div>
    </section>

    <div style="display: none;" id="nots" class="">
        <i class=""></i>
        <span></span>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        var monedaUsuario = '<?= $_SESSION['moneda'] ?>';
        var tipoTasaUsuario = '<?= $_SESSION['tipo_tasa'] ?>';
    </script>
    <script src="js/hb-simple-nots.js"></script>
    <script src="js/finance.js"></script>
    <script src="js/new-leasing.js"></script>
</body>
</html>