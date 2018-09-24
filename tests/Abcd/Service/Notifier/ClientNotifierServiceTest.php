<?php

namespace Test\Abcd\Service\Notifier;

use Abcd\Contracts\UserProviderContract;
use Abcd\Service\Notifier\ClientNotifierService;
use PHPUnit\Framework\TestCase;

/**
 * Class ClientNotifierServiceTest.
 *
 * @covers \Abcd\Service\Notifier\ClientNotifierService
 */
class ClientNotifierServiceTest extends TestCase
{
    /**
     * @covers \Abcd\Service\Notifier\ClientNotifierService::notifyClientAboutDailyLimit
     */
    public function testNotifyClientAboutDailyLimit(): void
    {
        $providerMock = $this->getMockBuilder(UserProviderContract::class)->getMock();
        $providerMock->expects($this->once())
            ->method('getClientEmailByToken')
            ->with('token')
            ->willReturn('vasya@ivanov.com');

        $notifier = new ClientNotifierService($providerMock);
        $notifier->notifyClientAboutDailyLimit('token');
    }

    /**
     * @covers \Abcd\Service\Notifier\ClientNotifierService::notifyClientAboutTotalLimit
     */
    public function testNotifyClientAboutTotalLimit(): void
    {
        $providerMock = $this->getMockBuilder(UserProviderContract::class)->getMock();
        $providerMock->expects($this->once())
            ->method('getAllClientEmails')
            ->willReturn(['vasya@ivanov.com']);

        $notifier = new ClientNotifierService($providerMock);
        $notifier->notifyClientAboutTotalLimit();
    }
}
