<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// 👉 Tes imports ajoutés
use App\Models\Acte;
use App\Policies\ActePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 👉 Ton ajout
        Acte::class => ActePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Exemple de Gate (facultatif)
        // Gate::define('view-dashboard', function ($user) {
        //     return $user->role === 'admin';
        // });
    }
}
