<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public function send(string $phone, string $message): bool
    {
        $provider = env('SMS_PROVIDER', 'kavenegar');

        if ($provider === 'kavenegar' && config('services.kavenegar.api_key')) {
            return $this->sendKavenegar($phone, $message);
        }

        Log::info("SMS to {$phone}: {$message}");
        return true;
    }

    private function sendKavenegar(string $phone, string $message): bool
    {
        try {
            $apiKey = config('services.kavenegar.api_key');
            $sender = config('services.kavenegar.sender');
            $response = Http::get("https://api.kavenegar.com/v1/{$apiKey}/sms/send.json", [
                'receptor' => $phone,
                'sender'   => $sender,
                'message'  => $message,
            ]);
            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('SMS Failed: ' . $e->getMessage());
            return false;
        }
    }
}