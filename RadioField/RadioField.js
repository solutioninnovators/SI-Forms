$(function() {
    $('body').on('click', '.radioField input', function() {
        //console.log('Field changed.');

        var $ui = $(this).closest('.ui');
        $ui.trigger('ui-value-changed', [{value: $ui.find('input:checked').val()}]);
    });
});