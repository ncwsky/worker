
单文件多进程处理框架

复制于[workerman](https://github.com/walkor/workerman)  

## Installation

```
composer require myphps/worker
```

## Usage
```php
<?php

require __DIR__ . '/Worker.php';

\Worker\Worker::$logFile = __DIR__ . '/log.log';
\Worker\Worker::$pidFile = __DIR__ . '/worker.pid';
$worker = new \Worker\Worker();
// 2 processes
$worker->name = 'test';
$worker->count = 2; //进程数
$worker->alarm = 100; //失败预警值
$worker->onWorkerStart = function (\Worker\Worker $worker) {
    //todo 引用代码
    //增加定时器
    \Worker\Timer::add(1, function () use ($worker) {
        echo 'okT--------------------------------------------------' . $worker->id . ':' . time() . PHP_EOL;
    });
};
$worker->onRun = function (\Worker\Worker $worker) {
    $rand = mt_rand(0, 9); //模拟处理结果
    if ($rand == 0) {
        echo 'fail--------' . $worker->id . ':' . time() . PHP_EOL;
        $result = false; //失败
    } elseif ($rand <= 5) {
        echo 'ok----------' . $worker->id . ':' . time() . PHP_EOL;
        $result = true; //成功
    } else {
        $result = null; //没有任何处理
    }
    return $result; //运行结果
};
$worker->onAlarm = function (\Worker\Worker $worker) {
    echo date("Y-m-d H:i:s") . '................. alarm ................. ' . PHP_EOL;
};
$worker->onWorkerStop = function (\Worker\Worker $worker) {
    echo 'end', PHP_EOL;
};
// Run worker
\Worker\Worker::runAll();
```
