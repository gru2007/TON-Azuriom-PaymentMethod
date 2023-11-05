<?php

namespace Azuriom\Plugin\TON\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Plugin\TON\TONMethod;
use Illuminate\Console\Scheduling\Schedule;
use Azuriom\Plugin\Shop\Models\Gateway;

class TONServiceProvider extends BasePluginServiceProvider
{
    /**
     * Register any plugin services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $gateway = Gateway::firstWhere('name','ton');
            $method = new TONMethod($gateway);  // correct
            $method->checkPayments();
        })->everyMinute();
    }

    /**
     * Bootstrap any plugin services.
     *
     * @return void
     */
    public function boot()
    {
        if (! plugins()->isEnabled('shop')) {
            logger()->warning('TON нужен плагин Shop для работы !');

            return;
        }

        $this->loadViews();

        $this->loadTranslations();

        $this->registerSchedule();

        payment_manager()->registerPaymentMethod('ton', TONMethod::class);
    }
}
