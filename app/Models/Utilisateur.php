<?php

namespace App\Models;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Teletravailleur;
use App\Models\Administrateur;
use App\Models\Calendar;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\Notifications\CustomResetPasswordNotification;

class Utilisateur extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable;

    protected $table = 'utilisateurs';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'last_activity',
        'statut',

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'statut' => 'boolean',
        'email_verified_at' => 'datetime',
         'last_activity' => 'datetime',
    ];

    public function teletravailleur(): HasOne
    {
        return $this->hasOne(Teletravailleur::class, 'user_id');
    }

    public function administrateur(): HasOne
    {
        return $this->hasOne(Administrateur::class, 'user_id');
    }

    public function calendars(): HasMany
    {
        return $this->hasMany(Calendar::class, 'user_id');
    }



    public function getAuthPassword()
    {
        return $this->password;
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function isOnline()
    {
        $inactivityLimit = now()->subMinutes(10);
        $isOnline = ($this->last_activity && $this->last_activity->gt($inactivityLimit)) ||
                    (Cache::has('user-is-online-' . $this->id) && $this->last_activity && $this->last_activity->gt($inactivityLimit));
        return $isOnline;
    }

    public function updateStatut()
    {
        if ($this->utilisateur) {
            $this->utilisateur->last_activity = now();
            $this->utilisateur->statut = true;
            $this->utilisateur->save();
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }





    public function getAuthIdentifierName()
    {
        return 'email';
    }

    public function getAuthIdentifier()
    {
        return $this->email;
    }


}
