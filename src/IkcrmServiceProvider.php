<?php
namespace Ikcrm;

use Illuminate\Support\ServiceProvider;

class IkcrmServiceProvider extends ServiceProvider
{
    const CONFIG = __DIR__ . '/config.php';

    public function register()
    {
        $this->publishConfig();

        foreach (collect(Ikcrm::API_DOMAIN)->keys() as $type) {
            app()->singleton('ikcrm_' . $type, function () use ($type) {
                return new Ikcrm($type);
            });
        }

        app()->singleton('ikcrm', function () {
            return new Ikcrm();
        });

        $this->mergeConfigFrom(static::CONFIG, 'ikcrm');
    }

    protected function publishConfig()
    {
        if (!app()->runningInConsole()) {
            return;
        }

        $this->publishes([
            static::CONFIG => config_path('ikcrm.php'),
        ], 'Ikcrm');
    }
}
