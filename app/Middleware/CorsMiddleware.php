<?php
namespace App\Middleware;

use Phalcon\Di\Injectable;

class CorsMiddleware extends Injectable
{
    public function __invoke(): bool
    {
        $this->response->setHeader('Access-Control-Allow-Origin', '*.yourdomain.com');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Authorization, Content-Type');
        if ($this->request->isOptions()) {
            $this->response->send();
            return false;
        }
        return true;
    }
}

