<?php

namespace App\Repositories;

interface CompanyRepositoryInterface
{
    /**
     * @param mixed $company_id
     * @return boolean
     */
    public static function checkAuth($company_id): bool;
}
