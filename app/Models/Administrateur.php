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

    // Relation indirecte pour accéder aux Télétravailleurs via Utilisateur
    public function teletravailleurs(): HasManyThrough
    {
        return $this->hasManyThrough(
            Teletravailleur::class,
            Utilisateur::class,
            'id', // Clé primaire de Utilisateur
            'user_id', // Clé étrangère dans Teletravailleur
            'user_id', // Clé étrangère dans Administrateur (vers Utilisateur)
            'id' // Clé primaire de Teletravailleur
        );
    }

    // Accéder aux captures d'écran des télétravailleurs
    public function screenshots(): HasManyThrough
    {
        return $this->hasManyThrough(
            Screenshot::class,
            Teletravailleur::class,
            'user_id', // Clé étrangère dans Teletravailleur (vers Utilisateur)
            'teletravailleur_id', // Clé étrangère dans Screenshot
            'user_id', // Clé primaire de Administrateur (vers Utilisateur)
            'id' // Clé primaire de Screenshot
        );
    }

    // Accéder aux heures de travail des télétravailleurs
    public function workingHours(): HasManyThrough
    {
        return $this->hasManyThrough(
            WorkingHour::class,
            Teletravailleur::class,
            'user_id', // Clé étrangère dans Teletravailleur (vers Utilisateur)
            'teletravailleur_id', // Clé étrangère dans WorkingHour
            'user_id', // Clé primaire de Administrateur (vers Utilisateur)
            'id' // Clé primaire de WorkingHour
        );
    }

    // Méthode pour s’assurer qu’il n’y a qu’un seul Administrateur (optionnel, pour un singleton)
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
