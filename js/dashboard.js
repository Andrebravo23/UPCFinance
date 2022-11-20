let summaries;

$.ajax({
    type: "GET",
    url: "./API/get-summaries.php",
    success: function (response) {
        summaries = JSON.parse(response);
        loadTable();
    },
    error: function(e) {
        showNots('fail', 'Ha ocurrido un error');
    }
});

function loadTable() {
    $('#summaries').DataTable( {
        "data": summaries,
        dataType: 'json',
        "columns": [
            { data: 'id' },
            { data: 'saldo_financiar' },
            { data: 'monto_prestamo' },
            { data: 'cuotas_total' },
            { data: 'tasa_descuento' },
            { data: 'tir' },
            { data: 'tcea' },
            { data: 'van' }
        ],
        "info": false,
        "searching": false,
        "lengthChange": false,
        "stripeClasses": [],
        "language": {
            "paginate": {
                "previous": '<i class="bi bi-chevron-left"></i>',
                "next": '<i class="bi bi-chevron-right"></i>'
            }
        },
        "columnDefs": [
            { 
                targets: [ 1, 2, 7 ],
                render: function(data, type, row) {
                    return `${data} ${row.moneda}`;
                }     
            },
            {
                targets: [4, 5, 6],
                render: function(data, type, row) {
                    return `${data}%`;
                }
            }
         ]
    } );
};