<?php

namespace App\Services;


use App\Exceptions\TwilioServiceException;
use App\Models\Consent;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioService
{
    public function __construct(private Client $client) {}

    /**
     * @throws TwilioServiceException
     */
    public function sendVerificationCode(Consent $consent): void
    {
        $message = "Your verification code is {$consent->verification_code}. Please provide it to the agent to begin the consent process.";
        $this->sendSms($consent->phone_number, $message);
    }

    /**
     * @throws TwilioServiceException
     */
    private function sendSms(string $to, string $body): void
    {
        try {
            $this->client->messages->create(
                $to,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $body,
                ]
            );
        } catch(TwilioException $e) {
            Log::error('Twilio error: ' . $e->getMessage());
            throw new TwilioServiceException('Failed to send SMS: ' . $e->getMessage());
        }
    }

    /**
     * @throws TwilioServiceException
     */
    public function sendConsentRequest(Consent $consent): void
    {
        $message = "Consent Request for {$consent->first_name} {$consent->last_name} at {$consent->phone_number}. " .
                   'Please reply "YES" to confirm that you consent to receive advertisement calls from [Company Name]. ' .
                   'Reply STOP to unsubscribe or HELP for help.';
        $this->sendSms($consent->phone_number, $message);
    }
}
