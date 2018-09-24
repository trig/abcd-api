<?php

namespace Test\Abcd\Service\UserProvider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Abcd\Service\UserProvider\ConfigUserProvider;

/**
 * Class ConfigUserProviderTest.
 *
 * @covers \Abcd\Service\UserProvider\ConfigUserProvider
 */
class ConfigUserProviderTest extends TestCase
{
    /**
     * @var ConfigUserProvider $configUserProvider An instance of "ConfigUserProvider" to test.
     */
    private $configUserProvider;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->configUserProvider = new ConfigUserProvider([
            'tok1' => 'user@null.com',
            'tok2' => 'user2@null.com',
        ]);
    }

    /**
     * @covers \Abcd\Service\UserProvider\ConfigUserProvider::getClientEmailByToken
     */
    public function testGetClientEmailByToken(): void
    {
        $this->assertEquals('user@null.com', $this->configUserProvider->getClientEmailByToken('tok1'));
    }

    /**
     * @covers \Abcd\Service\UserProvider\ConfigUserProvider::getAllClientEmails
     */
    public function testGetAllClientEmails(): void
    {
        $this->assertEquals([
            'user@null.com',
            'user2@null.com',
        ], $this->configUserProvider->getAllClientEmails());
    }
}
