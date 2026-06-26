<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Eloquent\EloquentPostRepository;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Eloquent\EloquentServiceRepository;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Eloquent\EloquentProjectRepository;
use App\Repositories\Contracts\AiIntelligenceRepositoryInterface;
use App\Repositories\Eloquent\AiIntelligenceRepository;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use App\Repositories\Eloquent\EloquentTestimonialRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, EloquentPostRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, EloquentServiceRepository::class);
        $this->app->bind(ProjectRepositoryInterface::class, EloquentProjectRepository::class);
        $this->app->bind(AiIntelligenceRepositoryInterface::class, AiIntelligenceRepository::class);
        $this->app->bind(TestimonialRepositoryInterface::class, EloquentTestimonialRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
