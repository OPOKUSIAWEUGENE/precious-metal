<?php

namespace App\Utils;

use App\Exports\ExcelExport;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class HTTPClient
{

    

public static function send(string $url, $key, array $header, string $method, string $content)
{
    $client = new Client();
    $request = new Request('POST',$url, th, db, );

        // Send Request
        return $client->send($request, [
            'Authorization' => "Bearer {$key}",
            'Accept' => 'application\json',
            'headers' =>$header,
            "body" => $content,   
        ]);
}
}