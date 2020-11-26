<?php

namespace App\Providers;

use App\Model\Message;
use App\Policies\MessagePolicy;
use App\Policies\UserPolicy;
use App\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        Message::class => MessagePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if ($this->app->environment() === 'local') {

            if (isset($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'])) {
                file_put_contents('php://stdout', "\e[33m[HTTP::{$_SERVER['REQUEST_METHOD']}] \e[0m{$_SERVER['REQUEST_URI']}\n");
            }

            DB::listen(function (QueryExecuted $query) {
               file_put_contents('php://stdout', "\e[34m{$query->sql}\t\e[37m" . json_encode($query->bindings) . "\t\e[32m{$query->time}ms\e[0m\n" );
            });


        }
    }
}
