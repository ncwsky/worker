
copy from workerman 单文件仅进程服务

## Installation

```
composer require myphps/worker
```

## Usage
```php
<?php

require __DIR__ . '/Worker.php';

$worker = new \Worker\Worker();
// 4 processes
$worker->count = 4;
$worker->onWorkerStart = function (\Worker\Worker $worker) {
    //todo
};
$worker->onWorkerStop = function (\Worker\Worker $worker) {
    //todo
};
// Run worker
\Worker\Worker::runAll();
```
