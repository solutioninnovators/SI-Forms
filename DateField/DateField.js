// Initialize date fields with pikaday
$(function() {
    $('.field_date').each(function() {
        var $input = $(this).find('input');

        var date = new Pikaday({
            field: $input[0],
            format: 'MM/DD/YYYY',
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