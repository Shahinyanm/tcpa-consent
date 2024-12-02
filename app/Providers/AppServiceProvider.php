<?php

namespace App\Providers;


use App\Repositories\CompanyRepository;
use App\Repositories\ConsentRepository;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use App\Repositories\Interfaces\ConsentRepositoryInterface;
use App\Repositories\Interfaces\SmsLogRepositoryInterface;
use App\Repositories\SmsLogRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ConsentRepositoryInterface::class, ConsentRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(SmsLogRepositoryInterface::class, SmsLogRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
