<?php
require __DIR__ . '/Worker.php';

\Worker\Worker::$stdoutFile = \Worker\Worker::$logFile = __DIR__ . '/log.log';
\Worker\Worker::$pidFile = __DIR__ . '/worker.pid';
$worker = new \Worker\Worker();
// 4 processes
$worker->count = 4;
$worker->onWorkerStart = function (\Worker\Worker $worker) {
    \Worker\Timer::add(1, function () use ($worker) {
        echo 'okT-' . $worker->id . ':'. time(). PHP_EOL;
    });

    while(1){
        echo 'ok-' . $worker->id . ':'. time(). PHP_EOL;
    }


};
$worker->onWorkerStop = function (\Worker\Worker $worker) {
    echo 'end', PHP_EOL;
};
// Run worker
\Worker\Worker::runAll();