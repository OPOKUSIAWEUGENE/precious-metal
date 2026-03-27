<?php

namespace App\Utils;


use Illuminate\Support\Facades\Log;

class SseRequestUtil
{
    /**
     * Stream SSE (Server-Sent Events) data from a URL
     *
     * @param string $url     SSE endpoint
     * @param array  $headers Optional HTTP headers
     * @param int    $timeout Stream timeout in seconds
     *
     * @return AppResponse
     */
    public static function stream(string $url, array $headers = [], int $timeout = 30): AppResponse
    {
        $resp = new AppResponse();
        $eventData = '';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => array_merge([
                'Accept: text/event-stream',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
            ], $headers),
            CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$eventData) {
                $eventData .= $data;
                return strlen($data);
            },
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        try {
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $errorNo = curl_errno($ch);
            $errorMsg = curl_error($ch);
            curl_close($ch);

            if ($errorNo !== 0) {
                throw new \Exception($errorMsg, $errorNo);
            }

            return $resp
                ->setSuccess(true)
                ->setCode($httpCode)
                ->setData($eventData);

        } catch (\Throwable $e) {
            return self::handleException($e, $url, $headers, $resp);
        }
    }

    /**
     * Handle streaming exceptions
     *
     * @param \Throwable    $e
     * @param string        $url
     * @param array         $headers
     * @param AppResponse   $resp
     *
     * @return AppResponse
     */
    protected static function handleException(\Throwable $e, string $url, array $headers, AppResponse $resp): AppResponse
    {
        Log::error('[SseRequestUtil] Stream failed', [
            'url' => $url,
            'headers' => $headers,
            'code' => $e->getCode(),
            'message' => $e->getMessage(),
        ]);

        return $resp
            ->setSuccess(false)
            ->setCode($e->getCode() ?: 500)
            ->setError($e->getMessage());
    }
}
