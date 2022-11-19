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
            <a class="active" href="dashboard.php">Dashboard</a><!--
         --><a href="">Pagos</a><!--
         --><a href="">Configuración</a><!--
         --><a href="API/login.php?action=logout"><i class="bi bi-power"></i></a>
        </nav>
    </header>

    <div class="container max">
        <section class="title">
            <h2>Ingrese los datos en los campos</h2>
        </section>
        <section class="leasing-group">
            <h3>Datos del valor del bien</h3>
            <div class="inline-input dotted">
                <div class="text-select">
                    <label for="PV">Precio de Venta</label>
                    <div class="input">
                        <input id="PV" name="PV" type="text">
                        <select name="PV-moneda" id="PV-moneda">
                            <option value="USD">USD</option>
                            <option value="PEN">PEN</option>
                        </select>
                    </div>
                </div>
                <div class="text-select">
                    <label for="CI">Cuota Inicial</label>
                    <div class="input">
                        <input id="CI" name="CI" type="text">
                        <select name="CI-medida" id="CI-medida">
                            <option value="porcentaje">%</option>
                            <option value="efectivo">$</option>
                        </select>
                    </div>
                </div>
                <div class="text-select">
                    <label for="opcion-compra">Opción de Compra</label>
                    <div class="input">
                        <input id="opcion-compra" name="opcion-compra" type="text">
                        <select name="moneda-opcion-compra" id="moneda-opcion-compra">
                            <option value="USD">USD</option>
                            <option value="PEN">PEN</option>
                        </select>
                    </div>
                </div>
            </div>
        </section>
        <section class="leasing-group">
            <h3>Plazo de pago</h3>
            <div class="inline-input dotted">
                <div class="text-select">
                    <label for="num-pagos">Número de Pagos</label>
                    <div class="input">
                        <input id="num-pagos" name="num-pagos" type="text">
                        <select name="num-pagos-unit" id="num-pagos-unit">
                            <option value="cuotas">CUOTAS</option>
                            <option value="años">AÑOS</option>
                        </select>
                    </div>
                </div>
                <div class="text-select">
                    <label for="frec-pago">Frecuencia de Pago</label>
                    <div class="input">
                        <input id="frec-pago" name="frec-pago" type="text">
                    </div>
                </div>
                <div class="text-select">
                    <label for="fecha-inicio-prestamo">Fecha de Inicio del Préstamo</label>
                    <div class="input">
                        <input id="fecha-inicio-prestamo" name="fecha-inicio-prestamo" type="date">
                    </div>
                </div>
            </div>
            <div class="inline-input dotted">
                <div class="text-select third">
                    <label for="fecha-primer-pago">Fecha del Primer Pago</label>
                    <div class="input">
                        <input id="fecha-primer-pago" name="fecha-primer-pago" type="date">
                    </div>
                </div>
            </div>
        </section>
        <section class="leasing-group">
            <h3>Tasas</h3>
            <div class="inline-input dotted">
                <div class="text-select">
                    <label for="TEA">Tasa Efectiva Anual</label>
                    <div class="input">
                        <input id="TEA" name="TEA" type="text">
                    </div>
                </div>
                <div class="text-select">
                    <label for="dias-anio">Días por año</label>
                    <div class="input">
                        <input id="dias-anio" name="dias-anio" type="text">
                    </div>
                </div>
                <div class="text-select">
                    <label for="ks">Ks</label>
                    <div class="input">
                        <input id="ks" name="ks" type="text">
                    </div>
                </div>
            </div>
            <div class="inline-input dotted">
                <div class="text-select third centered">
                    <label for="WACC">Tasa de Descuento W.A.C.C.</label>
                    <div class="input">
                        <input id="WACC" name="WACC" type="text">
                    </div>
                </div>
            </div>
        </section>
        <section class="leasing-group">
            <h3>Pagos Porcentuales</h3>
            <div class="inline-input dotted">
                <div class="text-select">
                    <label for="activacion">Activación</label>
                    <div class="input">
                        <input id="activacion" name="activacion" type="text">
                        <select name="activacion-unit" id="activacion-unit">
                            <option value="porcentaje">%</option>
                            <option value="efectivo">$</option>
                        </select>
                    </div>
                </div>
                <div class="text-select">
                    <label for="seg-riesgo">Seguro de Riesgo</label>
                    <div class="input">
                        <input id="seg-riesgo" name="seg-riesgo" type="text">
                        <select name="seg-riesgo-frec" id="seg-riesgo-frec">
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
                    <label for="IV">Impuesto a la venta</label>
                    <div class="input">
                        <input id="IV" name="IV" type="text">
                    </div>
                </div>
            </div>
            <div class="inline-input dotted">
                <div class="text-select third centered">
                    <label for="IR">Impuesto a la renta</label>
                    <div class="input">
                        <input id="IR" name="IR" type="text">
                    </div>
                </div>
            </div>
        </section>
        <section class="leasing-group">
            <h3>Pagos Iniciales</h3>
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
                <div class="text-select">
                    <label for="desembolso">Desembolso</label>
                    <div class="input">
                        <select class="full" name="desembolso" id="desembolso">
                            <option value="Pagado en Efectivo">Pagado en Efectivo</option>
                            <option value="Agregar al Préstamo">Agregar al Préstamo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="inline-input dotted">
                <table>
                    <tbody>
                        <tr id="pagos-iniciales">

                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="inline-input dotted btn-container">
                <button class="btn btn-primary">Agregar Pago Inicial</button>
            </div>
        </section>
        <section class="leasing-group">
            <h3>Pagos por Periodo</h3>
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
                    <tbody>
                        <tr id="pagos-periodo">

                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="inline-input dotted btn-container">
                <button class="btn btn-primary">Agregar Pago por Periodo</button>
            </div>
        </section>
        <div style="margin: 96px 0 24px;" class="btn-container">
            <button class="btn btn-primary">Generar Nuevo Leasing<br>Financiero</button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="js/dashboard.js"></script>
</body>
</html>