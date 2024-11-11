<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        // Đặt thời gian sống của Access Token là 1 giờ
        Passport::tokensExpireIn(now()->addHour());

        // Giữ thời gian sống của Refresh Token (ví dụ: 30 ngày)
        Passport::refreshTokensExpireIn(now()->addDays(30));

        // Giữ thời gian sống của Personal Access Token (ví dụ: 6 tháng)
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
    }
}
