<?php

namespace App\Provider;

use App\Repository\Contract\AdminRepositoryInterface;
use App\Repository\Contract\CategoryRepositoryInterface;
use App\Repository\Contract\BannerRepositoryInterface;
use App\Repository\Contract\LocationRepositoryInterface;
use App\Repository\Contract\BlogCategoryRepositoryInterface;
use App\Repository\Contract\BlogRepositoryInterface;
use App\Repository\Contract\SupportTeamRepositoryInterface;
use App\Repository\Contract\BankAccountRepositoryInterface;
use App\Repository\Contract\ConsultationRepositoryInterface;
use App\Repository\Contract\TourRepositoryInterface;
use App\Repository\Eloquent\AdminRepository;
use App\Repository\Eloquent\CategoryRepository;
use App\Repository\Eloquent\BannerRepository;
use App\Repository\Eloquent\LocationRepository;
use App\Repository\Eloquent\BlogCategoryRepository;
use App\Repository\Eloquent\BlogRepository;
use App\Repository\Eloquent\SupportTeamRepository;
use App\Repository\Eloquent\BankAccountRepository;
use App\Repository\Eloquent\ConsultationRepository;
use App\Repository\Eloquent\TourRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(BannerRepositoryInterface::class, BannerRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(BlogCategoryRepositoryInterface::class, BlogCategoryRepository::class);
        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
        $this->app->bind(SupportTeamRepositoryInterface::class, SupportTeamRepository::class);
        $this->app->bind(BankAccountRepositoryInterface::class, BankAccountRepository::class);
        $this->app->bind(ConsultationRepositoryInterface::class, ConsultationRepository::class);
        $this->app->bind(TourRepositoryInterface::class, TourRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
