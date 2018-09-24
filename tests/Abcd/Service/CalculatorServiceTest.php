<?php

namespace Test\Abcd\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Abcd\Service\CalculatorService;

/**
 * Class CalculatorServiceTest.
 *
 * @covers \Abcd\Service\CalculatorService
 */
class CalculatorServiceTest extends TestCase
{
    /**
     * @covers \Abcd\Service\CalculatorService::calculateSum
     */
    public function testCalculateSum(): void
    {
       $service = new CalculatorService();
       $this->assertEquals(5, $service->calculateSum(2.6, 2.4));
    }
}
