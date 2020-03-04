import {$} from './app'
import '../css/security.scss';

$("#toggle-password-button").click(function () {
    let input = $($(this).attr('data-toggle'));

    if (input.attr("type") === "password") {
        input.attr("type", "text");
        $(this).html('<i class="fas fa-eye-slash"></i>');
    } else {
        input.attr("type", "password");
        $(this).html('<i class="fas fa-eye"></i>');
    }
});
