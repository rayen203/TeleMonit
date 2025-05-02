@extends('layouts.app')

@section('content')
<div class="container-fluid" style="background: #1a2a44; min-height: 100vh; color: white; padding: 20px;">
    <!-- En-tête avec logo et bouton de retour -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <img src="{{ asset('images/telemonit-logo.png') }}" alt="TELEMONIT Logo" style="height: 40px;">
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary rounded-pill">Retour au Tableau de Bord</a>
        </div>
    </div>

    <!-- Informations du Télétravailleur -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-dark text-white shadow-lg border-0 rounded-4 p-3">
                <div class="d-flex align-items-center">
                    <img src="{{ $teletravailleur->teletravailleur->photoProfil ? asset('storage/' . $teletravailleur->teletravailleur->photoProfil) : asset('images/default-profile.png') }}" alt="Profile Photo" class="rounded-circle me-3" style="width: 60px; height: 60px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
                    <div>
                        <h5 class="mb-0">{{ $teletravailleur->nom ?? 'N/A' }} {{ $teletravailleur->prenom ?? '' }}</h5>
                        <p class="mb-0 text-muted">{{ $teletravailleur->email ?? 'email@example.com' }}</p>
                        <p class="mb-0 text-muted">Statut: <span class="badge {{ $teletravailleur->isOnline() ? 'bg-success' : 'bg-danger' }}">{{ $teletravailleur->isOnline() ? 'Connecté' : 'Déconnecté' }}</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suivi des Heures -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-dark text-white shadow-lg border-0 rounded-4 p-3">
                <h4>Suivi des Heures</h4>
                <p><strong>Total Aujourd'hui :</strong> <span id="todayHours">{{ round($todayHours, 2) }}</span></p>
                <p><strong>Total Mensuel :</strong> <span id="monthlyHours">{{ round($monthlyHours, 2) }}</span></p>
            </div>
        </div>
    </div>

    <!-- Historique des Captures d'Écran -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-dark text-white shadow-lg border-0 rounded-4 p-3">
                <h4>Historique des Captures d'Écran</h4>
                @if($screenshots->isEmpty())
                    <p class="text-muted">Aucune capture d’écran enregistrée.</p>
                @else
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($screenshots as $screenshot)
                                <tr>
                                    <td>{{ $screenshot->created_at->format('d/m/Y H:i:s') }}</td>
                                    <td><img src="{{ asset('storage/' . $screenshot->image_path) }}" alt="Capture" style="max-width: 100px;"></td>
                                    <td><a href="{{ asset('storage/' . $screenshot->image_path) }}" target="_blank" class="btn btn-primary btn-sm rounded-pill">Voir en Plein Écran</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $screenshots->links() }} <!-- Pagination -->
                @endif
            </div>
        </div>
    </div>

    <!-- Historique des Heures -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-dark text-white shadow-lg border-0 rounded-4 p-3">
                <h4>Historique des Heures</h4>
                @if($workingHours->isEmpty())
                    <p class="text-muted">Aucune heure enregistrée.</p>
                @else
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Temps Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workingHours as $hour)
                                <tr>
                                    <td>{{ $hour->date ? $hour->date->format('d/m/Y') : 'Non définie' }}</td>
                                    <td>{{ $hour->start_time ? $hour->start_time->format('H:i:s') : 'Non défini' }}</td>
                                    <td>{{ $hour->stop_time ? $hour->stop_time->format('H:i:s') : 'Non défini' }}</td>
                                    <td>
                                        @php
                                            $effectiveSeconds = $hour->total_seconds - ($hour->pause_total_seconds ?? 0);
                                            if ($effectiveSeconds < 0) {
                                                $effectiveSeconds = $hour->total_seconds; // Utiliser total_seconds brut si négatif
                                            }
                                            $hours = floor($effectiveSeconds / 3600);
                                            $remainingSeconds = $effectiveSeconds % 3600;
                                            $minutes = floor($remainingSeconds / 60);
                                            $seconds = $remainingSeconds % 60;
                                            echo "{$hours}h {$minutes}m {$seconds}s";
                                        @endphp
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $workingHours->links() }} <!-- Pagination -->
                @endif
            </div>
        </div>
    </div>

    <!-- Section Statistiques Mensuelles -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-dark text-white shadow-lg border-0 rounded-4 p-3">
                <h4>Statistiques Mensuelles</h4>
                <!-- Sélecteur de mois -->
                <form method="GET" action="{{ route('admin.teletravailleur.details', $teletravailleur->id) }}" class="mb-3">
                    <div class="form-group">
                        <label for="month">Sélectionner un mois :</label>
                        <input type="month" id="month" name="month" value="{{ request()->input('month', now()->setTimezone('UTC')->format('Y-m')) }}" class="form-control d-inline-block w-auto text-dark">
                        <input type="hidden" name="t" value="{{ now()->timestamp }}"> <!-- Prévenir les problèmes de cache -->
                        <button type="submit" class="btn btn-primary">Afficher</button>
                    </div>
                </form>

                @php
                \Log::info('Utilisateur Connecté', ['user_id' => $teletravailleur->id]);

                // Utiliser user_id pour les requêtes sur Calendar
                $userId = $teletravailleur->id;
                \Log::info('User ID Utilisé pour les Calendriers', ['user_id' => $userId]);

                // Utiliser teletravailleur_id pour les autres requêtes (WorkingHour, Screenshot)
                $teletravailleurId = $teletravailleur->teletravailleur->id;
                \Log::info('Teletravailleur ID Utilisé pour WorkingHour et Screenshot', ['teletravailleur_id' => $teletravailleurId]);

                // Déterminer le mois à afficher (par défaut : mois actuel)
                $selectedMonth = request()->input('month', now()->setTimezone('UTC')->format('Y-m'));
                \Log::info('Mois sélectionné brut', ['selectedMonth' => $selectedMonth]);

                // Créer la date et forcer une plage correcte
                $selectedDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth, 'UTC')->startOfMonth();
                $startOfMonth = $selectedDate->copy()->startOfDay();
                $endOfMonth = $selectedDate->copy()->endOfMonth()->endOfDay();

                \Log::info('Plage de dates pour le mois', [
                    'selectedMonth' => $selectedMonth,
                    'startOfMonth' => $startOfMonth->toDateTimeString(),
                    'endOfMonth' => $endOfMonth->toDateTimeString()
                ]);

                // Charger toutes les sessions du mois sélectionné
                $monthlySessions = \App\Models\WorkingHour::where('teletravailleur_id', $teletravailleurId)
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->whereNotNull('stop_time')
                    ->get();

                $monthlySeconds = 0;
                foreach ($monthlySessions as $session) {
                    $effectiveSeconds = $session->total_seconds - ($session->pause_total_seconds ?? 0);
                    $monthlySeconds += max(0, $effectiveSeconds);
                }
                $totalHours = round($monthlySeconds / 3600, 2);

                // Captures d'écran pour le mois sélectionné
                $allScreenshots = \App\Models\Screenshot::where('teletravailleur_id', $teletravailleurId)
                    ->orderBy('created_at', 'desc')
                    ->get();
                \Log::info('Toutes les Captures d’Écran (Vue)', ['screenshots' => $allScreenshots->toArray()]);

                $monthlyScreenshots = $allScreenshots->filter(function($screenshot) use ($startOfMonth, $endOfMonth) {
                    $isBetween = $screenshot->created_at->between($startOfMonth, $endOfMonth);
                    \Log::info('Screenshot Date', [
                        'id' => $screenshot->id,
                        'created_at' => $screenshot->created_at->toDateTimeString(),
                        'isBetween' => $isBetween
                    ]);
                    return $isBetween;
                })->count();

                // Tâches pour le mois sélectionné
                $totalTasks = 0;
                $calendars = \App\Models\Calendar::where('user_id', $userId)->get();
                \Log::info('Tous les Calendriers (Vue)', ['calendars' => $calendars->toArray()]);

                foreach ($calendars as $calendar) {
                    if (!empty($calendar->tacheList)) {
                        $tasks = is_array($calendar->tacheList) ? $calendar->tacheList : []; // Correction pour éviter l'erreur json_decode
                        \Log::info('Structure de tacheList', [
                            'calendar_id' => $calendar->id,
                            'tacheList' => $calendar->tacheList
                        ]);

                        if (is_array($tasks)) {
                            foreach ($tasks as $task) {
                                $taskDateKey = isset($task['deadline']) ? 'deadline' : null;
                                $taskDate = null;
                                if ($taskDateKey) {
                                    try {
                                        $taskDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $task[$taskDateKey], 'UTC');
                                        if ($taskDate === false) {
                                            $taskDate = \Carbon\Carbon::parse($task[$taskDateKey])->setTimezone('UTC');
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error('Erreur lors du parsing de la date de fin de la tâche', [
                                            'calendar_id' => $calendar->id,
                                            'task' => $task,
                                            'error' => $e->getMessage()
                                        ]);
                                    }
                                }

                                $isTaskDateBetween = $taskDate && $taskDate->between($startOfMonth, $endOfMonth);
                                if ($isTaskDateBetween) {
                                    $totalTasks += 1;
                                    \Log::info('Tâche ajoutée via tacheList deadline', [
                                        'calendar_id' => $calendar->id,
                                        'task_title' => $task['title'] ?? 'N/A',
                                        'date_key' => $taskDateKey,
                                        'date_value' => $task[$taskDateKey] ?? 'N/A',
                                        'parsed_date' => $taskDate ? $taskDate->toDateTimeString() : 'N/A',
                                        'isBetween' => $isTaskDateBetween,
                                        'totalTasksCumulative' => $totalTasks
                                    ]);
                                } else {
                                    \Log::info('Tâche non ajoutée', [
                                        'calendar_id' => $calendar->id,
                                        'task_title' => $task['title'] ?? 'N/A',
                                        'date_key' => $taskDateKey,
                                        'date_value' => $task[$taskDateKey] ?? 'N/A',
                                        'parsed_date' => $taskDate ? $taskDate->toDateTimeString() : 'N/A',
                                        'isBetween' => $isTaskDateBetween,
                                        'totalTasksCumulative' => $totalTasks
                                    ]);
                                }
                            }
                        }
                        \Log::info('Calendar Task', [
                            'calendar_id' => $calendar->id,
                            'date' => $calendar->date,
                            'tacheList' => $calendar->tacheList,
                            'totalTasksCumulative' => $totalTasks
                        ]);
                    }
                }

                \Log::info('Valeurs pour le Graphique', [
                    'totalHours' => $totalHours,
                    'monthlyScreenshots' => $monthlyScreenshots,
                    'totalTasks' => $totalTasks
                ]);
                @endphp

                <div id="chartContainer">
                    <canvas id="monthlyStatsChart" style="max-width: 400px; max-height: 400px; margin: 0 auto;"></canvas>
                </div>
                <div id="noDataMessage" style="display: none; text-align: center; margin-top: 10px;">
                    <p>Aucune donnée disponible pour ce mois.</p>
                </div>

                <script>
                    function loadChart() {
                        if (typeof Chart === 'undefined') {
                            const chartScript = document.createElement('script');
                            chartScript.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
                            chartScript.onload = function() {
                                const datalabelsScript = document.createElement('script');
                                datalabelsScript.src = 'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js';
                                datalabelsScript.onload = function() {
                                    createOrUpdateChart();
                                };
                                datalabelsScript.onerror = function() {
                                    console.error('Erreur lors du chargement de chartjs-plugin-datalabels');
                                    document.getElementById('chartContainer').innerHTML = '<p style="text-align: center;">Erreur lors du chargement des étiquettes du graphique.</p>';
                                };
                                document.head.appendChild(datalabelsScript);
                            };
                            chartScript.onerror = function() {
                                console.error('Erreur lors du chargement de Chart.js');
                                document.getElementById('chartContainer').innerHTML = '<p style="text-align: center;">Erreur lors du chargement du graphique.</p>';
                            };
                            document.head.appendChild(chartScript);
                        } else {
                            createOrUpdateChart();
                        }
                    }

                    let monthlyStatsChartInstance = null;

                    function createOrUpdateChart() {
                        const ctx = document.getElementById('monthlyStatsChart').getContext('2d');

                        if (monthlyStatsChartInstance) {
                            monthlyStatsChartInstance.destroy();
                        }

                        let hours = <?php echo $totalHours ?: 0; ?>;
                        let screenshots = <?php echo $monthlyScreenshots ?: 0; ?>;
                        let tasks = <?php echo $totalTasks ?: 0; ?>;

                        if (hours === 0 && screenshots === 0 && tasks === 0) {
                            document.getElementById('chartContainer').style.display = 'none';
                            document.getElementById('noDataMessage').style.display = 'block';
                            return;
                        } else {
                            document.getElementById('chartContainer').style.display = 'block';
                            document.getElementById('noDataMessage').style.display = 'none';
                        }

                        if (hours === 0) hours = 0.001;
                        if (screenshots === 0) screenshots = 0.001;
                        if (tasks === 0) tasks = 0.001;

                        const total = hours + screenshots + tasks;

                        Chart.register(window.ChartDataLabels);

                        monthlyStatsChartInstance = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: ['Heures Travaillées (h)', 'Captures d\'Écran', 'Tâches'],
                                datasets: [{
                                    label: 'Statistiques Mensuelles',
                                    data: [hours, screenshots, tasks],
                                    backgroundColor: [
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(75, 192, 192, 0.7)',
                                    ],
                                    borderColor: [
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(75, 192, 192, 1)',
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            color: '#fff'
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Statistiques Mensuelles - <?php echo $selectedDate->format('F Y'); ?>',
                                        font: {
                                            size: 16
                                        },
                                        color: '#fff'
                                    },
                                    tooltip: {
                                        enabled: true,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                let value = context.parsed;
                                                if (value === 0.001) value = 0;
                                                label += value + (context.label === 'Heures Travaillées (h)' ? ' heures' : '');
                                                return label;
                                            }
                                        }
                                    },
                                    datalabels: {
                                        color: '#fff',
                                        font: {
                                            weight: 'bold',
                                            size: 14
                                        },
                                        formatter: (value, context) => {
                                            let realValue = value === 0.001 ? 0 : value;
                                            if (realValue === 0) return '';
                                            const percentage = ((realValue / total) * 100).toFixed(1);
                                            return percentage + '%';
                                        },
                                        anchor: 'center',
                                        align: 'center'
                                    }
                                }
                            }
                        });
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        loadChart();
                    });
                </script>
            </div>
        </div>
    </div>
</div>

<!-- CSS personnalisé pour le style -->
<style>
    .table-dark th, .table-dark td {
        border: none;
    }
    .table-dark tr:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    .badge.bg-success {
        background-color: #28a745 !important;
    }
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    .card {
        background: #2c3e50;
    }
    .text-warning {
        font-size: 12px;
        display: block;
    }
    .alert {
        margin-bottom: 20px;
    }
</style>

<!-- Script pour formater les totaux -->
<script>
    function formatTime(hours) {
        const totalSeconds = Math.round(hours * 3600); // Convertir les heures en secondes
        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;
        return `${h}h ${m}m ${s}s`;
    }

    // Formater le total aujourd'hui
    const todayHoursElement = document.getElementById('todayHours');
    const todayHours = parseFloat(todayHoursElement.textContent);
    todayHoursElement.textContent = formatTime(todayHours);

    // Formater le total mensuel
    const monthlyHoursElement = document.getElementById('monthlyHours');
    const monthlyHours = parseFloat(monthlyHoursElement.textContent);
    monthlyHoursElement.textContent = formatTime(monthlyHours);
</script>

<!-- Inclusion de Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection
