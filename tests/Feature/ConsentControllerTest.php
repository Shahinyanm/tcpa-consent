<?php

namespace Tests\Feature\Api;


use App\Models\Company;
use App\Models\Consent;
use App\Repositories\Interfaces\ConsentRepositoryInterface;
use App\Services\TwilioService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConsentControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_create_consent()
    {
        $company = Company::factory()->create();

        $response = $this->postJson('/api/consents', [
            'first_name'   => 'John',
            'last_name'    => 'Doe',
            'phone_number' => '+1234567890',
            'language'     => 'en',
            'company_hash' => $company->hash,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'phone_number',
                    'language',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_can_verify_code()
    {
        $consent = Consent::factory()->verified()->create();

        $response = $this->postJson("/api/consents/{$consent->id}/verify", [
            'code' => '1234',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_cannot_verify_with_invalid_code()
    {
        $consent = Consent::factory()->verified()->create();

        $response = $this->postJson("/api/consents/{$consent->id}/verify", [
            'code' => '0000',
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid verification code']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(TwilioService::class, function ($mock) {
            $mock->shouldReceive('sendVerificationCode')->andReturn();
            $mock->shouldReceive('sendConsentRequest')->andReturn();
        });

        $this->mock(ConsentRepositoryInterface::class, function ($mock) {
            $mock->shouldReceive('create')
                ->andReturn(Consent::factory()->make());
            $mock->shouldReceive('updateStatus')->andReturn();
            $mock->shouldReceive('findByPhoneNumber')
                ->andReturn(Consent::factory()->make(['status' => 'verified']));
        });
    }
}
