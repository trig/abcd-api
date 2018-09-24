<?php

namespace Test\Abcd\Api;

use Abcd\Api\AuthMiddleware;
use App\Http\Request;
use App\Http\Response;
use Application;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthMiddlewareTest.
 *
 * @covers \Abcd\Api\AuthMiddleware
 */
class AuthMiddlewareTest extends TestCase
{
    /**
     * @var AuthMiddleware $authMiddleware An instance of "AuthMiddleware" to test.
     */
    private $authMiddleware;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->authMiddleware = new AuthMiddleware();
    }

    /**
     * @covers \Abcd\Api\AuthMiddleware::__invoke
     */
    public function testInvokeWithExceptionCanNotDetectApiToken(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)->getMock();
        $responseMock = $this->getMockBuilder(Response::class)->getMock();
        $appMock = $this->getMockBuilder(Application::class)->disableOriginalConstructor()->getMock();

        $this->expectExceptionMessage('Con not detect API token');
        $this->authMiddleware->__invoke($requestMock, $responseMock, $appMock);
    }

    /**
     * @covers \Abcd\Api\AuthMiddleware::__invoke
     */
    public function testInvokeWithExceptionUnrecognizedToken(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)->getMock();
        $responseMock = $this->getMockBuilder(Response::class)->getMock();

        $requestMock->headers['Authorization'] = 'Bearer token';

        $appMock = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appMock->method('offsetGet')
            ->with('config')
            ->willReturn(['api_tokens' => ['test_token']]);

        $this->expectExceptionMessage('Your API token is not registered in system');
        $this->authMiddleware->__invoke($requestMock, $responseMock, $appMock);
    }

    /**
     * @covers \Abcd\Api\AuthMiddleware::__invoke
     */
    public function testInvoke(): void
    {
        $requestMock = $this->getMockBuilder(Request::class)->getMock();
        $responseMock = $this->getMockBuilder(Response::class)->getMock();

        $requestMock->headers['Authorization'] = 'Bearer test_token';

        $appMock = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $appMock->method('offsetGet')
            ->with('config')
            ->willReturn(['api_tokens' => ['test_token']]);

        $response = $this->authMiddleware->__invoke($requestMock, $responseMock, $appMock);
        $this->assertInstanceOf(Response::class, $response);
    }
}
