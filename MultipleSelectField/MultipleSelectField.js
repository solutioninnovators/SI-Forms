$(function() {
    init($('.multipleSelectField-select'));

    function init($fields) {
        $fields.each(function() {
            var $this = $(this);
            var $ui = $this.closest('.ui');
            $this.multipleSelect({
                onClick: function () {
                    $ui.trigger('ui-value-changed');
                },
                onOptgroupClick: function () {
                    $ui.trigger('ui-value-changed');
                }
            });
        });
    }

    $('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
        init($(this).find('.multipleSelectField-select'));
    });
});