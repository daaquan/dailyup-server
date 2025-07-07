<?php
namespace App\Controllers;

use Phalcon\Mvc\Controller;

class AuthController extends Controller
{
    public function login()
    {
        $jwt = $this->di->get(\App\Services\JwtService::class);
        $token = $jwt->issue('1');
        return $this->response->setJsonContent(['token' => $token]);
    }

    public function refresh()
    {
        $auth = $this->request->getBestHeader('Authorization');
        $token = str_replace('Bearer ', '', $auth);
        $jwt = $this->di->get(\App\Services\JwtService::class);
        $new = $jwt->refresh($token);
        return $this->response->setJsonContent(['token' => $new]);
    }
}
