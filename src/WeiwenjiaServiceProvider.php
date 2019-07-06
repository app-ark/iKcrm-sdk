<?php

namespace Weiwenjia;

use Illuminate\Support\ServiceProvider;

class WeiwenjiaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->publishConfig();

        foreach (collect(Weiwenjia::API_DOMAIN)->keys() as $type) {
            app()->singleton('weiwenjia_' . $type, function () use ($type) {
                return new Weiwenjia($type);
            });
        }

        app()->singleton('weiwenjia', function () {
            return new Weiwenjia();
        });
    }

    protected function publishConfig()
    {
        if (!app()->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/config.php' => config_path('weiwenjia.php'),
        ], 'weiwenjia');
    }
}
