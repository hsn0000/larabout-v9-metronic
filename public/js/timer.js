

moment.locale('en');
var deviceTime,
    serverTime,
    actualTime,
    timeOffset;
// Run each second lap to show times in real time
var updateDisplay = function() {
    // Show static time data

    $('.date-time').html(actualTime.tz($timezone).format('ddd, MMM DD, YYYY - H:mm:ss'));
    $('.timezone').html('GMT '+actualTime.tz($timezone).format('Z'));

    // console.log(serverTime.format('ddd, MMM DD, YYYY - H:mm:ss'));
};

var timerHandler = function() {
    // Get current time on the device
    actualTime = moment();

    // Add the calculated offset
    actualTime.add(timeOffset);

    // Show our new results
    updateDisplay();

    // Re-run this next second wrap
    setTimeout(timerHandler, (1000 - (new Date().getTime() % 1000)));
};

// Fetch the servern time through a HEAD request to current URL
// using asynchronous request.
var fetchServerTime = function() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onload = function() {
        var dateHeader = xmlhttp.getResponseHeader('Date');

        // Just store the current time on device for display purpose
        deviceTime = moment();

        // Turn the "Date:" header field into a "moment" object,
        // use JavaScript Date() object as parser
        serverTime = moment(new Date(dateHeader)); // Read

        // Store the differences between device time and server time
        timeOffset = serverTime.diff(moment());

        // Now when we've got all data, trigger the timer for the first time
        timerHandler();
    }
    xmlhttp.open("HEAD", window.location.href);
    xmlhttp.send();
}

// Trigger the whole procedure
fetchServerTime();
