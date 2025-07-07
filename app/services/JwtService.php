<?php
namespace App\Services;

use Phalcon\Encryption\Security\JWT\Token\Parser;
use Phalcon\Encryption\Security\JWT\Token\Validator;
use Phalcon\Encryption\Security\JWT\Signer\Hmac;
use Phalcon\Encryption\Security\JWT\Builder;
use Phalcon\Encryption\Security\JWT\Serializer\Jws;

class JwtService
{
    private string $secret;

    public function __construct(array $config)
    {
        $this->secret = $config['secret'] ?? 'secret';
    }

    public function issue(string $sub): string
    {
        $signer = new Hmac();
        $builder = new Builder($signer);
        $now = time();
        $builder->setIssuedAt($now)
            ->setExpiration($now + 3600)
            ->setSubject($sub);
        $token = $builder->getToken();
        $serializer = new Jws();
        return $serializer->serialize($token, $this->secret);
    }

    public function validate(string $token)
    {
        $serializer = new Jws();
        $parsed = $serializer->parse($token);
        $signer = new Hmac();
        $parser = new Parser();
        $validator = new Validator($parser->parse($parsed));
        if (!$validator->validateSignature($signer, $this->secret)) {
            throw new \Exception('Invalid signature');
        }
        if ($validator->getPayload()['exp'] < time()) {
            throw new \Exception('Token expired');
        }
        return $validator->getPayload();
    }
}
