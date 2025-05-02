<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingHour extends Model
{
    use HasFactory;

    protected $table = 'working_hours';

    protected $fillable = [
        'teletravailleur_id',
        'date',
        'start_time',
        'pause_time',
        'resume_time',
        'stop_time',
        'total_seconds',
        'pause_total_seconds',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'start_time' => 'datetime',
        'pause_time' => 'datetime',
        'resume_time' => 'datetime',
        'stop_time' => 'datetime',
    ];

    public function teletravailleur(): BelongsTo
    {
        return $this->belongsTo(Teletravailleur::class, 'teletravailleur_id');
    }

    public function calculateTotalSeconds()
    {
        if (!$this->start_time || !$this->stop_time) {
            return $this->total_seconds ?? 0;
        }

        $totalTime = $this->stop_time->diffInSeconds($this->start_time);
        return max(0, $totalTime - ($this->pause_total_seconds ?? 0));
    }

    public function getTotalHoursAttribute()
    {
        $effectiveSeconds = $this->total_seconds - ($this->pause_total_seconds ?? 0);
        return $effectiveSeconds > 0 ? round($effectiveSeconds / 3600, 2) : 0.0;
    }

    public function calculerTotalMensuel()
    {
        $totalSeconds = self::where('teletravailleur_id', $this->teletravailleur_id)
                            ->whereMonth('date', now()->month)
                            ->whereYear('date', now()->year)
                            ->sum('total_seconds') ?? 0;

        return $totalSeconds / 3600;
    }

    public function getFormattedTimeAttribute()
    {
        $effectiveSeconds = $this->total_seconds - ($this->pause_total_seconds ?? 0);

        // Si le temps effectif est négatif, loguer le problème et utiliser total_seconds
        if ($effectiveSeconds < 0) {
            \Log::warning('Temps effectif négatif détecté', [
                'working_hour_id' => $this->id,
                'total_seconds' => $this->total_seconds,
                'pause_total_seconds' => $this->pause_total_seconds,
                'effectiveSeconds' => $effectiveSeconds,
            ]);
            $effectiveSeconds = $this->total_seconds; // Utiliser total_seconds brut
        }

        if ($effectiveSeconds >= 3600) {
            $hours = floor($effectiveSeconds / 3600);
            $remainingSeconds = $effectiveSeconds % 3600;
            $minutes = floor($remainingSeconds / 60);
            $seconds = $remainingSeconds % 60;
            $formatted = "$hours heure" . ($hours > 1 ? "s" : "");
            if ($minutes > 0) {
                $formatted .= " et $minutes minute" . ($minutes > 1 ? "s" : "");
            }
            if ($seconds > 0) {
                $formatted .= " et $seconds seconde" . ($seconds > 1 ? "s" : "");
            }
            return $formatted;
        } elseif ($effectiveSeconds >= 60) {
            $minutes = floor($effectiveSeconds / 60);
            $seconds = $effectiveSeconds % 60;
            $formatted = "$minutes minute" . ($minutes > 1 ? "s" : "");
            if ($seconds > 0) {
                $formatted .= " et $seconds seconde" . ($seconds > 1 ? "s" : "");
            }
            return $formatted;
        } else {
            return "$effectiveSeconds seconde" . ($effectiveSeconds > 1 ? "s" : "");
        }
    }
}
