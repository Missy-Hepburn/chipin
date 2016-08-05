<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('date_range_check', function($attribute, $value, $parameters, $validator) {
            $validator->addReplacer('date_range_check',
                function($message, $attribute, $rule, $parameters) use ($validator) {
                    $field = !empty($parameters[3])
                            ? array_get($validator->getCustomAttributes(), $parameters[3]) . ' (' .$parameters[2] . ')'
                            : array_get($validator->getCustomAttributes(), $parameters[0], $parameters[0]);

                    return str_replace([':what', ':int'], [$field, $parameters[1]], $message);
                });

            if ($parameters[0] == 'now') {
                $comparedAttribute = Carbon::now();
            }
            elseif (empty($parameters[0]) && !empty($parameters[2])) {
                $comparedAttribute = Carbon::parse($parameters[2]);
            } else {
                $comparedAttribute = Carbon::parse(array_get($validator->getData(), $parameters[0]));
            }

            return Carbon::parse($value) > $comparedAttribute->addHour($parameters[1]);
        });

        Validator::extend('country_code', function($attribute, $value, $parameters, $validator) {
            return in_array(strtoupper($value), Config::get('constants.countries'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
