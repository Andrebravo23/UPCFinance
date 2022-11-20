const email = $('#email');
const name = $('#name');
const lastname = $('#last-name');
const currentpassword = $('#current-password');
const newpassword = $('#new-password');
const newpasswordrepeat = $('#new-password-repeat');
const moneda = $('#moneda');
const tipo_tasa = $('#tipo_tasa');

function getData() {
    return {
        usuario: {
            'nombres': name.val(),
            'apellidos': lastname.val(),
            'password': currentpassword.val(),
            'newpassword': newpassword.val(),
            'newpasswordrepeat': newpasswordrepeat.val()
        },
        configuracion: {
            'id_moneda': moneda.val(),
            'tipo_tasa': tipo_tasa.val()
        }
    }
}

const configurationForm = $('#configuration');
configurationForm.on('submit', function(e) {
    e.preventDefault();
});

function guardarCambios() {
    payload = getData();

    if (payload.usuario.newpassword != '' || payload.usuario.newpasswordrepeat != '') {
        if (payload.usuario.newpassword != payload.usuario.newpasswordrepeat) {
            showNots('fail', 'Las contraseñas no coinciden');
            return;
        } else if (payload.usuario.password == "") {
            showNots('fail', 'Ingresa tu contraseña actual');
            return;
        }
    }

    $.ajax({
        type: "POST",
        url: "./API/update-configuration.php",
        data: payload,
        success: function (response) {
            response = JSON.parse(response);
            if (response.result == 1) {
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