<?php

namespace Axn\RevertDbDefaultStringLength;

use Axn\RevertDbDefaultStringLength\Console\TransformCommand;
use Axn\RevertDbDefaultStringLength\Transformer;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->app->singleton('command.revert-db-default-string-length.transform', function($app) {
            $transformer = new Transformer($app['db.connection']);

            return new TransformCommand($transformer);
        });

        $this->commands([
            'command.revert-db-default-string-length.transform',
        ]);

        $this->publishes([
            __DIR__.'/../database/migrations/revert_db_default_string_length.php.stub' =>
                database_path('migrations/'.now()->format('Y_m_d_His').'_revert_db_default_string_length.php'),
        ], 'revert-db-default-string-length-migration');
    }
}
