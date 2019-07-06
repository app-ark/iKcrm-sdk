<?php
namespace Ikcrm;

use Illuminate\Support\ServiceProvider;

class IkcrmServiceProvider extends ServiceProvider
{
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
    }

    protected function publishConfig()
    {
        if (!app()->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/config.php' => config_path('ikcrm.php'),
        ], 'Ikcrm');
    }
}
