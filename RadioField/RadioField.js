$(function() {
    $('body').on('click', '.radioField input', function() {
        //console.log('Field changed.');

        var $ui = $(this).closest('.ui');
        $ui.trigger('valueChange', [{value: $ui.find('input:checked').val()}]);
    });
});