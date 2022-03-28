<?php
require __DIR__ . '/Worker.php';

\Worker\Worker::$logFile = __DIR__ . '/log.log';
\Worker\Worker::$pidFile = __DIR__ . '/worker.pid';
$worker = new \Worker\Worker();
// 4 processes
$worker->name = 'test';
$worker->count = 2;
$worker->onWorkerStart = function (\Worker\Worker $worker) {
    \Worker\Timer::add(1, function () use ($worker) {
        echo 'okT--------------------------------------------------' . $worker->id . ':'. time(). PHP_EOL;
    });
};
$worker->onRun = function (\Worker\Worker $worker) {
    echo 'ok-' . $worker->id . ':'. time(). PHP_EOL;

    $rand = mt_rand(0,9);
    if ($rand == 0) {
        $result = null; //没有任何处理
    } elseif ($rand == 1) {
        $result = false; //失败
    } else {
        $result = true; //成功
    }
    $worker->runStatus($result); //运行结果
};
$worker->onWorkerStop = function (\Worker\Worker $worker) {
    echo 'end', PHP_EOL;
};
// Run worker
\Worker\Worker::runAll();