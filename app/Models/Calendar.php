<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calendar extends Model
{
    use HasFactory;

    protected $table = 'calendars';

    protected $fillable = [
        'user_id',
        'date',
        'tacheList',
    ];

    protected $casts = [
        'tacheList' => 'json',
        'date' => 'date',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'user_id');
    }

    public function ajouterTache($title, $description, $startDate, $deadline, $status = 'pending')
    {
        $tacheList = $this->tacheList ?? [];


        $newId = count($tacheList) + 1;

        $tacheList[] = [
            'id' => $newId,
            'title' => $title,
            'description' => $description,
            'start_date' => $startDate,
            'deadline' => $deadline,
            'status' => $status,
        ];

        $this->tacheList = $tacheList;
        $this->save();
    }


    public function modifierTache($tacheId, $title, $description, $startDate, $deadline, $status)
    {
        $tacheList = $this->tacheList ?? [];

        foreach ($tacheList as &$tache) {
            if ($tache['id'] == $tacheId) {
                $tache['title'] = $title;
                $tache['description'] = $description;
                $tache['start_date'] = $startDate;
                $tache['deadline'] = $deadline;
                $tache['status'] = $status;
                break;
            }
        }

        $this->tacheList = $tacheList;
        $this->save();
    }


    public function supprimerTache($tacheId)
    {
        $tacheList = $this->tacheList ?? [];

        $tacheList = array_filter($tacheList, function ($tache) use ($tacheId) {
            return $tache['id'] != $tacheId;
        });

        // Réindexer les tâches
        $tacheList = array_values($tacheList);
        foreach ($tacheList as $index => &$tache) {
            $tache['id'] = $index + 1;
        }

        $this->tacheList = $tacheList;
        $this->save();
    }


    public function getWorkedHoursForTache($tache)
    {

        $workingHours = $this->utilisateur->teletravailleur
            ? $this->utilisateur->teletravailleur->workingHours()
                ->whereBetween('start_time', [$tache['start_date'], $tache['deadline']])
                ->get()
            : collect();

        $totalSeconds = 0;
        foreach ($workingHours as $session) {
            $totalSeconds += $session->total_seconds;
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;
        return "{$hours}h {$minutes}m {$seconds}s";
    }


    public function isTacheOverdue($tache)
    {
        return now()->greaterThan($tache['deadline']) && $tache['status'] !== 'completed';
    }
}
