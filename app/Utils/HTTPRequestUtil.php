<?php

namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\{
    ClientException,
    ServerException,
    ConnectException,
    RequestException
};
use Illuminate\Support\Facades\Log;

/**
 * HTTPRequestUtil
 * 
 * A utility class for making HTTP requests using Guzzle.
 * Returns a standardized AppResponse object with success, data, error, and status code.
 */
class HTTPRequestUtil
{
    /**
     * Send an HTTP request using Guzzle
     *
     * @param string $method  HTTP method (GET, POST, PUT, DELETE, etc.)
     * @param string $url     The full URL to request
     * @param array  $options Guzzle-compatible options array (headers, json, form_params, etc.)
     *
     * @return AppResponse
     */
    public static function send(string $method, string $url, array $options = []): AppResponse
    {
        $resp = new AppResponse(); // Initialize standard response
        $client = new Client([
            'timeout' => 20,
            'connect_timeout' => 20,
        ]); // Guzzle client with timeout

        try {
            // Attempt the request
            $response = $client->request(strtoupper($method), $url, $options);

            return $resp
                ->setSuccess(true)
                ->setCode($response->getStatusCode())
                ->setData($response->getBody()->getContents());

        } catch (ClientException | ServerException | ConnectException | RequestException $e) {
            // Handle known Guzzle-related exceptions
            return self::handleException($e, $method, $url, $options, $resp);
        } catch (\Exception $e) {
            // Handle any other unexpected exceptions
            return self::handleException($e, $method, $url, $options, $resp, true);
        }
    }

    /**
     * Handle all exceptions thrown during the HTTP request
     *
     * @param \Throwable   $e         The exception caught
     * @param string       $method    The HTTP method attempted
     * @param string       $url       The requested URL
     * @param array        $options   Guzzle options used
     * @param AppResponse  $resp      The response object to return
     * @param bool         $isGeneric Whether this was a generic exception
     *
     * @return AppResponse
     */
    protected static function handleException(
        \Throwable $e,
        string $method,
        string $url,
        array $options,
        AppResponse $resp,
        bool $isGeneric = false
    ): AppResponse {
        // Get calling file/line (2 steps up the stack)
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[1] ?? [];

        $file = $caller['file'] ?? 'unknown';
        $line = $caller['line'] ?? 'unknown';

        $fullBody = '';
        
        if ($e instanceof RequestException && $e->hasResponse()) {
            $response = $e->getResponse();
            $fullBody = (string) $response->getBody();  // FULL untruncated body
            $status = $response->getStatusCode();
        }
        Log::error('[HTTPRequestUtil] ' . ($isGeneric ? 'Unexpected error' : "{$method} request failed"), [
            'exception' => $e,
            'fullBody' => $fullBody,
            'url' => $url,
            'method' => $method,
            'options' => $options,
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
            'file' => $file,
            'line' => $line,
        ]);

        return $resp
            ->setSuccess(false) 
            ->setCode($e->getCode() ?: 500)
            ->setErrors(['errorMessage' => $e->getMessage(), 'fullErrorBody' => $fullBody]);
            
    }

}