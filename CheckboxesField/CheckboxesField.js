$(function() {
    $('body').on('click', '.checkboxesField-all', function() {
        $(this).closest('.checkboxesField').find('input').prop('checked', true);
    });

    $('body').on('click', '.checkboxesField-none', function() {
        $(this).closest('.checkboxesField').find('input').prop('checked', false);
    });

    $('body').on('change', '.checkboxesField input', function() {
        var $ui = $(this).closest('.ui');
        var valArray = $ui.find("input:checked").map(function() { return $(this).val(); }).get();
        $ui.trigger('ui-value-changed', [{value: valArray}]);
    });
});