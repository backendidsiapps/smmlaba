<?php

namespace Smmlaba;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

/**
 * Class AdminPanelServiceProvider
 *
 * @package MulticahatServiceProvider
 */
class SmmlabaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    //    protected $defer = true;

    public function boot()
    {
        $this->publishes(
            [
                __DIR__ . '/config/smmlaba.php' => config_path('smmlaba.php'),
            ], 'config'
        );
    }

    /**
     *
     */
    public function register()
    {
        App::make(SmmLaba::class);
    }
}
