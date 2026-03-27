<?php

namespace App\Utils;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class HTTPStreamUtil
{
    /**
     * Connect to an SSE stream and return the first 'data' event.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $url Target URL
     * @param array $options Guzzle options (query, headers, etc.)
     * @return AppResponse containing the parsed data or error
     */
    public static function listenForFirstEvent(string $method, string $url, array $options = []): AppResponse
    {
        $resp = new AppResponse();

        // Force stream options
        $options['stream'] = true;
        $options['read_timeout'] = $options['read_timeout'] ?? 10;
        $options['decode_content'] = false; // Disable gzip decoding for stream

        // Ensure Accept header is set for SSE if not provided
        $options['headers'] = $options['headers'] ?? [];
        if (!isset($options['headers']['Accept'])) {
            $options['headers']['Accept'] = 'text/event-stream';
        }

        try {
            $client = new Client();
            $response = $client->request(strtoupper($method), $url, $options);

            $stream = $response->getBody()->detach();
            $payload = null;

            if ($stream) {
                while (!feof($stream)) {
                    // Read line by line
                    $line = stream_get_line($stream, 2048, "\n");

                    // Look for lines starting with "data:"
                    if ($line && str_starts_with(trim($line), 'data:')) {
                        $json = trim(substr($line, 5));

                        // Attempt decode
                        $data = json_decode($json, true);

                        if ($data) {
                            $payload = $data;
                            Log::info('[HTTPStreamUtil] SSE data received', $payload);
                            break; // Exit after first data event
                        }
                    }
                }

                // Cleanup
                fclose($stream);
            }

            if ($payload) {
                return $resp->setSuccess(true)
                    ->setCode($response->getStatusCode())
                    ->setData($payload);
            } else {
                return $resp->setSuccess(false)
                    ->setCode(404) // Or appropriate code for "no data found"
                    ->setError("No valid SSE data received from stream.");
            }

        } catch (Exception $e) {
            Log::error('[HTTPStreamUtil] Stream error', [
                'message' => $e->getMessage(),
                'url' => $url
            ]);

            return $resp->setSuccess(false)
                ->setCode(500)
                ->setError($e->getMessage());
        }
    }
}
