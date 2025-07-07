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
    'App\\Tasks' => dirname(__DIR__) . '/cli/tasks/',
    'App\\Middleware' => __DIR__ . '/middleware/'
]);
$loader->register();


$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig  = require __DIR__ . '/../config/db.php';
$jwtConfig = require __DIR__ . '/../config/jwt.php';

$config = new Config($appConfig);
$config->offsetSet('database', new Config($dbConfig));
$config->offsetSet('jwt', new Config($jwtConfig));
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

$container->setShared('redis', function() {
    $redis = new Redis();
    $redis->connect(getenv('REDIS_HOST') ?: 'redis', 6379);
    return $redis;
});

$container->setShared(App\Services\JwtService::class, function() use ($config) {
    return new App\Services\JwtService($config->jwt->toArray());
});

$app = new Micro($container);
$app->before(new App\Middleware\CorsMiddleware());
$app->before(new App\Middleware\RateLimitMiddleware());

return $app;
