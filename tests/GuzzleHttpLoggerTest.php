<?php

use Minors\phpkin\GuzzleHttpLogger;

class GuzzleHttpLoggerTest extends PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $logger = new GuzzleHttpLogger([
            'host' => 'http://zipkin_host:9144',
        ]);

        $client = $logger->getHttpClient();

        $this->assertInstanceOf(GuzzleHttp\Client::class, $client);
        $this->assertEquals('http://zipkin_host:9144', $client->getConfig('base_uri'));
    }

    public function testRequestCreatedProperly()
    {
        $method = self::getMethod(GuzzleHttpLogger::class, 'makeRequest');

        $logger = new GuzzleHttpLogger([
            'host' => 'http://zipkin_host:9144',
            'endpoint' => '/test',
        ]);

        $testBody = [
            'foo' => 'bar',
        ];

        $request = $method->invokeArgs($logger, [
            $testBody,
        ]);

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('/test', (string) $request->getUri());
        $this->assertEquals('{"foo":"bar"}', (string) $request->getBody());
        $this->assertEquals(['application/json'], $request->getHeader('Content-Type'));
    }

    public function testRequestIsSent()
    {
        $logger = new GuzzleHttpLogger([
            'host' => 'http://zipkin_host:9144',
            'endpoint' => '/test',
        ]);

        $client = $this->getMockBuilder(GuzzleHttp\Client::class)
                         ->setMethods(['send'])
                         ->getMock();

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function ($request) {
                return $request instanceof Psr\Http\Message\RequestInterface;
            }));

        $reflection = new ReflectionObject($logger);
        $refProperty = $reflection->getProperty('http');
        $refProperty->setAccessible(true);
        $refProperty->setValue($logger, $client);

        $testBody = [
            'foo' => 'bar',
        ];

        $logger->trace($testBody);
    }

    protected static function getMethod($class, $name)
    {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

}
