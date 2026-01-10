<?php

namespace App\Services;

use GuzzleHttp\Client;

class FcmService
{
    protected $serverKey;
    protected $client;

    public function __construct()
    {
        $this->serverKey = env('FIREBASE_SERVER_KEY'); // Add this to .env
        $this->client = new Client();
    }

    /**
     * Send a data message via FCM
     */
    public function sendMessage(string $fcmToken, string $phone, string $message)
    {
        $payload = [
            'to' => $fcmToken,
            'data' => [
                'phone' => $phone,
                'message' => $message,
            ],
        ];

        $response = $this->client->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($payload),
        ]);

        return json_decode((string) $response->getBody(), true);
    }
}
