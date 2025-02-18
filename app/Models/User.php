<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Auth\Passwords\CanResetPassword as PasswordsCanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Activitylog\LogOptions;
use Laravel\Sanctum\HasApiTokens;

use function Illuminate\Events\queueable;

class User extends Authenticatable implements HasMedia, CanResetPassword
{
    use Notifiable, HasRoles,  InteractsWithMedia, PasswordsCanResetPassword ;
    use HasApiTokens;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'tenant_id',
        'tenant_db_id',
        'mode',
    ];

    // public $appends = ['user_image'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // protected $appends = ['translated_name'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->keepOriginalImageFormat();
    }

    public function getUserImageAttribute()
    {
        $image = $this->getFirstMedia('users');
        if (empty($image)) {
            return global_asset('assets/media/avatars/013-user_b.png');
        } elseif (!file_exists($image->getPath('thumb'))) {
            return $image->getFullUrl();
        } else {
            return $image->getFullUrl('thumb');
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        $data = LogOptions::defaults()
            ->logOnly(['*', 'register.name'])
            ->useLogName('User');
        return $data;
    }


    #TODO: needs some improvememnts
    public function getRoleAttribute()
    {
        return $this->roles()->first();
    }


    protected static function booted()
    {
        static::saved(queueable(function ($user) {
            self::replicateUserChangesToMaster($user);
        }));

        static::deleted(queueable(function ($user) {
            $user->tenant_id = tenant()->id;

            tenancy()->central(function () use ($user) {
                User::where([
                    'tenant_id' => $user->tenant_id,
                    'tenant_db_id' => $user->id
                ])->delete();
            });
        }));
    }

    protected static function replicateUserChangesToMaster($user)
    {
        $user = (new User())->fill([
            'tenant_id' => tenant()->id,
            'tenant_db_id' => $user->id,
        ] + $user->only(['email', 'phone', 'created_at', 'updated_at']));

        tenancy()->central(function () use ($user) {
            User::withoutEvents(function () use ($user) {
                User::where([
                    'tenant_id' => $user->tenant_id,
                    'tenant_db_id' => $user->tenant_db_id
                ])->first()?->update($user->toArray()) ??
                    User::create($user->toArray());
            });
        });
    }


}
