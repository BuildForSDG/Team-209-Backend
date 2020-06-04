<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $guarded = ["id"];
    protected $dateFormat = 'Y-m-d H:i:s.u';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        return $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }

    /**
     * @param array $validated_values
     * @return array
     */
    public static function preProcess(array $validated_values)
    {
        unset($validated_values["data"]["attributes"]["password_confirmation"]);

        $agent = new Agent();
        $validated_values["data"]["attributes"]["type"] = $agent->isPhone() ?  "mobile" : "web";

        return $validated_values["data"]["attributes"];
    }

    public function getImageAttribute($value)
    {
        return Storage::url("public/images/uploads/profile/".$value);
    }

    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
