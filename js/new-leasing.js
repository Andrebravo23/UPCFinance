// UTILITARIOS ***********************************************************************************************
function ajustaTipoCambio(moneda, monto) {
    if (moneda != monedaUsuario) {
        return monedaUsuario == 'PEN' ? parseFloat(monto) * 3.89 : parseFloat(monto) / 3.89;
    } else {
        return parseFloat(monto);
    }
}

// DATOS DEL PRÉSTAMO ***************************************************************************************
const precioVenta = $('#PV');
const monedaPrecioVenta = $('#PV-moneda');
const cuotaInicial = $('#CI');
const cuotaInicialMedida = $('#CI-medida');
const numeroPagos = $('#num-pagos');
const numeroPagosUnidad = $('#num-pagos-unit');
const frecuenciaPago = $('#frec-pago');
const diasAnio = $('#dias-anio');

function getDataPrestamo() {
    let PV = ajustaTipoCambio(monedaPrecioVenta.val(), precioVenta.val());
    let CI = cuotaInicialMedida.val() == 'P' ? PV * parseFloat(cuotaInicial.val()) / 100 : parseFloat(cuotaInicial.val());

    return {
        'precio_venta': PV,
        'cuota_inicial': CI,
        'num_pagos': parseInt(numeroPagos.val()),
        'unidad': numeroPagosUnidad.val(),
        'frecuencia': parseInt(frecuenciaPago.val()),
        'dias_anio': parseInt(diasAnio.val())
    }
}

// DATOS DE LOS PAGOS INICIALES *****************************************************************************
const tablePagosIniciales = $('#pagos-iniciales');
const montoPagoInicial = $('#monto');
const conceptoPagoInicial = $('#concepto')
let pagosIniciales = [];

function agregarPagoInicial() {
    console.log(montoPagoInicial.val());
    if (!montoPagoInicial.val()) {
        return;
    }

    let newPago = {
        "monto": parseFloat(montoPagoInicial.val()).toFixed(2),
        "concepto": conceptoPagoInicial.val()
    }
    pagosIniciales.push(newPago);
    let index = pagosIniciales.length - 1;
    tablePagosIniciales.append(`<tr style="display: none;" id="pago-inicial-${index}">
                                    <td>${newPago.monto}</td>
                                    <td>${newPago.concepto}</td>
                                    <td><button onclick="eliminarPagoInicial(${index})" type="button" class="btn btn-primary"><i class="bi bi-trash"></i></button></td>
                                </tr>`);
    console.log(pagosIniciales);
    $(`#pago-inicial-${index}`).fadeIn('slow');

    montoPagoInicial.val('');
    conceptoPagoInicial.val(conceptoPagoInicial.children("option").eq(0).val());
}

function eliminarPagoInicial(index) {
    pagosIniciales.splice(index, 1);
    $(`#pago-inicial-${index}`).remove();
    console.log(pagosIniciales);
}

// DATOS DE LOS PAGOS POR PERIODO ****************************************************************************
const tablePagosPeriodo = $('#pagos-periodo');
const montoPagoPeriodo = $('#monto-por-periodo');
const conceptoPagoPeriodo = $('#concepto-por-periodo');
let pagosPorPeriodo = [];

function agregarPagoPorPeriodo() {
    if (!montoPagoPeriodo.val()) {
        return;
    }

    let newPago = {
        "monto": parseFloat(montoPagoPeriodo.val()).toFixed(2),
        "concepto": conceptoPagoPeriodo.val()
    }

    pagosPorPeriodo.push(newPago);
    let index = pagosPorPeriodo.length - 1;
    tablePagosPeriodo.append(`<tr style="display: none;" id="pago-periodo-${index}">
                                    <td>${newPago.monto}</td>
                                    <td>${newPago.concepto}</td>
                                    <td><button onclick="eliminarPagoPeriodo(${index})" type="button" class="btn btn-primary"><i class="bi bi-trash"></i></button></td>
                                </tr>`);

    $(`#pago-periodo-${index}`).fadeIn('slow');
    montoPagoPeriodo.val('');
    conceptoPagoPeriodo.val(conceptoPagoPeriodo.children('option').eq(0).val());
    console.log(pagosPorPeriodo);
}

function eliminarPagoPeriodo(index) {
    pagosPorPeriodo.splice(index, 1);
    $(`#pago-periodo-${index}`).remove();
    console.log(pagosPorPeriodo);
}

// DATOS DE LOS PAGOS (INICIALES Y PERIODICOS) **************************************************************
function getDataPagosPrevios() {
    pagosIniciales.forEach(pago => {
        pago.tipo = 'I';
    })
    pagosPorPeriodo.forEach(pago => {
        pago.tipo = 'P';
    })
    return pagosIniciales.concat(pagosPorPeriodo);
}

// DATOS DE LOS SEGUROS *************************************************************************************
const segDesgravamen = $('#seg-desgravamen');
const segRiesgo = $('#seg-riesgo');

function getDataSeguros() {
    return {
        'seguro_desgravamen': parseFloat(segDesgravamen.val()).toFixed(7),
        'seguro_riesgo': parseFloat(segRiesgo.val()).toFixed(7)
    };
}

// DATOS DE LAS TASAS ***************************************************************************************
const tasaLeasing = $('#tasa-leasing');
const capitalizacion = $('#capitalizacion');
const wacc = $('#wacc');

function getDataTasa() {
    return {
        'tasa': {
            'tipo_tasa': tipoTasaUsuario,
            'tasa': parseFloat(tasaLeasing.val()).toFixed(7),
            'periodo_cap': parseInt(capitalizacion.val())
        },
        'tasaleasing': {
            'wacc': parseFloat(wacc.val()).toFixed(7)
        }
    }
}

// OBTENER DATOS DEL FORMULARIO *****************************************************************************
function getFormData() {
    let tasas = getDataTasa();
    return {
        'prestamo': getDataPrestamo(),
        'pagosprevios': getDataPagosPrevios(),
        'seguros': getDataSeguros(),
        'tasa': tasas.tasa,
        'tasaleasing': tasas.tasaleasing,
        'moneda': {
            'simbolo': monedaUsuario
        }
    };
}

// CÁLCULOS *************************************************************************************************

let leasingForm = $('#leasing-form');

leasingForm.on('submit', function(e) {
    e.preventDefault();
    leasingForm.fadeOut('fast', function() {
        results.fadeIn('fast');
    });
    generateSummary(getFormData());
})

// ENVIO AL SERVIDOR
function getDataResumen() {
    return {
        'saldo_financiar': saldo_financiar,
        'monto_prestamo': monto_prestamo,
        'cuotas_anio': cuotas_por_anio,
        'cuotas_total': cuotas_total,
        'tasa_descuento': tasa_descuento,
        'tir': TIR,
        'tcea': TCEA,
        'van': VAN
    }
}

function getAllData() {
    let tasa_aux = getDataTasa(); 
    return {
        'prestamo': getDataPrestamo(),
        'pagosprevios': getDataPagosPrevios(),
        'seguros': getDataSeguros(),
        'tasaleasing': tasa_aux.tasaleasing,
        'tasa': tasa_aux.tasa,
        'resumenleasing': getDataResumen()
    }
}

function guardarOperacion() {
    $.ajax({
        type: "POST",
        url: "./API/add-leasing.php",
        data: getAllData(),
        dataType: "json",
        success: function (response) {
            if (response.result) {
                window.location.href = './dashboard.php';
            } else {
                showNots('fail', response.message);
            }
        },
        error: function(e) {
            showNots('fail', 'Ha ocurrido un error');
        }
    });
}