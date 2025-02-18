<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as ModelsRole;
use Spatie\Activitylog\LogOptions;


class Role extends ModelsRole
{
    protected $appends = ['translated_name'];

    public function getActivitylogOptions(): LogOptions
    {
        $data = LogOptions::defaults()
            ->logOnly(['*'])
            ->useLogName('Role');
        return $data;
    }

    public function getTranslationsAttribute($value)
    {
        return json_decode($value);
    }

    // separation scope
    public function scopeSeparation($query)
    {
        return $query->where(function ($query) {
            $query->where(function ($query) {
                $query->where('is_super_admin', 1)->orWhere('is_supervisor', 1);
            });
        });
    }


    public function getTranslatedNameAttribute()
    {
        $locale = app()->getLocale();
        if(!app('sharedData')->generalSettings->translate_content_to){
            return $this->name;
        }
        if (!count(app('sharedData')->generalSettings->translate_content_to) || !in_array($locale, app('sharedData')->generalSettings->translate_content_to) || !isset($this->translations->name)) {
            return $this->name;
        }
        $translations      = $this->translations;
        $inputTranslations = $translations->name;
        $inputTranslations = json_decode($inputTranslations, true);

        if (!isset($inputTranslations[$locale])) {
            return $this->name;
        } else {
            return $inputTranslations[$locale];
        }
    }
}
