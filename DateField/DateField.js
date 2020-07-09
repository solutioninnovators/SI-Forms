// Initialize date fields with pikaday
$(function() {
    $('.dateField').each(function() {
        var $input = $(this).find('input');

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
        });
    });
});