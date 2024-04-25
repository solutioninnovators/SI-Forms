$(function() {
    $('body').on('click', '.numberField-up, .numberField-down', function() {
        var $input = $(this).closest('.numberField').find('input');
        var increment = parseFloat($input.attr('data-number-step')) || 1;
        if ($input.val() === '') $input.val(0);

        if($(this).hasClass('numberField-down')) increment = increment * -1;

        $input.val(incrementNumber($input.val(), increment, $input));
        $input.closest('.ui').trigger('ui-value-changed');
    });

    $('body').on('change', '.numberField input', function() {
        var $input = $(this);
        this.value = this.value.replace(/[^0-9.-]/g, ''); // Allow only numbers and decimals
        //$input.val(incrementNumber($input.val(), 0, $input));
        $input.closest('.ui').trigger('ui-value-changed');
    });

    // Handle long mouse presses
    var interval;
    var timeout;
    $('body').on('mousedown', '.numberField-up, .numberField-down', function() {
        var $input = $(this).closest('.numberField').find('input');
        var $button = $(this);
        var increment = parseFloat($input.attr('data-number-step')) || 1;
        if ($input.val() === '') $input.val(0);

        if($button.hasClass('numberField-down')) increment = increment * -1;

        timeout = setTimeout(function() {
            interval = setInterval(function() {
                var $input = $button.closest('.numberField').find('input');
                $input.val(incrementNumber($input.val(), increment, $input));
            }, 100);
        }, 400);
    });
    $('body').on('mouseup mouseout', '.numberField-up, .numberField-down', function() {
        clearTimeout(timeout);
        clearInterval(interval);
    });


    function incrementNumber(number, increment, $input) {
        var min = $input.attr('data-number-min');
        var max = $input.attr('data-number-max');
        var step = parseFloat($input.attr('data-number-step')) || 1;

        if(number) {
            let decimalPlaces = Math.max(0, -Math.floor(Math.log10(Math.abs(step))));
            number = parseFloat(number) + parseFloat(increment);
            number = roundToDecimalPlaces(number, decimalPlaces);

            if(isNumeric(max) && parseFloat(number) > parseFloat(max)){
                number = parseFloat(max);
            }
            if(isNumeric(min) && parseFloat(number) < parseFloat(min)){
                number = parseFloat(min);
            }
        }

        return number;
    }

    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    function roundToDecimalPlaces(number, decimalPlaces) {
        // Use Math.pow(10, decimalPlaces) to calculate the multiplier
        let multiplier = Math.pow(10, decimalPlaces);

        // Round the absolute value of the number and apply the sign back
        let roundedNumber = Math.round(Math.abs(number) * multiplier) / multiplier;

        // Apply the sign back to the rounded number
        return (number < 0) ? -roundedNumber : roundedNumber;
    }


});