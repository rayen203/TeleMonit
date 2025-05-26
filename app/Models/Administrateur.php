<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Collection;
use App\Models\Utilisateur;
use App\Models\Teletravailleur;
use App\Models\Screenshot;
use App\Models\WorkingHour;

class Administrateur extends Model
{
    use HasFactory;

    protected $table = 'administrateurs';

    protected $fillable = [
        'user_id',
        'dateCreation',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }


    public function teletravailleurs(): HasManyThrough
    {
        return $this->hasManyThrough(
            Teletravailleur::class,
            Utilisateur::class,
            'id',
            'user_id',
            'user_id',
            'id'
        );
    }


    public function screenshots(): HasManyThrough
    {
        return $this->hasManyThrough(
            Screenshot::class,
            Teletravailleur::class,
            'user_id',
            'teletravailleur_id',
            'user_id',
            'id'
        );
    }


    public function workingHours(): HasManyThrough
    {
        return $this->hasManyThrough(
            WorkingHour::class,
            Teletravailleur::class,
            'user_id',
            'teletravailleur_id',
            'user_id',
            'id'
        );
    }


    public static function getOrCreate(): self
    {
        $admin = self::first();
        if (!$admin) {
            $user = Utilisateur::where('email', 'admin@example.com')->firstOrFail();
            $admin = new self();
            $admin->dateCreation = now()->toDateString();
            $admin->user_id = $user->id;
            $admin->save();
        }
        return $admin;
    }
}
