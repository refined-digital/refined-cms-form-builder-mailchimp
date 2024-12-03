<?php

namespace RefinedDigital\Mailchimp\Module\Providers;

use Illuminate\Support\ServiceProvider;
use RefinedDigital\Mailchimp\Commands\Install;

class FormBuilderMailchimpServiceProvider extends ServiceProvider
{    /**
 * Bootstrap the application services.
 *
 * @return void
 */
    public function boot()
    {
        try {
            if ($this->app->runningInConsole()) {
                if (\DB::connection()->getDatabaseName() && !file_exists(config_path('newsletter.php'))) {
                    $this->commands([
                        Install::class
                    ]);
                }
            }
        } catch (\Exception $e) {}
    }

}
