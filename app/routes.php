<?php
use Phalcon\Mvc\Micro\Collection;

$topic = new Collection();
$topic->setHandler(App\Controllers\TopicController::class, true);
$topic->setPrefix('/api/v1/topics');
$topic->get('/', 'index');
$app->mount($topic);

$app->post('/login', [App\Controllers\AuthController::class, 'login']);
