<?php
namespace App\Middleware;

use OakLabs\PhalconThrottler\RedisThrottler;
use Phalcon\Di\Injectable;

class RateLimitMiddleware extends Injectable
{
    private RedisThrottler $throttler;

    public function __construct()
    {
        $this->throttler = new RedisThrottler(
            $this->di->get('redis'),
            [
                'bucket_size'   => 60,
                'refill_time'   => 60,
                'refill_amount' => 60,
            ]
        );
    }

    public function __invoke(): bool
    {
        $token = $this->request->getBearerToken();
        $key = $token ?: $this->request->getClientAddress();
        $rate = $this->throttler->consume($key);
        if ($rate->isLimitExceeded()) {
            $this->response->setStatusCode(429);
            $this->response->setJsonContent(['error' => 'Too Many Requests']);
            $this->response->send();
            return false;
        }
        return true;
    }
}

