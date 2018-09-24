<?php

namespace App\Http;

class Route
{

    /**
     * @var string
     */
    private $method;

    /**
     * @var
     */
    private $pattern;

    /**
     * @var callable
     */
    private $callable;

    /**
     * Route constructor.
     * @param string $method
     * @param string $pattern
     * @param callable $callable
     */
    public function __construct(string $method, string $pattern, $callable)
    {
        $this->method = strtoupper($method);
        $this->pattern = $pattern;
        $this->callable = $callable;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return sha1($this->method . $this->pattern);
    }

    /**
     * @return callable
     */
    public function getMiddleware()
    {
        return $this->callable;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function isMatched(Request $request): bool
    {
        $pathInfo = $request->getPathInfo();
        $method = $request->getMethod();
        $pattern = preg_replace('!\{\w+?\}!', '[^/?]*?', $this->pattern);

        return $this->method === $method && !!preg_match("!^{$pattern}$!", $pathInfo);
    }

}