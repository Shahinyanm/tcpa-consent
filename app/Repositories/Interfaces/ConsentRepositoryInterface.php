<?php

namespace App\Repositories\Interfaces;

use App\DTO\ConsentData;
use App\Enums\ConsentStatus;
use App\Models\Consent;

interface ConsentRepositoryInterface
{
    public function create(ConsentData $data, int $companyId): Consent;
    public function updateStatus(Consent $consent, ConsentStatus $status): bool;
    public function findByPhoneNumber(string $phoneNumber): ?Consent;
}
