<?php

namespace Minors\phpkin;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client as HttpClient;
use whitemerry\phpkin\Logger\SimpleHttpLogger;
use whitemerry\phpkin\Logger\LoggerException;

class GuzzleHttpLogger extends SimpleHttpLogger {

    /**
     * Guzzle-compatible Http Client
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $http;

    /**
     * @inheritdoc
     */
    public function __construct($options = [])
    {
        parent::__construct($options);

        $this->http = new HttpClient([
            'base_uri' => $this->options['host'],
        ]);
    }

    /**
     * Get http client instance.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getHttpClient()
    {
        return $this->http;
    }

    /**
     * @inheritdoc
     */
    public function trace($spans)
    {
        $response = $this->http->send($this->makeRequest($spans));

        if (!$this->options['muteErrors'] && $response->getStatusCode() != 202) {
            throw new LoggerException('Trace upload failed');
        }
    }

    /**
     * Create Http Request
     *
     * @param array $spans
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function makeRequest($spans)
    {
        return new Request(
            'POST',
            $this->options['endpoint'],
            [
                'Content-Type' => 'application/json',
            ],
            \GuzzleHttp\json_encode($spans)
        );
    }

}
