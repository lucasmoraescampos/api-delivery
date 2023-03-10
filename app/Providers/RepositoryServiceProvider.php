<?php

namespace App\Providers;

use App\Repositories\EloquentRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\CardRepository;
use App\Repositories\CardRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\CategoryRepositoryInterface;
use App\Repositories\CompanyRepository;
use App\Repositories\CompanyRepositoryInterface;
use App\Repositories\ComplementRepository;
use App\Repositories\ComplementRepositoryInterface;
use App\Repositories\DeliverymanRepository;
use App\Repositories\DeliverymanRepositoryInterface;
use App\Repositories\LocationRepository;
use App\Repositories\LocationRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\PlanRepository;
use App\Repositories\PlanRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Repositories\SegmentRepository;
use App\Repositories\SegmentRepositoryInterface;
use App\Repositories\SubcomplementRepository;
use App\Repositories\SubcomplementRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PlanRepositoryInterface::class, PlanRepository::class);
        $this->app->bind(CardRepositoryInterface::class, CardRepository::class);
        $this->app->bind(CompanyRepositoryInterface::class, CompanyRepository::class);
        $this->app->bind(DeliverymanRepositoryInterface::class, DeliverymanRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(SegmentRepositoryInterface::class, SegmentRepository::class);
        $this->app->bind(ComplementRepositoryInterface::class, ComplementRepository::class);
        $this->app->bind(SubcomplementRepositoryInterface::class, SubcomplementRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
