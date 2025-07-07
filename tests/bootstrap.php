<?php
$app = require __DIR__ . '/../app/bootstrap.php';

// use sqlite for tests
$app->di->setShared('db', function() {
    return new Phalcon\Db\Adapter\Pdo\Sqlite(['dbname' => ':memory:']);
});

return $app;
