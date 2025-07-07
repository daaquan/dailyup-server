<?php
$app = require __DIR__ . '/../app/bootstrap.php';

// use sqlite for tests
$app->di->setShared('db', function() {
    return new Phalcon\Db\Adapter\Pdo\Sqlite(['dbname' => ':memory:']);
});
$app->di->setShared('redis', function() {
    $redis = new Redis();
    $redis->connect('127.0.0.1');
    return $redis;
});

return $app;
