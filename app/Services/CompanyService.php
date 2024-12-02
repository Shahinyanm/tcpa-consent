<?php

namespace App\Services;

use App\Repositories\Interfaces\CompanyRepositoryInterface;

readonly class CompanyService
{

    public function __construct(protected CompanyRepositoryInterface $companyRepository)
    {
    }

    public function getAllCompanies()
    {
        return $this->companyRepository->getAllCompanies();
    }

    public function getCompanyByHash($hash)
    {
        return $this->companyRepository->getCompanyByHash($hash);
    }
}
