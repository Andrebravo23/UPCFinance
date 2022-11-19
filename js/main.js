const nots = $('#nots');
const notsIcon = $('#nots i');
const notsText = $('#nots span');

const notsIcons = {
    'success': 'bi bi-check-circle',
    'fail': 'bi bi-x-circle'
}

let notsTimeout = undefined;

function showNots(type, text) {
    clearTimeout(notsTimeout);
    nots.fadeIn('fast');
    nots.attr('class', '');
    nots.addClass(type);
    notsIcon.attr('class', '');
    notsIcon.addClass(notsIcons[type]);
    notsText.html(text);
    notsTimeout = setTimeout(() => {
        nots.fadeOut('fast');
    }, 3000);
}

let onRegister = false;

function toggleRegister(e) {
    if (!onRegister) {
        $('#log-in').fadeOut("fast", function() {
            $('#register').fadeIn("fast");
            onRegister = true;
        });
    } else {
        $('#register').fadeOut("fast", function() {
            $('#log-in').fadeIn("fast");
            onRegister = false;
        });
    }
}

$('#register').on('submit', function(e) {
    let _form = $(this);

    e.preventDefault();
    if ($('#password-register').val() != $('#repeat-password').val()) {
        showNots('fail', 'Las contraseñas no son iguales');
        return;
    }

    $.ajax({
        type: "POST",
        url: "./API/login.php?action=register",
        data: _form.serialize(),
        success: function (response) {
            response = JSON.parse(response);
            if (response.result == 1) {
                toggleRegister();
                _form.trigger("reset");
                showNots('success', response.message);
            } else {
                showNots('fail', response.message);
            }
        },
        error: function (response) {
            showNots('fail', 'Ha ocurrido un error');
        }
    });
});

$('#log-in').on('submit', function(e) {
    let _form = $(this);

    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "./API/login.php?action=login",
        data: _form.serialize(),
        success: function (response) {
            response = JSON.parse(response);
            if (response.result == 1) {
                window.location.href = 'dashboard.php';
            } else {
                showNots('fail', response.message);
            }
        },
        error: function (response) {
            showNots('fail', 'Ha ocurrido un error');
        }
    });
});