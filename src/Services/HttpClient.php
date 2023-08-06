<?php

namespace Matmper\Services;

use Matmper\Contracts\HttpRequest;

class HttpClient implements HttpRequest
{
    /**
     * HTTP Code Response 300
     * @var integer
     */
    const HTTP_MULTIPLE_CHOICES = 300;

    /**
     * HTTP Code Response 204
     * @var integer
     */
    const HTTP_NO_CONTENT = 204;

    /**
     * HTTP Code Response 404
     * @var integer
     */
    const HTTP_NOT_FOUND = 404;

    /**
     * Store curl handle
     *
     * @var \CurlHandle
     */
    private $curl;

    /**
     * Curl handler
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->curl = curl_init($url);
    }

    /**
     * Set new CURLOPT
     *
     * @param int $option
     * @param mixed $value
     * @return void
     */
    public function setopt(int $option, $value): void
    {
       curl_setopt($this->curl, $option, $value);
    }

    /**
     * curl_exec and curl_close
     *
     * @return string curl_exec
     */
    public function execute(): string
    {
        try {
            $response = curl_exec($this->curl);
            $responseCode = (int) curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

            curl_close($this->curl);

            if ($responseCode >= self::HTTP_MULTIPLE_CHOICES) {
                throw new \Exception('Failed to perform webhook request', $responseCode);
            }

            if (empty($response) && $responseCode <> self::HTTP_NO_CONTENT) {
                throw new \Exception('Failed to capture request return data', $responseCode);
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        return $response;
    }
}
