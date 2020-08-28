$(function() {
    $('body').on('click', '.checkboxesField-all', function() {
        $(this).closest('.checkboxesField').find('input').prop('checked', true);
    });

    $('body').on('click', '.checkboxesField-none', function() {
        $(this).closest('.checkboxesField').find('input').prop('checked', false);
    });

    // todo: Trigger ui-value-changed event when value changes
});