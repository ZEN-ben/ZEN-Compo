var socket = io('https://'+window.location.host+':8443/');
socket.on('notification', function(msg) {
    new PNotify(msg);
});

socket.on('overview', function(overview) {
    $.each(overview, function(app, isRunning){
        updateAppState(app, isRunning);
    });
});

socket.on('stopped', function(app) {
    updateAppState(app, false);
});

socket.on('started', function(app) {
    updateAppState(app, true);
});

function updateAppState(app, isRunning) {
    var $appContainer = $('[data-app="' + app + '"]');
    $appContainer.toggleClass('running', isRunning);
    $appContainer.toggleClass('stopped', !isRunning);
    var $progressBar = $appContainer.find('.progress-bar');
    $progressBar.toggleClass('active', isRunning);
    $progressBar.toggleClass('progress-bar-success', isRunning);
    $progressBar.toggleClass('progress-bar-danger', !isRunning);
}