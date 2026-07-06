<?php

namespace App\Providers;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layouts.app', function ($view): void {
            $user = auth()->user();

            $view->with('moderationNotifications', $user
                ? $user->moderationNotifications()->whereNull('read_at')->latest()->get()
                : collect());
        });

        View::composer('components.admin.sidebar', function ($view): void {
            $view->with(
                'pendingReportCount',
                Report::query()->where('status', ReportStatus::Pending)->count()
            );
        });
    }
}
