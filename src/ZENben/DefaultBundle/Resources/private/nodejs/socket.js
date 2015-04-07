var fs = require('fs');
var express = require('express');
var cfg = require('./config.json');

var key = fs.readFileSync(cfg.key).toString();
var cert = fs.readFileSync(cfg.cert).toString();

var ca = cfg.ca ? fs.readFileSync(cfg.ca).toString() : null;

var app = require('express')();
var http = require('http');
var https = require('https');
var server = https.createServer({ key: key, cert: cert, ca: ca}, app);
server.listen(8443);
var io = require('socket.io')(server);

var Ps = require('ps-node');
var debounce = require('debounce');
var slowSendStatus = debounce(sendStatus, 1000);

var runningApps = [];
var monitorApps = require('./monitor.json');

function monitor() {
    for (var key in monitorApps) {
        var app = monitorApps[key];
        (function(app){
            Ps.lookup({
                command: app.process
            }, function(error, results) {
                if (error || typeof results === 'undefined ' || results === null) {
                    slowSendStatus(null);
                    return;
                }

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

setInterval(monitor, 5000);

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


