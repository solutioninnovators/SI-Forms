$(function () {
    init($('.tagifyField .field-input'));

    $('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
        init($(this).find('.tagifyField .field-input'));
    });

    function init($fields){
        $fields.each(function() {
            var $this = $(this);

            var defaultSettings = {
                enforceWhitelist: true,
                dropdown: {
                    maxItems: 20,           // maxumum allowed rendered suggestions
                    enabled: 0,             // show suggestions on focus
                    closeOnSelect: true    // hide the suggestions dropdown once an item has been selected
                }
            };

            var customSettings = $.parseJSON($this.attr('data-settings'));
            var settings = $.extend(defaultSettings, customSettings);

            new Tagify(this, settings);

            // Trigger ui-value-changed event on the UI Block when field is changed
            $this.on('change', function () {
                var $this = $(this);
                var $ui = $this.closest('.ui');
                $ui.trigger('ui-value-changed', [{value: $this.val()}]);
            });
        });
    }

});