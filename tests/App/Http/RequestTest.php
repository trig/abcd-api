<?php

namespace Test\App\Http;

use App\Http\Request;
use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest.
 *
 * @covers \App\Http\Request
 */
class RequestTest extends TestCase
{

    /**
     * @covers \App\Http\Request::getPathInfo
     */
    public function testGetPathInfo(): void
    {
        $request = new Request(null, null, null, null, null, [
            'PATH_INFO' => '/some/path'
        ]);
        $this->assertEquals('/some/path', $request->getPathInfo());
    }

    /**
     * @covers \App\Http\Request::getMethod
     */
    public function testGetMethod(): void
    {
        $request = new Request(null, null, null, null, null, [
            'REQUEST_METHOD' => 'OPTIONS'
        ]);
        $this->assertEquals('OPTIONS', $request->getMethod());
    }
}
