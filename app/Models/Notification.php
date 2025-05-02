<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'teletravailleur_id',
        'contenu',
    ];

    public function teletravailleur(): BelongsTo
    {
        return $this->belongsTo(Teletravailleur::class, 'teletravailleur_id');
    }

    public function envoyerSMS()
    {
        // Méthode personnalisée (à implémenter selon tes besoins, par exemple, utiliser une API SMS)
        // Exemple fictif :
        $teletravailleur = $this->teletravailleur;
        if ($teletravailleur && $teletravailleur->telephone) {
            // Logique pour envoyer un SMS avec $this->contenu au $teletravailleur->telephone
            // (intégrer une bibliothèque comme Twilio ou Nexmo)
            $this->read_at = now();
            $this->save();
        }
    }
}
