$(function() {
    $('.numberField-up').on('click', function() {
        var $input = $(this).closest('.numberField').find('input');
        $input.val(incrementNumber($input.val(), 1, $input));
        $input.closest('.ui').trigger('ui-value-changed');
        $input.trigger('number-change'); // @deprecated
    });

    $('.numberField-down').on('click', function() {
        var $input = $(this).closest('.numberField').find('input');
        $input.val(incrementNumber($input.val(), -1, $input));
        $input.closest('.ui').trigger('ui-value-changed');
        $input.trigger('number-change'); // @deprecated
    });

    $('.numberField input').on('change', function() {
        $(this).val(incrementNumber($(this).val(), 0, $(this) ));
        $input.closest('.ui').trigger('ui-value-changed');
        $input.trigger('number-change'); // @deprecated
    });

    // Handle long mouse presses
    var interval;
    var timeout;
    $('.numberField-up, .numberField-down').on('mousedown', function() {
        var $button = $(this);
        var increment = 1;
        if($button.hasClass('numberField-down')) increment = -1;

        timeout = setTimeout(function() {
            interval = setInterval(function() {
                var $input = $button.closest('.numberField').find('input');
                $input.val(incrementNumber($input.val(), increment, $input));
            }, 100);
        }, 400);
    });
    $('.numberField-up, .numberField-down').on('mouseup mouseout', function() {
        clearTimeout(timeout);
        clearInterval(interval);
    });


    function incrementNumber(number, increment, $input) {
		var isDecimal = $input.attr('data-number-decimal') == 1 ? true : false;
        var min = $input.attr('data-number-min');
        var max = $input.attr('data-number-max');

        if(number)
        {
            if(increment>0){
                if(!isNumeric(max) || number < max){
                    number = parseInt(number) + parseInt(increment);
                }
            }
            else if(increment<0){
                if(!isNumeric(min) || number > min){
                    number = parseInt(number) + parseInt(increment);
                }

            }
        }

        return number;
    }

    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

});