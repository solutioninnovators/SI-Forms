// Initialize date fields with pikaday
$(function() {
    $('.dateField').each(function() {
        init($(this));
    });

    $('body').on('ui-reloaded', '.ui_DateField', function(){
        init($(this));
    });
    
    function init($field) {
        var $input = $field.find('input');

        var date = new Pikaday({
            field: $input[0],
            format: $input.attr('data-date-format'),
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
    }
});