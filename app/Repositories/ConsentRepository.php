<?php

namespace App\Repositories;

use App\DTO\ConsentData;
use App\Enums\ConsentStatus;
use App\Models\Consent;
use App\Repositories\Interfaces\ConsentRepositoryInterface;

class ConsentRepository implements ConsentRepositoryInterface
{
    public function create(ConsentData $data, int $companyId): Consent
    {
        return Consent::query()->create([
            'company_id' => $companyId,
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
            'phone_number' => $data->phoneNumber,
            'language' => $data->language,
            'status' => ConsentStatus::PENDING,
            'verification_code' => $this->generateVerificationCode(),
        ]);
    }

    public function updateStatus(Consent $consent, ConsentStatus $status): bool
    {
        return $consent->update(['status' => $status]);
    }

    public function findByPhoneNumber(string $phoneNumber): ?Consent
    {
        return Consent::query()->where('phone_number', $phoneNumber)->latest()->first();
    }

    private function generateVerificationCode(): string
    {
        return str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }
}
