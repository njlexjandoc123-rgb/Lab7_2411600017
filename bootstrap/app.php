<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Providers\ViewServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__."/../routes/web.php",
        commands: __DIR__."/../routes/console.php",
        health: "/up",
    )
    ->withProviders([ViewServiceProvider::class])
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('inventory:check-reorder-points')
            ->daily()
            ->withoutOverlapping()
            ->onFailure(function (): void {
                Log::error('Scheduled reorder point check failed.');
                User::query()->whereIn('role', ['admin', 'manager'])->whereNotNull('email')->pluck('email')
                    ->each(fn (string $email) => Mail::raw(
                        'The scheduled inventory reorder check failed. Review the application log before running it again.',
                        fn ($message) => $message->to($email)->subject('Inventory reorder check failed')
                    ));
            });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
