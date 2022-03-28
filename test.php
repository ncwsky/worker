<?php
require __DIR__ . '/Worker.php';

\Worker\Worker::$logFile = __DIR__ . '/log.log';
\Worker\Worker::$pidFile = __DIR__ . '/worker.pid';
//\Worker\Worker::$blockingTime = 0.001; // 设置为0无处理数据时cpu容易100% 建设默认或自定义值
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
    $rand = mt_rand(0,9);
    if ($rand == 0) {
        echo 'fail--------' . $worker->id . ':'. time(). PHP_EOL;
        $result = false; //失败
    } elseif ($rand <= 5) {
        echo 'ok----------' . $worker->id . ':'. time(). PHP_EOL;
        $result = true; //成功
    } else {
        $result = null; //没有任何处理
    }
    $worker->runStatus($result); //运行结果
};
$worker->onWorkerStop = function (\Worker\Worker $worker) {
    echo 'end', PHP_EOL;
};
// Run worker
\Worker\Worker::runAll();