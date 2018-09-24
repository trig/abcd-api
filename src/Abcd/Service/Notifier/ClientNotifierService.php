<?php

namespace Abcd\Service\Notifier;

use Abcd\Contracts\UserProviderContract;

class ClientNotifierService
{
    /**
     * @var UserProviderContract
     */
    protected $userProvider;

    public function __construct(UserProviderContract $provider)
    {
        $this->userProvider = $provider;
    }

    /**
     * @param string $token
     */
    public function notifyClientAboutDailyLimit(string $token)
    {
        if ($email = $this->userProvider->getClientEmailByToken($token)) {
            mail(
                $email,
                'Abcd Daily API usage limit exhausted',
                "Dear, customer, we noticing you about your daily API usage by token '{$token}' limit hit. Please be aware and refill you account"
            );
        }
    }

    public function notifyClientAboutTotalLimit()
    {
        foreach ($this->userProvider->getAllClientEmails() as $email) {
            mail(
                $email,
                'Abcd Monthly API limit exhausted',
                "Dear, customer, we noticing you about our Monthly API usage limit hit. Please be patient, we bring it back soon."
            );
        }
    }

}