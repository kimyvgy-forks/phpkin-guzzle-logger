# Guzzle Http Logger for phpkin

## Installation

Install dependency via composer.

```
composer require minors/phpkin-guzzle-logger
```

Attach the logger to your tracer instance.

```php
// ...
use Minors\phpkin\GuzzleHttpLogger;
// ...

$logger = new GuzzleHttpLogger([
    'host' => 'http://127.0.0.1:9144', // your zipkin base URI
    'endpoint' => '/api/v1/spans', // optional
    'muteErrors' => true, // optional
    'contextOptions' => [], // optional
]);

$tracer = new Tracer(
    $name,
    $endpoint,
    $logger,
);

```
