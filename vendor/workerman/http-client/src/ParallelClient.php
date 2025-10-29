<?php

namespace Workerman\Http;

use Throwable;
use Workerman\Coroutine\Parallel;
use Workerman\Http\Client;

/**
 * Parallel client request
 */
#[\AllowDynamicProperties]
class ParallelClient extends Client
{
    /**
     * @var Parallel
     */
    protected Parallel $parallel;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->parallel = new Parallel($options['concurrent'] ?? -1);
        parent::__construct($options);
    }

    /**
     * Push a request to parallel.
     *
     * @param string $url
     * @param array $options
     * @return void
     * @throws Throwable
     */
    public function push(string $url, array $options = []): void
    {
        $this->parallel->add(function () use ($url, $options) {
            return $this->request($url, $options);
        });
    }


    /**
     * Batch requests to parallel.
     *
     * @param array $requests
     * @return void
     * @throws Throwable
     */
    public function batch(array $requests): void
    {
        foreach ($requests as $key => $request) {
            $this->parallel->add(function () use ($request) {
                return $this->request($request[0], $request[1]);
            }, $key);
        }
    }

    /**
     * Wait for all requests to complete.
     *
     * @param bool $errorThrow
     * @return array
     * @throws Throwable
     */
    public function await(bool $errorThrow = false): array
    {
        $results = $this->parallel->wait();
        $exceptions = $this->parallel->getExceptions();
        if ($errorThrow && $exceptions) {
            throw current($exceptions);
        }
        $data = [];
        foreach ($results as $key => $response) {
            $data[$key] = [true, $response];
        }
        foreach ($exceptions as $key => $exception) {
            $data[$key] = [false, $exception];
        }
        ksort($data);
        return $data;
    }

}
