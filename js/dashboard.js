var data = [
    [
        "s",
        "S",
        "S",
        "S",
        "S",
        "S",
        "S",
        "S"
    ]
];

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
        },
        "columnDefs": [
            { 
                targets: [5],
                render: function(data, type, row) {
                    let tagClass = data == 'Pendiente' ? 'pending' : 'done';
                    return `<span class="tag ${tagClass}">${data}</span>`;
                }     
            },
            {
                targets: [1],
                render: function(data, type, row) {
                    return `<span style="color: var(--gray);">${data}</span>`;
                }
            }
         ]
    } );
})