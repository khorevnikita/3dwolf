<?php

namespace App\Models;

use GuzzleHttp\Client;

class Telegram
{
    public static function request(string $method, array $data = [])
    {
        $key = config('services.tg.key');
        $client = new Client();
        $response = $client->post("https://api.telegram.org/bot$key/$method", [
            "json" => $data
        ]);
        $body = $response->getBody();
        $content = $body->getContents();
        return json_decode($content, true);
    }
}
