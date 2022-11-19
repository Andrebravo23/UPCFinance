var data = [
    [
        "07/09/2022",
        "Opción de Compra",
        "$1,800.00",
        "$2.00",
        "$1,798",
        "Pendiente"
    ]
]
$(document).ready( function () {
$('#example').DataTable( {
    "data": data,
    "info": false,
    "searching": false,
    "lengthChange": false,
    "stripeClasses": [],
    "language": {
        "paginate": {
            "previous": '<i class="bi bi-chevron-left"></i>',
            "next": '<i class="bi bi-chevron-right"></i>'
        }
    }
} );
})