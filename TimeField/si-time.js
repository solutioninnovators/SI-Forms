/**
 * Takes an HH:MM string and returns an integer number of minutes
 */
function timeToMinutes(timeString) {
    var timeArray = timeString.split(':');
    var hours = timeArray[0];
    var minutes = timeArray[1] | 0;
    return (+hours) * 60 + (+minutes);
}

/**
 * Takes an integer number of minutes and returns an HH:MM string
 */
function minutesToTime(totalMinutes) {
    var totalHours = parseInt(totalMinutes) / 60;
    var timeArray = totalHours.toString().split('.');
    var hours = parseInt(timeArray[0]);
    var minutes = pad(totalMinutes - (hours * 60));
    return hours + ':' + minutes;
}

function amPmToMilitary(time) {
    var lastTwoChars = time.toLowerCase().slice(-2); // Store am/pm value in amPm var
    if(lastTwoChars == 'am' || lastTwoChars == 'pm') {
        amPmLabel = lastTwoChars;
        time = time.slice(0, -2); // Remove am/pm from time
    }
    else {
        amPmLabel = 'am'; // If no am/pm, assume am
    }

    var timeArray = time.split(':');
    var hours = timeArray[0];
    var minutes = timeArray[1] | 0;

    if(hours < 12 && amPmLabel == 'pm') { // If pm, add 12 hours to get 24 hour time
        hours = (+hours) + 12;
    }
    else if(hours == 12 && amPmLabel == 'am') { // Convert 12:00am to 0:00
        hours = 0;
    }

    return hours + ':' + pad(minutes);
}

function militaryToAmPm(time) {
    var timeArray = time.split(':');
    var hours = timeArray[0];
    var minutes = timeArray[1] | 0;

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

    return hours + ':' + pad(minutes) + amPmLabel;
}

/**
 * Add leading 0 to numbers less than 10
 */
function pad(n) {
	return (n < 10) ? ("0" + n) : n;
}