<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Settings extends Model implements HasMedia
{
    use InteractsWithMedia;
    public $timestamps = false;
    protected $guarded = [];

    public function getDataAttribute($value)
    {
        if (!$value) {
            switch ($this->type) {
                case 'general_settings':
                    $settingsData = (object)[
                        'company_name'                   => null,
                        'default_email'                  => null,
                        'phone'                          => null,
                        'country'                        => null,
                        'city'                           => null,
                        'address'                        => null,
                    ];
                    break;



            }

            return $settingsData;
        }
        return json_decode($value);
    }

}
