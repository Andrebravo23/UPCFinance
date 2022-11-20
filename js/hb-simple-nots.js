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