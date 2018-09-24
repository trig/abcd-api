<?php

namespace Test;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Application;
use App\Http\Request;
use App\Http\Route;

/**
 * Class ApplicationTest.
 *
 * @covers \Application
 */
class ApplicationTest extends TestCase
{

    /**
     * @covers \Application::define
     */
    public function testDefineAndGet(): void
    {
        $app = new Application([]);
        $app->define('service', function(){
            return new \stdClass();
        });
        $this->assertInstanceOf(\stdClass::class, $app->get('service'));
    }

    /**
     * @covers \Application::offsetExists
     */
    public function testArrayAccess(): void
    {
        $app = new Application([]);
        $app['service'] = function(){
            return new \stdClass();
        };
        $this->assertInstanceOf(\stdClass::class, $app['service']);
        unset($app['service']);
        $this->isNull($app->get('service'));
    }

}
