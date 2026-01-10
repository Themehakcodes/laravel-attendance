<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'));

        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send data message to Flutter app
     */
    public function sendDataMessage(string $fcmToken, array $data): bool
    {
        try {
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withData($data)
                ->withNotification(Notification::create(
                    'Attendance Marked',
                    $data['message'] ?? 'Your attendance has been marked!'
                ));

            $this->messaging->send($message);

            return true;
        } catch (\Throwable $e) {
            \Log::error("FCM Error: " . $e->getMessage());
            return false;
        }
    }
}
