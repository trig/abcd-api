<?php
error_reporting(E_ALL);
ini_set('display_errors', true);

require_once '../autoload.php';

use Abcd\Api\RateLimiterMiddlewareService;
use Abcd\Service\Cache\FileCacheContract;
use App\Http\Request;
use App\Http\Response;
use Abcd\Service\Notifier\ClientNotifierService;
use Abcd\Service\UserProvider\ConfigUserProvider;
use Abcd\Service\CalculatorService;

$app = new Application([
    'cache_dir' => PROJECT_DIR . '/cache',
    'rate_limiter' => [
        'client_daily' => 1000,
        'total_monthly' => 100000
    ],
    'api_tokens' => [
        'aaabbbcccdddfffeee',
        'zzzaaaqqqwwweeerrr'
    ],
    'client_emails' => [
        'aaabbbcccdddfffeee' => 'vasya@pupkin.com',
        'zzzaaaqqqwwweeerrr' => 'ivan@pupkin.com'
    ],
    'exceptionHandler' => function (\Throwable $e, Application $app) {
        return (new Response($e->getCode() ?: Response::HTTP_BAD_REQUEST))->setJson([
            'errorMessage' => $e->getMessage(),
        ]);
    }
]);

// DI
$app->define('rate_limiter', function (Application $app) {
    $rateLimiter = new RateLimiterMiddlewareService(
        $app->get('cache'),
        $app->get('notifier')
    );
    $config = $app['config']['rate_limiter'];
    return $rateLimiter
        ->setClientDailyLimit($config['client_daily'] ?? 0)
        ->setTotalMonthlyLimit($config['total_monthly'] ?? 0);
});

$app->define('cache', function (Application $app) {
    return new FileCacheContract($app['config']['cache_dir']);
});

$app->define('notifier', function (Application $app) {
    return new ClientNotifierService(
        $app->get('user_provider')
    );
});

$app->define('user_provider', function(Application $app){
    return new ConfigUserProvider($app['config']['client_emails'] ?? []);
});

// Middleware (registration sequence is important here)
$app->addMiddleware(function (Request $req, Response $res, Application $app) {
    if (Response::HTTP_OK !== $res->getStatusCode()) {
        $res = $res->setJson(['error' => $res->getBody() ?: 'Application error']);
    }
    return $res;
});

$app->addMiddleware(new \Abcd\Api\AuthMiddleware());

$app->addMiddleware($app->get('rate_limiter'));

// Routes
$app->path('post', '/api/calculator/add', function (Request $req, Response $res) {
    $requestBody = json_decode($req->getBody(), true);
    if(JSON_ERROR_NONE !== json_last_error()){
        return $res->setBody('Please use JSON request');
    }
    $a = $requestBody['a'] ?? null;
    $b = $requestBody['b'] ?? null;

    if(is_null($a) || is_null($b)){
        return $res->setBody('Please provide request parameters: (float) "a" & "b"');
    }
    $calculator = new CalculatorService();
    return $res->setJson([
        'result' => $calculator->calculateSum((float)$a, (float)$b)
    ]);
});

$app->run();