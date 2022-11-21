// CÁLCULOS *************************************************************************************************
let results = $('#results');
let feesTable = $('#fees-table');
let resultsSummary = $('#results-summary');

let saldo_financiar = 0;
let monto_prestamo = 0;
let cuotas_por_anio = 0;
let cuotas_total = 0;
let tasa_descuento = 0;
let TIR = 0;
let TCEA = 0;
let VAN = 0;

let porcentaje_seguro_desgravamen = 0;
let seguro_riesgo = 0;
let amortizacion = 0;
let arr_flujoCaja = [];

function generateSummary(leasingData) {
    let { prestamo, pagosprevios, seguros, tasa, tasaleasing, moneda } = leasingData;
    moneda = moneda.simbolo;
    
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

    saldo_financiar = prestamo['precio_venta'] - prestamo['cuota_inicial'];
    monto_prestamo = saldo_financiar + gastos_iniciales;
    cuotas_por_anio = prestamo['dias_anio'] / prestamo['frecuencia'];
    cuotas_total = prestamo['unidad'] == 'A' ? prestamo['num_pagos'] * cuotas_por_anio : prestamo['num_pagos'];
    porcentaje_seguro_desgravamen = seguros['seguro_desgravamen'] * prestamo['frecuencia'] / 30;
    seguro_riesgo = -seguros['seguro_riesgo'] * prestamo['precio_venta'] / (cuotas_por_anio * 100);
    amortizacion = -monto_prestamo / cuotas_total;
 
    summary.push([ 'Saldo a financiar', moneda, parseFloat(saldo_financiar).toFixed(2) ]);
    summary.push([ 'Monto del préstamo', moneda, parseFloat(monto_prestamo).toFixed(2) ]);
    summary.push([ 'Nº Cuotas por Año', '-', parseInt(cuotas_por_anio) ]);
    summary.push([ 'Nº Total de Cuotas', '-', parseInt(cuotas_total) ]);
    summary.push([ '% de Seguro desgrav. per.', '%', parseFloat(porcentaje_seguro_desgravamen).toFixed(7) ]);
    summary.push([ 'Seguro contra todo Riesgo', moneda, parseFloat(-seguro_riesgo).toFixed(2) ]);

    let TEA = 0;
    if (tasa['tipo_tasa'] == 'E') {
        TEA = parseFloat(tasa['tasa']).toFixed(7);
    } else {
        let n = 360 / tasa['periodo_cap'];
        TEA = parseFloat(100 * (Math.pow(1 + tasa['tasa'] / (100 * n), n) - 1)).toFixed(7);
    }

    let TEP = parseFloat((Math.pow(1 + TEA / 100, prestamo['frecuencia'] / prestamo['dias_anio']) - 1) * 100).toFixed(7);

    let allFees = [];
    let saldo_inicial = monto_prestamo;
    
    let total_intereses = 0;
    let total_amortizacion = 0;
    let total_desgravamen = 0;
    let total_riesgo = 0;
    let total_periodicos = 0;

    for (let index = 1; index <= cuotas_total; index++) {
        let interes = -saldo_inicial * TEP / 100;
        let seguro_desgravamen = -porcentaje_seguro_desgravamen * saldo_inicial / 100;
        let saldo_final = saldo_inicial + amortizacion;
        let cuota = interes + amortizacion + seguro_desgravamen;
        let flujo = cuota + seguro_riesgo + gastos_periodicos;

        arr_flujoCaja.push(flujo);

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
    
    summary.push([ 'Total de Intereses', moneda, parseFloat(-total_intereses).toFixed(2) ]);
    summary.push([ 'Amortización Total del Capital', moneda, parseFloat(-total_amortizacion).toFixed(2) ]);
    summary.push([ 'Total Seguro de desgravamen', moneda, parseFloat(-total_desgravamen).toFixed(2) ]);
    summary.push([ 'Total Seguro Contra todo Riesgo', moneda, parseFloat(-total_riesgo).toFixed(2) ]);
    summary.push([ 'Total de Pagos Periódicos', moneda, parseFloat(-total_periodicos).toFixed(2) ]);

    tasa_descuento = 100 * (Math.pow(1 + tasaleasing.wacc / 100, prestamo['frecuencia'] / prestamo['dias_anio']) - 1);
    arr_flujoCaja.unshift(monto_prestamo);
    TIR = irr(arr_flujoCaja) * 100;
    TCEA = (Math.pow(1 + TIR / 100, cuotas_por_anio) - 1) * 100;
    VAN = npv(tasa_descuento / 100, arr_flujoCaja);

    summary.push([ 'Tasa de Descuento', '%', parseFloat(tasa_descuento).toFixed(7) ]);
    summary.push([ 'TIR de la Operación', '%', parseFloat(TIR).toFixed(7) ]);
    summary.push([ 'TCEA de la Operación', '%', parseFloat(TCEA).toFixed(7) ]);
    summary.push([ 'VAN de la Operación', moneda, parseFloat(VAN).toFixed(2) ]);

    resultsSummary.DataTable( {
        "data": summary,
        "info": false,
        "searching": false,
        "lengthChange": false,
        "ordering": false,
        "paging": false,
        "stripeClasses": []
    } );
};

// MOSTRAR RESULTADOS ***************************************************************************************
let resultSelected = 0;
let showSummaryBtn = $('#show-summary');
let showTableBtn = $('#show-table');
let summaryContainer = $('#summary');
let feesContainer = $('#fees');
let rendered = false;

function toggleSummary() {
    showSummaryBtn.toggleClass('disabled');
    showTableBtn.toggleClass('disabled');
    if (resultSelected == 0) {
        summaryContainer.fadeOut('fast', function(){
            feesContainer.fadeIn('fast');
            setTimeout(() => {
                if (!rendered) {
                    feesTable.DataTable().rows().invalidate('data').draw(false);
                    rendered = true;
                }
            }, 10);
            resultSelected = 1;
        })
    } else {
        feesContainer.fadeOut('fast', function(){
            summaryContainer.fadeIn('fast');
            resultSelected = 0;
        })
    }
}