$(function() {
    $('body').on('click', '.timeField-up', function() {
        var $input = $(this).closest('.timeField').find('input');
        $input.val(incrementTime($input.val(), 1, $input));
        $input.closest('.ui').trigger('ui-value-changed');
        $input.trigger('time-change'); // @deprecated
    });

    $('body').on('click', '.timeField-down', function() {
        var $input = $(this).closest('.timeField').find('input');
        $input.val(incrementTime($input.val(), -1, $input));
        $input.closest('.ui').trigger('ui-value-changed');
        $input.trigger('time-change'); // @deprecated
    });

    $('body').on('change', '.timeField input', function() {
        var $input = $(this);
        $input.val(incrementTime($(this).val(), 0, $(this) ));
        $input.closest('.ui').trigger('ui-value-changed');
        $input.trigger('time-change'); // @deprecated
    });

    // Handle long mouse presses
    var interval;
    var timeout;
    $('body').on('mousedown', '.timeField-up, .timeField-down', function() {
        var $button = $(this);
        var increment = 1;
        if($button.hasClass('timeField-down')) increment = -1;

        timeout = setTimeout(function() {
            interval = setInterval(function() {
                var $input = $button.closest('.timeField').find('input');
                $input.val(incrementTime($input.val(), increment, $input));
            }, 100);
        }, 400);
    });
    $('body').on('mouseup mouseout', '.timeField-up, .timeField-down', function() {
        clearTimeout(timeout);
        clearInterval(interval);
    });


    function incrementTime(time, increment, $input) {
        var twentyFourHour = $input.attr('data-twentyfourhour') == 1 ? true : false;
        var amPm = twentyFourHour && $input.attr('data-ampm') == 1 ? true : false;

        if(!time && increment != 0) time = '00:00';

        if(time) {
            var regex;
            if (twentyFourHour) {
                if (amPm) {
                    regex = new RegExp(/^\d{1,2}(:\d{2})?([AaPp][Mm])?$/);
                }
                else {
                    regex = new RegExp(/^\d{1,2}(:\d{2})?$/);
                }
            }
            else {
                // If entered as a decimal, convert to HH:MM
                if(time.split(".").length - 1 === 1) {
                    time = minutesToTime(time * 60);
                }

                regex = new RegExp(/^\d+(:\d{2})?$/);
            }

            // Check if time format is valid. If not, leave it alone and highlight the error
            if (regex.test(time) == false) {
                $input.addClass('txtBox_error');
                return time;
            }
            $input.removeClass('txtBox_error');

            // If necessary, convert from am/pm to 24-hour time
            if(amPm) {
                time = amPmToMilitary(time);
            }

            var totalMinutes = timeToMinutes(time);
            totalMinutes += increment; // Apply the time increment/decrement

            if (twentyFourHour) {
                if (totalMinutes > 1439) totalMinutes = 0;
                else if (totalMinutes < 0) totalMinutes = 1439;
            }
            else {
                if (totalMinutes < 0) totalMinutes = 0;
            }

            time = minutesToTime(totalMinutes);

            // Convert back to am/pm if necessary
            if(amPm) {
               time = militaryToAmPm(time);
            }

        }

        return time;
    }
});