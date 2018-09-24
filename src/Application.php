<?php

use App\Http\Request;
use App\Http\Response;
use App\Http\Route;

class Application implements \ArrayAccess
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * @var array
     */
    private $resolvedServices = [];

    /**
     * @var array
     */
    private $middleware = [];

    /**
     * @var array
     */
    private $routes = [];

    public function __construct(array $config)
    {
        $this['config'] = $config;
    }

    /**
     * @param string $method
     * @param string $routePath
     * @param $middleware
     */
    public function path(string $method, string $routePath, $middleware)
    {
        if (!is_callable($middleware)) {
            throw new \RuntimeException('Please provide valid callable to Application->path() method');
        }

        $route = new Route($method, $routePath, $middleware);
        $hash = $route->getHash();
        if (isset($this->routes[$hash])) {
            throw new \RuntimeException("Route {$method} {$routePath} already registered");
        }

        $this->routes[$hash] = $route;
    }

    /**
     * @param $middleware
     * @return Application
     */
    public function addMiddleware($middleware): Application
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    public function run()
    {
        $exceptionHandler = $this['config']['exceptionHandler'] ?? null;
        try {
            $request = new Request();
            $route = $this->matchRoute($request);

            $response = new Response($route ? Response::HTTP_OK : Response::HTTP_NOT_FOUND);
            if (!$route) {
                $response->setBody("Route not found for '{$request->getPathInfo()}' path");
            }

            foreach ($this->middleware as $middleware) {
                $response = $middleware($request, $response, $this);
            }

            if ($route) {
                $response = call_user_func_array($route->getMiddleware(), [$request, $response, $this]);
            }
        } catch (\Throwable $e) {
            if ($exceptionHandler && is_callable($exceptionHandler)) {
                $response = $exceptionHandler($e, $this);
            }
        }

        $response->send();
    }

    /**
     * Register a new service
     *
     * @param string $id
     * @param \Closure|mixed $resolver
     * @return Application
     */
    public function define(string $id, $resolver): Application
    {
        if (isset($this->container[$id])) {
            throw new \RuntimeException("The service '{$id}' was already defined before");
        }
        $this->container[$id] = $resolver;
        return $this;
    }

    /**
     * Return previously registered service
     *
     * @param $id
     * @return null
     */
    public function get($id)
    {
        if(isset($this->resolvedServices[$id])){
            return $this->resolvedServices[$id];
        }
        $definition = isset($this->container[$id]) ? $this->container[$id] : null;
        if ($definition && $definition instanceof \Closure && !isset($this->resolvedServices[$id])) {
            return $this->resolvedServices[$id] = $definition($this);
        }
        return $definition;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->define($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        if (isset($this->container[$offset])) {
            unset($this->container[$offset]);
        }
    }

    /**
     * @param Request $request
     * @return Route|null
     */
    private function matchRoute(Request $request): ?Route
    {
        /** @var Route $route */
        foreach ($this->routes as $route) {
            if ($route->isMatched($request)) {
                return $route;
            }
        }
        return null;
    }
}