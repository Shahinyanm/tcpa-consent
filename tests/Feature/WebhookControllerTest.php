<?php

namespace Tests\Feature\Api;

use App\Models\Consent;
use App\Repositories\Interfaces\ConsentRepositoryInterface;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WebhookControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Mocking ConsentRepository
        $this->mock(ConsentRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('findByPhoneNumber')
                ->andReturnUsing(function ($phoneNumber) {
                    if ($phoneNumber === '+1234567890') {
                        return Consent::factory()->make(['status' => 'verified']);
                    }
                    return null;
                });
            $mock->shouldReceive('updateStatus')->andReturn();
        });

        // Mocking SmsLogRepository
        $this->mock(\App\Repositories\Interfaces\SmsLogRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('create')->andReturn();
        });
    }

    public function test_can_handle_sms_response()
    {
        $response = $this->postJson('/api/webhook/sms', [
            'From' => '+1234567890',
            'Body' => 'YES',
        ]);

        $response->assertStatus(200);
    }

    public function test_can_handle_sms_decline()
    {
        $response = $this->postJson('/api/webhook/sms', [
            'From' => '+1234567890',
            'Body' => 'NO',
        ]);

        $response->assertStatus(200);
    }

    public function test_cannot_handle_sms_for_nonexistent_consent()
    {
        $response = $this->postJson('/api/webhook/sms', [
            'From' => '+1111111111',
            'Body' => 'YES',
        ]);

        $response->assertStatus(200);
    }
}
