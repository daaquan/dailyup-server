<?php

use Illuminate\Foundation\Application;

$app = new Application(
    dirname(__DIR__)
);

$app->router->group(['namespace' => 'App\\Http\\Controllers'], function ($router) {
    require __DIR__.'/../routes/api.php';
});

return $app;
