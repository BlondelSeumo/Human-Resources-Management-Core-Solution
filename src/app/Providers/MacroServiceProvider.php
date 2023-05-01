<?php

namespace App\Providers;

use App\Models\Core\Macro\BelongsToOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        BelongsToMany::macro('toOne', function () {
            return new BelongsToOne(
                $this->related->newQuery(),
                $this->parent,
                $this->table,
                $this->foreignPivotKey,
                $this->relatedPivotKey,
                $this->parentKey,
                $this->relatedKey,
                $this->relationName
            );
        });
    }
}
