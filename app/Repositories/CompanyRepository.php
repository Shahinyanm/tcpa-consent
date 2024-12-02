<?php

namespace App\Repositories;


use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository implements CompanyRepositoryInterface
{
    public function getAllCompanies(): Collection
    {
        return Company::query()->get();
    }

    public function getCompanyByHash($hash): Company
    {
        return Company::query()->where('hash', $hash)->first();
    }

}
