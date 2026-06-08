<?php

namespace App\Providers;

use App\CPU\Helpers;
use App\Model\BusinessSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
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
        try {
            $emailServices_smtp = Helpers::get_business_settings('mail_config');
            if ($emailServices_smtp['status'] == 0) {
                $emailServices_smtp = Helpers::get_business_settings('mail_config_sendgrid');
            }
            if ($emailServices_smtp['status'] == 1) {
                $config = array(
                    'driver' => $emailServices_smtp['driver'],
                    'host' => $emailServices_smtp['host'],
                    'port' => $emailServices_smtp['port'],
                    'username' => $emailServices_smtp['username'],
                    'password' => $emailServices_smtp['password'],
                    'encryption' => $emailServices_smtp['encryption'],
                    'from' => array('address' => $emailServices_smtp['email_id'], 'name' => $emailServices_smtp['name']),
                    'sendmail' => '/usr/sbin/sendmail -bs',
                    'pretend' => false,
                    'stream' => [
                        'ssl' => [
                            'allow_self_signed' => true,
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ],
                );
                Config::set('mail', $config);
            }
        } catch (\Exception $ex) {
            Log::error('MailConfigServiceProvider boot failed: ' . $ex->getMessage());
        }
    }
}
