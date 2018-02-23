// @todo: Switch to using moment.js and moment-duration-format.js instead of custom time/duration conversion logic?

$(function() {
    $('.timeField-up').on('click', function() {
        var $input = $(this).closest('.timeField').find('input');
        $input.val(incrementTime($input.val(), 1, $input));
        $input.trigger('time-change');
    });

    $('.timeField-down').on('click', function() {
        var $input = $(this).closest('.timeField').find('input');
        $input.val(incrementTime($input.val(), -1, $input));
        $input.trigger('time-change');
    });

    $('.timeField input').on('change', function() {
        $(this).val(incrementTime($(this).val(), 0, $(this) ));
        $(this).trigger('time-change');
    });

    // Handle long mouse presses
    var interval;
    var timeout;
    $('.timeField-up, .timeField-down').on('mousedown', function() {
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
    $('.timeField-up, .timeField-down').on('mouseup mouseout', function() {
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
                regex = new RegExp(/^\d+(:\d{2})?$/);
            }

            // Check if time format is valid. If not, leave it alone and highlight the error
            if (regex.test(time) == false) {
                $input.addClass('txtBox_error');
                return time;
            }
            $input.removeClass('txtBox_error');


            // Parse and increment/decrement the time
            var timeArray;
            var hours;
            var minutes;
            var amPmLabel = '';

            // If necessary, convert from am/pm to 24-hour time
            if(amPm) {
                var lastTwoChars = time.toLowerCase().slice(-2); // Store am/pm value in amPm var
                if(lastTwoChars == 'am' || lastTwoChars == 'pm') {
                    amPmLabel = lastTwoChars;
                    time = time.slice(0, -2); // Remove am/pm from time
                }
                else {
                    amPmLabel = 'am'; // If no am/pm, assume am
                }

                timeArray = time.split(':');
                hours = timeArray[0];
                minutes = timeArray[1] | 0;

                if(hours < 12 && amPmLabel == 'pm') { // If pm, add 12 hours to get 24 hour time
                    hours = (+hours) + 12;
                }
                else if(hours == 12 && amPmLabel == 'am') { // Convert 12:00am to 0:00
                    hours = 0;
                }
            }
            else {
                timeArray = time.split(':');
                hours = timeArray[0];
                minutes = timeArray[1] | 0;
            }


            var totalMinutes = (+hours) * 60 + (+minutes);

            totalMinutes = totalMinutes + increment;

            if (twentyFourHour) {
                if (totalMinutes > 1439) totalMinutes = 0;
                else if (totalMinutes < 0) totalMinutes = 1439;
            }
            else {
                if (totalMinutes < 0) totalMinutes = 0;
            }

            var totalHours = totalMinutes / 60;
            timeArray = totalHours.toString().split('.');
            hours = timeArray[0];
            minutes = pad(totalMinutes - (hours * 60));

            // Convert back to am/pm if necessary
            if(amPm) {
                if(hours > 11) {
                    amPmLabel = 'pm';
                    if(hours > 12) {
                        hours = hours - 12;
                    }
                }
                else {
                    amPmLabel = 'am';
                    if(hours == 0) { // Convert 0:00 to 12:00am
                        hours = 12;
                    }
                }

            }
            time = hours + ':' + minutes + amPmLabel;

        }

        return time;
    }
});