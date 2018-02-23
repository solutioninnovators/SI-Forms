$(function() {
    if($('.checkboxesField-columnize').length) {
        $('.checkboxesField-columnize').columnize({ width: 400 });
        $('.checkboxesField .column').css('padding-right', '1em');
    }

    $('body').on('click', '.checkboxesField-all', function() {
        $(this).closest('.checkboxesField').find('input').prop('checked', true);
    });

    $('body').on('click', '.checkboxesField-none', function() {
        $(this).closest('.checkboxesField').find('input').prop('checked', false);
    });
});