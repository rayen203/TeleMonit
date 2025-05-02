<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teletravailleur extends Model
{
    use HasFactory;

    protected $table = 'teletravailleurs';

    protected $fillable = [
        'user_id',
        'telephone',
        'adresse',
        'photoProfil',
        'CIN',
        'token'
    ];

    protected $hidden = [
        'CIN',
        'token',
    ];



    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkingHour::class, 'teletravailleur_id');
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(Screenshot::class, 'teletravailleur_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'teletravailleur_id');
    }

    public function chatbots()
    {
        return $this->hasOne(Chatbot::class, 'teletravailleur_id');
    }

    public function getTotalHeuresAttribute()
    {
        // Supposons que vous avez une table `working_hours` avec des entrées
        $hours = \App\Models\WorkingHour::where('user_id', $this->user_id)
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->sum('hours');
        return $hours ?: '0H';
    }
}
