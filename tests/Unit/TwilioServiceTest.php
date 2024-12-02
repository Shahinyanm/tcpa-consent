<?php

namespace Tests\Unit;


use App\Exceptions\TwilioServiceException;
use App\Models\Consent;
use App\Services\TwilioService;
use Mockery;
use Tests\TestCase;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioServiceTest extends TestCase
{
    /**
     * @throws TwilioServiceException
     */
    public function test_send_verification_code(): void
    {
        $consent = Consent::factory()->make([
            'phone_number'      => '+1234567890',
            'verification_code' => '1234',
        ]);

        $mockTwilioClient           = Mockery::mock(Client::class);
        $mockMessages               = Mockery::mock();
        $mockTwilioClient->messages = $mockMessages;

        $mockMessages
            ->shouldReceive('create')
            ->once()
            ->with(
                $consent->phone_number,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => "Your verification code is {$consent->verification_code}. Please provide it to the agent to begin the consent process.",
                ]
            );

        $twilioService = new TwilioService($mockTwilioClient);
        $twilioService->sendVerificationCode($consent);
    }

    public function test_send_consent_request(): void
    {
        $consent = Consent::factory()->make([
            'phone_number' => '+1234567890',
            'first_name'   => 'John',
            'last_name'    => 'Doe',
        ]);

        $mockTwilioClient           = Mockery::mock(Client::class);
        $mockMessages               = Mockery::mock();
        $mockTwilioClient->messages = $mockMessages;

        $mockMessages
            ->shouldReceive('create')
            ->once()
            ->with(
                $consent->phone_number,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => "Consent Request for {$consent->first_name} {$consent->last_name} at {$consent->phone_number}. " .
                              'Please reply "YES" to confirm that you consent to receive advertisement calls from [Company Name]. ' .
                              'Reply STOP to unsubscribe or HELP for help.',
                ]
            );

        $twilioService = new TwilioService($mockTwilioClient);
        $twilioService->sendConsentRequest($consent);
    }

    public function test_send_sms_throws_exception_on_twilio_error(): void
    {
        $consent                    = Consent::factory()->make([
            'phone_number' => '+1234567890',
        ]);
        $mockTwilioClient           = Mockery::mock(Client::class);
        $mockMessages               = Mockery::mock();
        $mockTwilioClient->messages = $mockMessages;

        $mockMessages
            ->shouldReceive('create')
            ->once()
            ->andThrow(new TwilioException('Fake Twilio error'));

        $this->expectException(TwilioServiceException::class);
        $this->expectExceptionMessage('Failed to send SMS: Fake Twilio error');

        $twilioService = new TwilioService($mockTwilioClient);
        $twilioService->sendVerificationCode($consent);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
