//UTILITARIOS
function ajustaTipoCambio(moneda, monto) {
    if (moneda != monedaUsuario) {
        return monedaUsuario == 'PEN' ? parseFloat(monto) * 3.89 : parseFloat(monto) / 3.89;
    } else {
        return parseFloat(monto);
    }
}

//VALOR DEL BIEN
const precioVenta = $('#PV');
const monedaPrecioVenta = $('#PV-moneda');
const cuotaInicial = $('#CI');
const cuotaInicialMedida = $('#CI-medida');
const opcionCompra = $('#opcion-compra');
const monedaOpcionCompra = $('#moneda-opcion-compra');

function getDataValorBien() {
    let PV = ajustaTipoCambio(monedaPrecioVenta.val(), precioVenta.val());
    let CI = cuotaInicialMedida.val() == 'P' ? PV * parseFloat(cuotaInicial.val()) / 100 : parseFloat(cuotaInicial.val());
    let OC = ajustaTipoCambio(monedaOpcionCompra.val(), opcionCompra.val());

    return {
        'precio_venta': PV,
        'cuota_inicial': CI,
        'opcion_compra': OC
    }
}

//PLAZO DE PAGO
const numeroPagos = $('#num-pagos');
const numeroPagosUnidad = $('#num-pagos-unit');
const frecuenciaPago = $('#frec-pago');
const fecInicioPrestamo = $('#fecha-inicio-prestamo');
const fecPrimerPago = $('#fecha-primer-pago');

function getDataPlazoPago() {
    return {
        'num_pagos': parseInt(numeroPagos.val()),
        'unidad': numeroPagosUnidad.val(),
        'frecuencia': parseInt(frecuenciaPago.val()),
        'fec_prestamo': fecInicioPrestamo.val(),
        'fec_primer_pago': fecPrimerPago.val()
    }
}

//TASAS
const tasaLeasing = $('#tasa-leasing');
const capitalizacion = $('#capitalizacion');
const diasAnio = $('#dias-anio');
const ks = $('#ks');
const wacc = $('#WACC');

function getDataTasa() {
    return {
        'tasa': {
            'tipo_tasa': tipoTasaUsuario,
            'tasa': parseFloat(tasaLeasing.val()).toFixed(7),
            'periodo_cap': parseInt(capitalizacion.val()),
            'dias_anio': parseInt(diasAnio.val())
        },
        'tasaleasing': {
            'ks': parseFloat(ks.val()).toFixed(7),
            'wacc': parseFloat(wacc.val()).toFixed(7)
        }
    }
}

//PAGOS PORCENTUALES
const activacion = $('#activacion');
const activacionUnit = $('#activacion-unit');
const segRiesgo = $('#seg-riesgo');
const segRiesgoFrec = $('#seg-riesgo-frec');
const impuestoVenta = $('#IV');
const impuestoRenta = $('#IR');

function getDataPagosPorcentuales(CI) {
    let AC = activacionUnit.val() == 'P' ? CI * parseFloat(activacion.val()) / 100 : parseFloat(activacion.val()); 
    return {
        'activacion': parseFloat(AC).toFixed(7),
        'seguro_riesgo': parseFloat(segRiesgo.val()).toFixed(7),
        'frec_seguro': parseInt(segRiesgo.val()),
        'impVenta': parseFloat(impuestoVenta.val()).toFixed(7),
        'impRenta': parseFloat(impuestoRenta.val()).toFixed(7)
    };
}

//PAGOS INICIALES Y POR PERIODOS
const tablePagosIniciales = $('#pagos-iniciales');
const montoPagoInicial = $('#monto');
const conceptoPagoInicial = $('#concepto');
const desembolsoPagoInicial = $('#desembolso');
let pagosIniciales = [];

function agregarPagoInicial() {
    console.log(montoPagoInicial.val());
    if (!montoPagoInicial.val()) {
        return;
    }

    let newPago = {
        "monto": parseFloat(montoPagoInicial.val()).toFixed(2),
        "concepto": conceptoPagoInicial.val(),
        "desembolso": desembolsoPagoInicial.val()
    }
    pagosIniciales.push(newPago);
    let index = pagosIniciales.length - 1;
    tablePagosIniciales.append(`<tr id="pago-inicial-${index}">
                                    <td>${newPago.monto}</td>
                                    <td>${newPago.concepto}</td>
                                    <td>${newPago.desembolso}</td>
                                    <td><button onclick="eliminarPagoInicial(${index})" type="button" class="btn btn-primary"><i class="bi bi-trash"></i></button></td>
                                </tr>`);
    console.log(pagosIniciales);

    montoPagoInicial.val('');
    conceptoPagoInicial.val(conceptoPagoInicial.children("option").eq(0).val());
    desembolsoPagoInicial.val(desembolsoPagoInicial.children("option:first").eq(0).val());
}

function eliminarPagoInicial(index) {
    pagosIniciales.splice(index, 1);
    $(`#pago-inicial-${index}`).remove();
    console.log(pagosIniciales);
}

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
    tablePagosPeriodo.append(`<tr id="pago-periodo-${index}">
                                    <td>${newPago.monto}</td>
                                    <td>${newPago.concepto}</td>
                                    <td><button onclick="eliminarPagoPeriodo(${index})" type="button" class="btn btn-primary"><i class="bi bi-trash"></i></button></td>
                                </tr>`);

    montoPagoPeriodo.val('');
    conceptoPagoPeriodo.val(conceptoPagoPeriodo.children('option').eq(0).val());
    console.log(pagosPorPeriodo);
}

function eliminarPagoPeriodo(index) {
    pagosPorPeriodo.splice(index, 1);
    $(`#pago-periodo-${index}`).remove();
    console.log(pagosPorPeriodo);
}