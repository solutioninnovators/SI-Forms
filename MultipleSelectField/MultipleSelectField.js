$(function() {
    init($('.multipleSelectField-select'));

    function init($fields) {
        $fields.each(function() {
            var $this = $(this);
            var $ui = $this.closest('.ui');
            const defaultSettings = {
                onClick: function () {
                    $ui.trigger('ui-value-changed');
                },
                onOptgroupClick: function () {
                    $ui.trigger('ui-value-changed');
                }
            }
            var customSettings =  $this.data('settings');
            var settings = $.extend(defaultSettings, customSettings);
            $this.multipleSelect(settings);
            $this.multipleSelect('refresh');
        });
    }

    $('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
        init($(this).find('.multipleSelectField-select:not(.ms-parent)'));
    });
});