<?php

namespace AgenterLab\Database;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class ModelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Blueprint::macro('authUser', function() {
            $this->unsignedBigInteger('created_by')->index();
            $this->unsignedBigInteger('updated_by')->index();
        });

        Blueprint::macro('softDeletesV1', function() {
            $this->unsignedBigInteger('deleted_at')->default(0);
            $this->unsignedTinyInteger('is_deleted')->default(0)->index();
        });
    }
}

