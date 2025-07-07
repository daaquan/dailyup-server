<?php
use Phalcon\Di\FactoryDefault\Cli as CliDI;
use Phalcon\Cli\Console;
use Phalcon\Autoload\Loader;

require __DIR__ . '/../vendor/autoload.php';

$di = new CliDI();
$loader = new Loader();
$loader->setNamespaces([
    'App\\Tasks' => dirname(__DIR__) . '/cli/tasks/'
]);
$loader->register();

$console = new Console($di);

$arguments = [];
foreach ($argv as $k => $arg) {
    if ($k == 1) {
        $arguments['task'] = $arg;
    } elseif ($k == 2) {
        $arguments['action'] = $arg;
    }
}

$console->handle($arguments);
