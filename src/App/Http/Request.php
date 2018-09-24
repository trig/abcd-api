<?php

namespace App\Http;

class Request
{
    /**
     * @var array
     */
    public $headers = [];

    /**
     * @var array
     */
    public $request = [];

    /**
     * @var array
     */
    public $query = [];

    /**
     * @var array
     */
    public $files = [];

    /**
     * @var array
     */
    public $cookies = [];

    /**
     * @var array
     */
    public $server = [];

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * Request constructor.
     * @param array|null $headers
     * @param array|null $request
     * @param array|null $query
     * @param array|null $files
     * @param array|null $cookies
     * @param array|null $server
     */
    public function __construct(array $headers = null, array $request = null, array $query = null, array $files = null, array $cookies = null, array $server = null)
    {
        $this->headers = $headers ?? $this->prepareHeaders();
        $this->request = $post ?? $_POST;
        $this->query = $get ?? $_GET;
        $this->files = $files ?? $_FILES;
        $this->cookies = $cookies ?? $_COOKIE;
        $this->server = $server ?? $_SERVER;
    }

    /**
     * @return null|string
     */
    public function getPathInfo(): ?string
    {
        return $this->server['PATH_INFO'] ?? null;
    }

    /**
     * @return null|string
     */
    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? '');
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return (string)file_get_contents('php://input');
    }

    /**
     * @return array
     */
    private function prepareHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if(0 === strpos($key, 'HTTP_')){
                $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }
}