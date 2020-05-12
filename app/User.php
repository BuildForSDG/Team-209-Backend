<?php

namespace App;

use App\Http\Requests\StoreUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
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
     * @param StoreUser $request
     * @return array
     */
    public static function preProcess(StoreUser $request)
    {
        $validated_values = $request->validated();
        unset($validated_values['image']);

        if ($request->hasFile('image')) {
            $image_name = time() . '.' . $request->file('image')->clientExtension();
            $request->file('image')->storeAs('public/images/uploads', $image_name, ["visibility" => "public"]);
            $validated_values['image'] = $image_name;
        }

        $agent = new Agent();
        $validated_values["type"] = $agent->isPhone() ?  "mobile" : "web";

        return $validated_values;
    }
}
