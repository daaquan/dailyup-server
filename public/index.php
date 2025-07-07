<?php
$app = require __DIR__ . '/../app/bootstrap.php';

$app->before(function() use ($app) {
    $uri = $app->request->getURI();
    if ($uri === '/login') {
        return true;
    }
    $header = $app->request->getHeader('Authorization');
    if (!$header || !preg_match('/Bearer\s+(.*)/', $header, $m)) {
        $app->response->setStatusCode(401, 'Unauthorized')->send();
        return false;
    }
    try {
        $app->di->get(App\Services\JwtService::class)->validate($m[1]);
    } catch (\Throwable $e) {
        $app->response->setStatusCode(401, 'Unauthorized')->send();
        return false;
    }
    return true;
});

require __DIR__ . '/../app/routes.php';

if (php_sapi_name() !== 'cli') {
    $app->handle($_SERVER['REQUEST_URI']);
}

return $app;
