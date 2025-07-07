<?php
return [
    'adapter'  => getenv('DB_ADAPTER') ?: 'Mysql',
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'username' => getenv('DB_USERNAME') ?: 'root',
    'password' => getenv('DB_PASSWORD') ?: '',
    'dbname'   => getenv('DB_NAME') ?: 'dailyup',
    'charset'  => 'utf8mb4'
];
