/**
 * User: Gold Dragon
 * Date: 03.03.2012
 * Time: 22:24
 * Блокирует "Название" (id='name') и помещет в это поле значение
 * из "Фамилии" (id='content_fam'), "Имя" (id='content_im'), "Отчество" (id='content_ot')
 */

$(function () {
    if ($("#content_fam").length > 0 && $("#content_im").length > 0 && $("#content_ot").length > 0) {
        $("#name").attr("readonly", true);
        $("#name").after('<br /><span style="font-size:90%;color:#ff0000;">Поле только для чтения. Заполняется автоматически (A read-only field. It is filled automatically)</span>');
        $("#content_fam, #content_im, #content_ot").blur(function () {
            boss_contact();
        });
    }
});

function boss_contact() {
    if ($("#content_fam").val() + $("#content_im").val() + $("#content_ot").val() != '') {
        $("#name").val($("#content_fam").val() + " " + $("#content_im").val() + " " + $("#content_ot").val());
    } else {
        $("#name").val('');
    }
}

