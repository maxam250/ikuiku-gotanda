<?php

namespace App\Providers;

use App\Models\Fanclub;
use App\Models\FanclubContent;
use App\Models\FanclubContentChild;
use App\Policies\FanclubContentChildPolicy;
use App\Policies\FanclubContentPolicy;
use App\Policies\FanclubPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Fanclub::class => FanclubPolicy::class,
        FanclubContent::class => FanclubContentPolicy::class,
        FanclubContentChild::class => FanclubContentChildPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::policy(Fanclub::class, FanclubPolicy::class);
        Gate::policy(FanclubContent::class, FanclubContentPolicy::class);
        Gate::policy(FanclubContentChild::class, FanclubContentChildPolicy::class);
    }
} 