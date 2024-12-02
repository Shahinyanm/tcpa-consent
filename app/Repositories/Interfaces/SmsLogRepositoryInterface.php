<?php

namespace App\Repositories\Interfaces;


use App\Models\SmsLog;
use Illuminate\Support\Collection;

interface SmsLogRepositoryInterface
{
    public function create(array $data): SmsLog;

    public function find(int $id): ?SmsLog;

    public function all(): Collection;
}
