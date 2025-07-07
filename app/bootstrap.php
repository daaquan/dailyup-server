<?php
use Phalcon\Di\FactoryDefault;
use Phalcon\Autoload\Loader;
use Phalcon\Config\Config;
use Phalcon\Mvc\Micro;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new FactoryDefault();

$loader = new Loader();
$loader->setNamespaces([
    'App\\Controllers' => __DIR__ . '/controllers/',
    'App\\Models' => __DIR__ . '/models/',
    'App\\Services' => __DIR__ . '/services/',
    'App\\Validation' => __DIR__ . '/validation/',
    'App\\Tasks' => __DIR__ . '/tasks/'
]);
$loader->register();

$configArray = [
    'database' => [
        'adapter'  => getenv('DB_ADAPTER') ?: 'Mysql',
        'host'     => getenv('DB_HOST') ?: 'localhost',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'dbname'   => getenv('DB_NAME') ?: 'dailyup',
        'charset'  => 'utf8mb4'
    ],
    'jwt' => [
        'secret' => getenv('JWT_SECRET') ?: 'secret'
    ]
];

$config = new Config($configArray);
$container->set('config', $config);

$container->setShared('db', function() use ($config) {
    $adapter = $config->database->adapter;
    if ($adapter === 'Sqlite') {
        return new Phalcon\Db\Adapter\Pdo\Sqlite(['dbname' => $config->database->dbname]);
    }
    $class = 'Phalcon\\Db\\Adapter\\Pdo\\' . $adapter;
    return new $class([
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => $config->database->charset
    ]);
});

$container->setShared(App\Services\JwtService::class, function() use ($config) {
    return new App\Services\JwtService($config->jwt->toArray());
});

$app = new Micro($container);

return $app;
