<?php

namespace Test\App\Http;

use App\Http\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class ResponseTest.
 *
 * @covers \App\Http\Response
 */
class ResponseTest extends TestCase
{

    /**
     * @covers \App\Http\Response::getBody
     */
    public function testGetBody(): void
    {
        $response = new Response(200, 'test');
        $this->assertEquals('test', $response->getBody());
    }

    /**
     * @covers \App\Http\Response::setBody
     */
    public function testSetBody(): void
    {
        $response = new Response(200);
        $response->setBody('some new contents');
        $this->assertEquals('some new contents', $response->getBody());
    }

    /**
     * @covers \App\Http\Response::getStatusCode
     */
    public function testGetStatusCode(): void
    {
        $response = new Response(500);
        $this->assertEquals(500, $response->getStatusCode());
    }

    /**
     * @covers \App\Http\Response::setJson
     */
    public function testSetJson(): void
    {
        $response = new Response(200);
        $response->setJson(['node' => 'value']);

        $this->assertEquals('application/json;charset=utf-8', $response->headers['Content-Type']);
        $this->assertJson($response->getBody());
        $this->assertEquals(['node' => 'value'], json_decode($response->getBody(), true));
    }

    /**
     * @covers \App\Http\Response::setStatus
     */
    public function testSetStatus(): void
    {
        $response = new Response(200);
        $response->setStatus(502);
        $this->assertEquals(502, $response->getStatusCode());
    }

}
