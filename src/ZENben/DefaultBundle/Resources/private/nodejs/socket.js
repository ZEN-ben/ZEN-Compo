var io = require('socket.io')(8080);
var Ps = require('ps-node');
var debounce = require('debounce');
var slowSendStatus = debounce(sendStatus, 250);

var runningApps = [];
var monitorApps = require('./monitor.json');

function monitor() {
    for (var key in monitorApps) {
        var app = monitorApps[key];
        (function(app){
            Ps.lookup({
                command: app.process
            }, function(error, results) {
                var running = results.length > 0;
                if (runningApps.indexOf(app.id) === -1 && running) {
                    slowSendStatus(null);
                    runningApps.push(app.id)
                } else if (runningApps.indexOf(app.id) > -1 && !running) {
                    slowSendStatus(null);
                    var index = runningApps.indexOf(app.id);
                    delete runningApps[index];
                }
            });
        })(app);
    }
}

setInterval(monitor, 1000);

io.on('connection', function (socket) {
    console.log('connected');
    sendStatus(socket);

    socket.on('notification', function(message, socket) {
        receive(message, socket);
    });

    socket.on('disconnect', function () {

    });
});

function sendStatus(socket) {
    var overview = {};
    for (var key in monitorApps) {
        var app = monitorApps[key];
        overview[app.id] = isRunning(app);
    }
    if (socket) {
        socket.emit('overview', overview);
    } else {
        io.emit('overview', overview);
    }
}

function isRunning(app) {
    return runningApps.indexOf(app.id) !== -1;
}

function receive(message, socket) {
    console.log(message);
}

function send(message, socket) {
    io.emit('notification', message);
}


