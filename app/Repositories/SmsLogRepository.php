<?php

namespace App\Repositories;


use App\Models\SmsLog;
use App\Repositories\Interfaces\SmsLogRepositoryInterface;
use Illuminate\Support\Collection;

class SmsLogRepository implements SmsLogRepositoryInterface
{

    public function all(): Collection
    {
        return SmsLog::query()->get();
    }

    public function create(array $data): SmsLog
    {
        return SmsLog::query()->create($data);
    }

    public function find(int $id): ?SmsLog
    {
        return SmsLog::query()->find($id);
    }
}
