<?php

namespace Abcd\Contracts;

interface UserProviderContract
{

    /**
     * @param string $apiToken
     * @return null|string
     */
    public function getClientEmailByToken(string $apiToken): ?string;

    /**
     * @return array|null
     */
    public function getAllClientEmails(): ?array;

}