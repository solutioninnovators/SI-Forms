$(function() {
    init($('.multipleSelectField-select'));

    function init($fields) {
        $fields.each(function() {
            var $this = $(this);
            var $ui = $this.closest('.ui');
            console.log($this.attr('data-settings'));
            const defaultSettings = {
                onClick: function () {
                    $ui.trigger('ui-value-changed');
                },
                onOptgroupClick: function () {
                    $ui.trigger('ui-value-changed');
                }
            }
            var customSettings = $.parseJSON($this.attr('data-settings'));
            var settings = $.extend(defaultSettings, customSettings);
            $this.multipleSelect(settings);
        });
    }

    $('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
        init($(this).find('.multipleSelectField-select'));
    });
});