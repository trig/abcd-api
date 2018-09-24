# abcd-api
Simple API service which serve calculator `sum()` function.

## Background
1. Client has daily API requests limit of 1000 per day
2. Overall monthly limit is 100000 requests per months.
3. If limit is reached - client will see JSON error along with email notification about API limits

## Requirements
* Only PHP7.1 codebase
* Data storage engines is free to choose
* Info about installation and testing

## Installation
You need [composer.phar](https://getcomposer.org/download/) to be installed on your machine for this project
```
git clone https://github.com/trig/abcd-api.git
cd abcd-api
composer install

```
## Testing with PHPUnit
```
./vendor/bin/phpunit tests
```
## Inspecting API
You need curl cli tool for this
```
# start PHP builtin server:
php -S localhost:8080 -t public

```
Then perform request for main API endpoint:
```
curl -X POST \
  http://localhost:8080/api/calculator/add \
  -H 'Authorization: Bearer zzzaaaqqqwwweeerrr' \
  -d '{"a":0.1, "b":3.4}'
```
You must see response:
```
{
    "result": 3.5
}
```
Client can check how many requests is remaining via HTTP response header `X-RateLimit-Remaining`

### Appendix
Here is some info about application. Main entry point is a `public/index.php`. The you can find main app class `Application`
As well as dependency injection container instantiation and one route definition for above endpoint. Application uses
middleware pattern. Also there is a simple PSR-4 autoloader implemeted in `autoload.php`.



