// Initialize date fields with pikaday
$(function() {
    init($('.dateField'));

    $('body').on('ui-reloaded', '.ui', function(e) {
        e.stopPropagation();
        init($(this).find('.dateField'));
    });
    
    function init($fields) {
        $fields.each(function() {
            var $input = $(this).find('input');

            var date = new Pikaday({
                field: $input[0],
                format: $input.attr('data-date-format'),
                minDate: new Date($input.attr('data-min-date')),
                maxDate: new Date($input.attr('data-max-date')),
            });

            // If the value of the field is changed, automatically trigger setDate on pikaday
            var recursed = false;
            $input.on('change', function() {
                if(recursed == false) { // Prevent setDate from triggering a recursive call
                    recursed = true;
                    date.setDate($input.val());
                    recursed = false;
                }

                // Trigger ui-value-changed event on the UI Block when field is changed
                var $this = $(this);
                var $ui = $this.closest('.ui');
                $ui.trigger('ui-value-changed', [{value: $this.val()}]);
            });
        });
    }
});