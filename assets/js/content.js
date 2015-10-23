/**
 * Created by mkv on 10.07.15.
 */
$(document).ready(function () {

    /**
     * Групповые действия с элементами
     */
    $('a.btn-multiple').on('click', function () {

        if(confirm('Вы действительно хотите выполнить это действие?')) {
            var obj = $(this);
            var url = document.location.href;
            var keys = $('#grid').yiiGridView('getSelectedRows');
            var model = obj.data('classname');
            var action = obj.data('action');

            if(keys.length > 0) {
                $.ajax({
                    url: $('#grid').data('url'),
                    type: 'POST',
                    data: 'keys=' + keys + '&model=' + model + '&action=' + action + '&url=' + url,
                    success: function (data) {},
                    dataType: 'json'
                });
            }
        }

        return false;
    });

    // Изменение видимости статей
    $('.visible_checkbox').change(function () {
        var $obj = $(this);

        $.ajax({
            url: $obj.data('url'),
            type: "POST",
            data: 'id=' + $obj.data('id') + '&visible=' + ($obj.prop('checked') ? 1 : 0),
            success: function($data) {
                $obj.after('<span style="margin: 0 10px" class="label label-' + $data.type + '">' + $data.message + '</span>');
                $obj.next('span').fadeOut(2000, function() {
                    $( this ).remove();
                });
            },
            dataType: 'json'
        });
    });

    // Скрываем элементы редактирования
    $('.sort_button').css('display', 'none');
    $('.sort_change').css('display', 'none');

    // Событие по нажатию на кнопку изменить поле
    $('.sort_input').on('click', function () {
        var $obj = $(this);

        $obj.next('.sort_change').css('display', 'inline-block');
        $obj.next('.sort_change').next('.sort_button').css('display', 'inline-block');
        $obj.next('.sort_change').focus();
        $obj.css('display', 'none');
    });

    // Событие после нажатие на кнопку применить изменения
    $('.sort_button').on('click', function () {
        var $obj = $(this);
        var $input = $obj.prev('.sort_change');

        $.ajax({
            url: '/admin/content/sort',
            type: "POST",
            data: 'id=' + $input.data('id') + '&sort=' + $input.val(),
            success: function($data) {
                $obj.after('<span style="margin: 0 10px" class="label label-' + $data.type + '">' + $data.message + '</span>');
                $obj.next('span').fadeOut(2000, function() {
                    $( this ).remove();
                });
                $input.prev('.sort_input').html('<i class="icon icon fa fa-edit">' + $input.val() + '</i>');
            },
            dataType: 'json'
        });

        $obj.css('display', 'none');
        $input.css('display', 'none');
        $input.prev('.sort_input').css('display', 'inline-block');

        return false;
    });

    //$('.sort_change').focusout(function (e) {
    //    if($(this).prev('.sort_input').css('display') == 'none') {
    //        $(this).prev('.sort_input').css('display', 'block');
    //        $(this).css('display', 'none');
    //        $(this).next('.sort_button').css('display', 'none');
    //    }
    //});

    // Событие по нажатию на Enter
    //$(document).keydown(function (e) {
    //    if(e.keyCode == 13) {
    //        $obj = $("input:focus");
    //        $obj.next('.sort_button').click();
    //
    //        return false;
    //    }
    //});
});