<?php

namespace Abcd\Api;

use Abcd\Contracts\CacheContract;
use Abcd\Service\Notifier\ClientNotifierService;
use App\Http\Request;
use App\Http\Response;

class RateLimiterMiddlewareService
{
    const TOTAL_CACHE_ID = 'total_calls';

    /**
     * @var CacheContract;
     */
    private $cache;

    /**
     * @var ClientNotifierService
     */
    private $notifier;

    /**
     * @var int
     */
    private $dailyLimit = 0;

    /**
     * @var int
     */
    private $totalLimit = 0;

    /**
     * RateLimiterMiddlewareService constructor.
     * @param CacheContract $cacheService
     */
    public function __construct(CacheContract $cacheService, ClientNotifierService $notifier)
    {
        $this->cache = $cacheService;
        $this->notifier = $notifier;
    }

    /**
     * @param int $limit
     * @return RateLimiterMiddlewareService
     */
    public function setClientDailyLimit(int $limit): RateLimiterMiddlewareService
    {
        $this->dailyLimit = $limit;
        return $this;
    }

    /**
     * @param int $limit
     * @return RateLimiterMiddlewareService
     */
    public function setTotalMonthlyLimit(int $limit): RateLimiterMiddlewareService
    {
        $this->totalLimit = $limit;
        $this->createTotalLimiterCacheEntry();
        return $this;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param \Application $app
     * @return Response
     * @throws \RuntimeException
     */
    public function __invoke(Request $request, Response $response, \Application $app): Response
    {
        $token = $request->attributes['api_token'] ?? null;
        if (!$token) {
            throw new \RuntimeException("Please provide 'api_token' attribute in one of the middleware service");
        }
        $this->updateTotalApiCalls();
        $this->updateClientApiCalls($token, $response);

        return $response;
    }

    private function createTotalLimiterCacheEntry()
    {
        if (!$this->cache->has(self::TOTAL_CACHE_ID)) {
            $this->cache->put(self::TOTAL_CACHE_ID, $this->totalLimit);
        }
    }

    /**
     * @throws \RuntimeException
     */
    private function updateTotalApiCalls()
    {
        if (0 <= $this->cache->updateCounter(self::TOTAL_CACHE_ID, -1)) {
            return;
        }
        $this->notifier->notifyClientAboutTotalLimit();
        throw new \RuntimeException('Total API usage limit exhausted', Response::HTTP_SERVICE_UNAVAILABLE);
    }

    /**
     * @param string $token
     * @param Response $response
     */
    private function updateClientApiCalls(string $token, Response $response)
    {
        $key = "client_usage_{$token}";
        if (!$this->cache->has($key)) {
            $response->headers['X-RateLimit-Remaining'] = $this->dailyLimit;

            $this->cache->put($key, $this->dailyLimit, 24 * 3600);
        } else {
            $remainingLimit = $this->cache->updateCounter($key, -1);
            $response->headers['X-RateLimit-Remaining'] = $remainingLimit;

            if (0 <= $remainingLimit) {
                return;
            }
            $this->notifier->notifyClientAboutDailyLimit($token);
            throw new \RuntimeException('Daily API usage limit exhausted', Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

}
