<?php

namespace Abcd\Service\UserProvider;

use Abcd\Contracts\UserProviderContract;

class ConfigUserProvider implements UserProviderContract
{
    /**
     * @var array
     */
    protected $clientsMap;

    public function __construct(array $clientsMap)
    {
        $this->clientsMap = $clientsMap;
    }

    /**
     * @param string $apiToken
     * @return null|string
     */
    public function getClientEmailByToken(string $apiToken): ?string
    {
        return $this->clientsMap[$apiToken] ?? null;
    }

    /**
     * @return array|null
     */
    public function getAllClientEmails(): ?array
    {
        return array_unique(array_values($this->clientsMap));
    }
}