<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Screenshot extends Model
{
    use HasFactory;

    protected $table = 'screenshots';

    protected $fillable = [
        'teletravailleur_id',
        'image_path',
        'created_at',
    ];

    public function teletravailleur(): BelongsTo
    {
        return $this->belongsTo(Teletravailleur::class, 'teletravailleur_id');
    }

    public function capturer()
    {
        // Méthode personnalisée (à implémenter selon tes besoins, par exemple, sauvegarder une image)

    }
}
