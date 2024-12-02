<?php

namespace App\Repositories\Interfaces;

interface CompanyRepositoryInterface
{
    public function getAllCompanies();
    public function getCompanyByHash($hash);
}
