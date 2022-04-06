<?php

namespace Rostami\ShoppingCart;

use Illuminate\Auth\Events\Logout;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;

class ShoppingCartServiceProvider extends ServiceProvider
{
    public function boot()
    {
//        $this->loadMigrationsFrom(__DIR__ . "/Migrations");
    }

    public function register()
    {
        $this->app->bind('cart', 'Rostami\ShoppingCart\Cart');
        $this->mergeConfigFrom(__DIR__ . '/Config/cart.php', 'cart');

        $this->publishes([__DIR__ . '/config/cart.php' => config_path('cart.php')], 'config');

        $this->app['events']->listen(Logout::class, function () {
            if ($this->app['config']->get('cart.destroy_on_logout')) {
                $this->app->make(SessionManager::class)->forget('cart');
            }
        });

        if ( ! class_exists('CreateShoppingcartTable')) {
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/Migrations/0000_00_00_000000_create_shoppingCart_table.php' => database_path('migrations/'.$timestamp.'_create_shoppingcart_table.php'),
            ], 'migrations');
        }
    }
}
