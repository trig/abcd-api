<?php

namespace Test\App\Http;

use App\Http\Request;
use App\Http\Route;
use PHPUnit\Framework\TestCase;

/**
 * Class RouteTest.
 *
 * @covers \App\Http\Route
 */
class RouteTest extends TestCase
{
    /**
     * @covers \App\Http\Route::getHash
     */
    public function testGetHash(): void
    {
        $route = new Route('get', '/some/path', function () {
        });

        $expectedHAsh = sha1('GET/some/path');
        $this->assertEquals($expectedHAsh, $route->getHash());
    }

    /**
     * @covers \App\Http\Route::isMatched
     */
    public function testIsMatched(): void
    {
        $route = new Route('get', '/some/path', function () {
        });

        $probes = [
            '/some/path' => 'True',
            '/some/path/another' => 'False',
            '/some/another' => 'False',
        ];

        foreach($probes as $path => $result) {
            $request = new Request(null, null, null, null, null, [
                'PATH_INFO' => $path,
                'REQUEST_METHOD' => 'GET'
            ]);

            $this->{'assert' . $result}($route->isMatched($request), "Path $path must matched with result: {$result}");
        }
    }
}
