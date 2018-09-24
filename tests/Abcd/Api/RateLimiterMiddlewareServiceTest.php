<?php

namespace Test\Abcd\Api;

use Abcd\Api\RateLimiterMiddlewareService;
use Abcd\Contracts\CacheContract;
use Abcd\Service\Notifier\ClientNotifierService;
use App\Http\Request;
use App\Http\Response;
use Application;
use PHPUnit\Framework\TestCase;

/**
 * Class RateLimiterMiddlewareServiceTest.
 *
 * @covers \Abcd\Api\RateLimiterMiddlewareService
 */
class RateLimiterMiddlewareServiceTest extends TestCase
{
    /**
     * @covers \Abcd\Api\RateLimiterMiddlewareService::__invoke
     */
    public function testInvokeWithException(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)->getMock();
        $responseMock = $this->getMockBuilder(Response::class)->getMock();
        $appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
        $cacheMock = $this->getMockBuilder(CacheContract::class)->getMock();
        $notifierMock = $this->getMockBuilder(ClientNotifierService::class)->disableOriginalConstructor()->getMock();

        $midleware = new RateLimiterMiddlewareService($cacheMock, $notifierMock);

        $this->expectExceptionMessage("Please provide 'api_token' attribute in one of the middleware service");
        $midleware->__invoke($requestMock, $responseMock, $appMock);
    }

    /**
     * @covers \Abcd\Api\RateLimiterMiddlewareService::createTotalLimiterCacheEntry
     */
    public function testUpdateTotalApiUsage(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)->getMock();
        $responseMock = $this->getMockBuilder(Response::class)->getMock();
        $appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();
        $cacheMock = $this->getMockBuilder(CacheContract::class)->getMock();
        $notifierMock = $this->getMockBuilder(ClientNotifierService::class)->disableOriginalConstructor()->getMock();


        $cacheMock->method('updateCounter')
            ->with(RateLimiterMiddlewareService::TOTAL_CACHE_ID, -1)
            ->willReturn(100);

        $requestMock->attributes['api_token'] = 'test_token';

        $midleware = new RateLimiterMiddlewareService($cacheMock, $notifierMock);

        $midleware->__invoke($requestMock, $responseMock, $appMock);

        $cacheMock = $this->getMockBuilder(CacheContract::class)->getMock();
        $cacheMock->method('updateCounter')
            ->with(RateLimiterMiddlewareService::TOTAL_CACHE_ID, -1)
            ->willReturn(-1);

        $notifierMock->expects($this->once())
            ->method('notifyClientAboutTotalLimit');

        $midleware = new RateLimiterMiddlewareService($cacheMock, $notifierMock);
        $this->expectExceptionMessage('Total API usage limit exhausted');
        $midleware->__invoke($requestMock, $responseMock, $appMock);
    }

    /**
     * @covers \Abcd\Api\RateLimiterMiddlewareService::updateTotalApiCalls
     */
    public function testUpdateClientApiUsage(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)->getMock();
        $responseMock = $this->getMockBuilder(Response::class)->getMock();
        $appMock = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();
        $cacheMock = $this->getMockBuilder(CacheContract::class)
            ->getMock();
        $notifierMock = $this->getMockBuilder(ClientNotifierService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock->attributes['api_token'] = 'test_token';

        $cacheMock->method('updateCounter')
            ->with(RateLimiterMiddlewareService::TOTAL_CACHE_ID, -1)
            ->willReturn(10);

        $cacheMock->expects($this->once())->method('has')
            ->with('client_usage_test_token')
            ->willReturn(false);

        $cacheMock->expects($this->once())->method('put')
            ->with('client_usage_test_token', 10, 24 * 3600);


        $midleware = new RateLimiterMiddlewareService($cacheMock, $notifierMock);
        $midleware->setClientDailyLimit(10);

        $response = $midleware->__invoke($requestMock, $responseMock, $appMock);
        $this->assertInstanceOf(Response::class, $response);

        $cacheMock = $this->getMockBuilder(CacheContract::class)->getMock();
        $cacheMock->expects($this->once())->method('has')
            ->with('client_usage_test_token')
            ->willReturn(true);
        $cacheMock->method('updateCounter')
            ->willReturnOnConsecutiveCalls(10, -1);

        $notifierMock->expects($this->once())
            ->method('notifyClientAboutDailyLimit');

        $midleware = new RateLimiterMiddlewareService($cacheMock, $notifierMock);

        $this->expectExceptionMessage('Daily API usage limit exhausted');
        $response = $midleware->__invoke($requestMock, $responseMock, $appMock);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertArrayHasKey('X-RateLimit-Remaining', $response->headers);
        $this->assertEquals(10, $response->headers['X-RateLimit-Remaining']);
    }
}
