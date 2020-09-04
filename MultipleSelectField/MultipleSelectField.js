$(function() {
    init($('.multipleSelectField-select'));

    function init($fields) {
        $fields.multipleSelect({
            onClick: function () {
                $(this).closest('.ui').trigger('ui-value-changed');
            },
            onOptgroupClick: function () {
                $(this).closest('.ui').trigger('ui-value-changed');
            }
        });
    }

    $('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
        init($(this).find('.multipleSelectField-select'));
    });
});