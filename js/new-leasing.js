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
    tablePagosIniciales.append(`<tr style="display: none;" id="pago-inicial-${index}">
                                    <td>${newPago.monto}</td>
                                    <td>${newPago.concepto}</td>
                                    <td>${newPago.desembolso}</td>
                                    <td><button onclick="eliminarPagoInicial(${index})" type="button" class="btn btn-primary"><i class="bi bi-trash"></i></button></td>
                                </tr>`);
    console.log(pagosIniciales);
    $(`#pago-inicial-${index}`).fadeIn('slow');

    montoPagoInicial.val('');
    conceptoPagoInicial.val(conceptoPagoInicial.children("option").eq(0).val());
    desembolsoPagoInicial.val(desembolsoPagoInicial.children("option:first").eq(0).val());
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
        pago.desembolso = 0;
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

// CÁLCULOS *************************************************************************************************

let leasingForm = $('#leasing-form');
let results = $('#results');
let feesTable = $('#fees-table');

leasingForm.on('submit', function(e) {
    e.preventDefault();

    let tasas = getDataTasa();
    let prestamo = getDataPrestamo();
    let pagosprevios = getDataPagosPrevios();
    let seguros = getDataSeguros();
    let tasa = tasas.tasa;
    let tasaleasing = tasas.tasaleasing;
    let summary = [];

    let gastos_iniciales = 0;
    let gastos_periodicos = 0;
    pagosprevios.forEach(pago => {
        if (pago.tipo == 'I') {
            gastos_iniciales += parseFloat(pago.monto);
        } else {
            gastos_periodicos -= parseFloat(pago.monto);
        }
    });

    let saldo_financiar = prestamo['precio_venta'] - prestamo['cuota_inicial'];
    let monto_prestamo = saldo_financiar + gastos_iniciales;
    let cuotas_por_anio = prestamo['dias_anio'] / prestamo['frecuencia'];
    let total_cuotas = prestamo['unidad'] == 'A' ? prestamo['num_pagos'] * cuotas_por_anio : prestamo['num_pagos'];
    let porcentaje_seguro_desgravamen = seguros['seguro_desgravamen'] * prestamo['frecuencia'] / 30;
    let seguro_riesgo = -seguros['seguro_riesgo'] * prestamo['precio_venta'] / (cuotas_por_anio * 100);
    let amortizacion = -monto_prestamo / total_cuotas;

    let TEA = 0;
    if (tasa['tipo_tasa'] == 'E') {
        TEA = parseFloat(tasa['tasa']).toFixed(7);
    } else {
        let n = 360 / tasa['periodo_cap'];
        TEA = parseFloat(100 * (Math.pow(1 + tasa['tasa'] / (100 * n), n) - 1)).toFixed(7);
    }

    let TEP = parseFloat((Math.pow(1 + TEA / 100, prestamo['frecuencia'] / prestamo['dias_anio']) - 1) * 100).toFixed(7);

    leasingForm.fadeOut('fast', function() {
        results.fadeIn('fast');
    });

    let allFees = [];
    let saldo_inicial = monto_prestamo;
    
    let total_intereses = 0;
    let total_amortizacion = 0;
    let total_desgravamen = 0;
    let total_riesgo = 0;
    let total_periodicos = 0;

    for (let index = 1; index <= total_cuotas; index++) {
        let interes = -saldo_inicial * TEP / 100;
        let seguro_desgravamen = -porcentaje_seguro_desgravamen * saldo_inicial / 100;
        let saldo_final = saldo_inicial + amortizacion;
        let cuota = interes + amortizacion + seguro_desgravamen;
        let flujo = cuota + seguro_riesgo + gastos_periodicos;

        total_intereses += interes;
        total_amortizacion += amortizacion;
        total_desgravamen += seguro_desgravamen;
        total_riesgo += seguro_riesgo;
        total_periodicos += gastos_periodicos;

        let newFee = [
            index,
            TEA + '%',
            TEP + '%',
            saldo_inicial.toFixed(2),
            interes.toFixed(2),
            cuota.toFixed(2),
            amortizacion.toFixed(2),
            seguro_desgravamen.toFixed(2),
            seguro_riesgo.toFixed(2),
            gastos_periodicos.toFixed(2),
            saldo_final.toFixed(2),
            flujo.toFixed(2)
        ];
        
        allFees.push(newFee);
        saldo_inicial = saldo_final;
    }
    
    feesTable.DataTable( {
        "data": allFees,
        "info": false,
        "searching": false,
        "lengthChange": false,
        "scrollX": true,
        "stripeClasses": [],
        "language": {
            "paginate": {
                "previous": '<i class="bi bi-chevron-left"></i>',
                "next": '<i class="bi bi-chevron-right"></i>'
            }
        },
        "columnDefs": [
            { 
                targets: [ 4, 5, 6, 7, 8, 9, 11 ],
                render: function(data, type, row) {
                    return `<span class="negative">${data}</span>`;
                }
            },
            {
                targets: [10],
                render: function(data, type, row) {
                    return `<span class="positive">${data}</span>`;
                }
            }
         ]
    } );

    summary.push([ 'Saldo a financiar', monedaUsuario, parseFloat(saldo_financiar).toFixed(2) ]);
    summary.push([ 'Monto del préstamo', monedaUsuario, parseFloat(monto_prestamo).toFixed(2) ]);
    summary.push([ 'Nº Cuotas por Año', '-', parseFloat(cuotas_por_anio).toFixed(2) ]);
    summary.push([ 'Nº Total de Cuotas', '-', parseFloat(total_cuotas).toFixed(2) ]);

    summary.push([ '% de Seguro desgrav. per.', '%', parseFloat(porcentaje_seguro_desgravamen).toFixed(7) ]);
    summary.push([ 'Seguro Riesgo', monedaUsuario, parseFloat(-seguro_riesgo).toFixed(2) ]);
    
    summary.push([ 'Intereses', monedaUsuario, parseFloat(-total_intereses).toFixed(2) ]);
    summary.push([ 'Amortización del Capital', monedaUsuario, parseFloat(-total_amortizacion).toFixed(2) ]);
    summary.push([ 'Seguro de desgravamen', monedaUsuario, parseFloat(-total_desgravamen).toFixed(2) ]);
    summary.push([ 'Seguro contra todo Riesgo', monedaUsuario, parseFloat(-total_riesgo).toFixed(2) ]);
    summary.push([ 'Pagos Periódicos', monedaUsuario, parseFloat(-total_periodicos).toFixed(2) ]);

    resultsSummary.DataTable( {
        "data": summary,
        "info": false,
        "searching": false,
        "lengthChange": false,
        "ordering": false,
        "paging": false,
        "scrollX": true,
        "stripeClasses": [],
        "columnDefs": [/* 
            { 
                targets: [ 4, 5, 6, 7, 8, 9, 11 ],
                render: function(data, type, row) {
                    return `<span class="negative">${data}</span>`;
                }
            },
            {
                targets: [10],
                render: function(data, type, row) {
                    return `<span class="positive">${data}</span>`;
                }
            } */
         ]
    } );
})

// RESULTADOS ***********************************************************************************************
let resultSelected = 0;
let showSummaryBtn = $('#show-summary');
let showTableBtn = $('#show-table');
let resultsSummary = $('#results-summary');

function toggleSummary() {
    showSummaryBtn.toggleClass('disabled');
    showTableBtn.toggleClass('disabled');
    if (resultSelected == 0) {
        resultsSummary.fadeOut('fast', function(){
            feesTable.fadeIn('fast');
            resultSelected = 1;
        })
    } else {
        feesTable.fadeOut('fast', function(){
            resultsSummary.fadeIn('fast');
            resultSelected = 0;
        })
    }
}