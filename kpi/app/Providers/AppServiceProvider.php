<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\{
    CustomerRepositoryInterface,
    ProductRepositoryInterface,
    SupplierRepositoryInterface,
    KPIEntryRepositoryInterface
};

use App\Repositories\{
    CustomerRepository,
    ProductRepository,
    SupplierRepository,
    KPIEntryRepository
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(KPIEntryRepositoryInterface::class, KPIEntryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
