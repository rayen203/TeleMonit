@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tableau de bord du Télétravailleur</h2>

    <!-- Bouton "3 points" pour accéder au chatbot -->
    <a href="{{ route('teletravailleur.chat.index') }}" class="btn btn-link" style="font-size: 24px; color: #007bff;">
        <i class="fas fa-ellipsis-v"></i> Accéder au Chatbot
    </a>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Notification pour les seuils de 4h et 8h -->
    @if($notification)
        <div class="alert alert-info" role="alert">
            {{ $notification }}
        </div>
    @endif

    <!-- Notification de capture d'écran -->
    <div id="captureNotification" class="alert alert-success" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        Capture enregistrée avec succès !
    </div>

    <!-- Informations Télétravailleur -->
    <div class="card mb-4">
        <div class="card-header">Informations du Télétravailleur</div>
        <div class="card-body">
            <p><strong>Nom :</strong> {{ $user->nom ?? 'Non défini' }}</p>
            <p><strong>Prénom :</strong> {{ $user->prenom ?? 'Non défini' }}</p>
            <p><strong>Email :</strong> {{ $user->email ?? 'Non défini' }}</p>
            <p><strong>Statut :</strong> {{ $user->isOnline() ? '🟢 Actif' : '🔴 Inactif' }}</p>
            <a href="{{ route('profile.edit') }}" class="btn btn-primary mt-3">Modifier le Profil</a>
            <a href="{{ url('/calendars') }}" class="btn btn-info mt-3">Voir le Calendrier</a>
            <form method="POST" action="{{ route('logout') }}" class="mt-3 d-inline-block">
                @csrf
                <button type="submit" class="btn btn-danger">Déconnexion</button>
            </form>
        </div>
    </div>

    <!-- Section pour le suivi des heures -->
    <div class="card mb-4">
        <div class="card-header">Suivi des Heures</div>
        <div class="card-body">
            <h5>Total Aujourd'hui : {{ $todayFormatted }}</h5>
            <p>Temps de la session en cours : <span id="session-time">0h 0m 0s</span></p>
            <div id="timer" class="mb-3">Temps de la session en cours : 0h 0m 0s</div>
            <button id="startBtn" class="btn btn-success me-2">Démarrer</button>
            <button id="pauseBtn" class="btn btn-warning me-2" disabled>Mettre en Pause</button>
            <button id="resumeBtn" class="btn btn-info me-2" disabled>Reprendre</button>
            <button id="stopBtn" class="btn btn-danger me-2" disabled>Arrêter</button>
        </div>
    </div>

    <!-- Historique des heures -->
    <div class="card">
        <div class="card-header">Historique des Heures</div>
        <div class="card-body">
            @if($workingHours->isEmpty())
                <p>Aucune heure enregistrée.</p>
            @else
                <table class="table table-bordered">
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
                                <td>{{ $hour->formatted_time }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <h5>Total Mensuel : {{ $monthlyFormatted }}</h5>
                {{ $workingHours->links() }}
            @endif
        </div>
    </div>

    <!-- Historique des Captures -->
    <div class="card">
        <div class="card-header">Historique des Captures d'Écran</div>
        <div class="card-body">
            @if($screenshots->isEmpty())
                <p>Aucune capture d’écran enregistrée.</p>
            @else
                <table class="table">
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
                                <td><img src="{{ asset('storage/' . $screenshot->image_path) }}" alt="Capture" width="100"></td>
                                <td><a href="{{ asset('storage/' . $screenshot->image_path) }}" target="_blank" class="btn btn-primary btn-sm">Voir en Plein Écran</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $screenshots->links() }}
            @endif
        </div>
    </div>

    <!-- Section Statistiques Mensuelles -->
    <div class="card mb-4">
        <div class="card-header">Statistiques Mensuelles</div>
        <div class="card-body">
            <!-- Sélecteur de mois -->
            <form method="GET" action="{{ route('teletravailleur.dashboard') }}" class="mb-3">
                <div class="form-group">
                    <label for="month">Sélectionner un mois :</label>
                    <input type="month" id="month" name="month" value="{{ request()->input('month', now()->setTimezone('UTC')->format('Y-m')) }}" class="form-control d-inline-block w-auto">
                    <input type="hidden" name="t" value="{{ now()->timestamp }}"> <!-- Prévenir les problèmes de cache -->
                    <button type="submit" class="btn btn-primary">Afficher</button>
                </div>
            </form>

            @php
            \Log::info('Utilisateur Connecté', ['user_id' => $user->id]);

            // Utiliser user_id pour les requêtes sur Calendar
            $userId = $user->id;
            \Log::info('User ID Utilisé pour les Calendriers', ['user_id' => $userId]);

            // Utiliser teletravailleur_id pour les autres requêtes (WorkingHour, Screenshot)
            $teletravailleurId = $teletravailleur->id;
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
                    $tasks = is_array($calendar->tacheList) ? $calendar->tacheList : [];
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
                                },
                                title: {
                                    display: true,
                                    text: 'Statistiques Mensuelles - <?php echo $selectedDate->format('F Y'); ?>',
                                    font: {
                                        size: 16
                                    }
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

<!-- JavaScript pour gérer le timer et les captures automatiques -->
<script>
    let timerInterval;
    let screenshotInterval = null;
    let totalSeconds = 0;
    let lastScreenshotTime = null;
    let isPaused = false;
    let pausedTime = 0;
    let pauseStartTime = null;

    function updateTimer() {
        totalSeconds++;
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        document.getElementById('timer').textContent = `Temps de la session en cours : ${hours}h ${minutes}m ${seconds}s`;
        document.getElementById('session-time').textContent = `${hours}h ${minutes}m ${seconds}s`;
    }

    function stopScreenshotInterval() {
        if (screenshotInterval) {
            clearInterval(screenshotInterval);
            screenshotInterval = null;
            console.log('Intervalle de capture arrêté.');
        }
    }

    function startScreenshotInterval() {
        stopScreenshotInterval();
        console.log('Démarrage de l’intervalle de capture automatique à :', new Date());

        if (!lastScreenshotTime) {
            lastScreenshotTime = new Date();
            console.log('Initialisation de lastScreenshotTime à :', lastScreenshotTime);
        }

        screenshotInterval = setInterval(() => {
            if (isPaused) {
                console.log('Capture ignorée : le timer est en pause.');
                return;
            }

            const now = new Date();
            const timeSinceLastScreenshot = (now - lastScreenshotTime) - pausedTime;

            if (timeSinceLastScreenshot >= 10 * 60 * 1000) {
                console.log('Capture automatique déclenchée à :', now);
                console.log('Temps actif écoulé depuis la dernière capture :', timeSinceLastScreenshot / 1000, 'secondes');
                captureScreenshot();
            } else {
                console.log('Prochaine capture prévue dans', Math.ceil((10 * 60 * 1000 - timeSinceLastScreenshot) / 1000), 'secondes');
            }
        }, 10 * 1000);
    }

    function showNotification(message) {
        const notification = document.getElementById('captureNotification');
        notification.textContent = message;
        notification.style.display = 'block';

        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }

    async function captureScreenshot() {
        const now = new Date();
        try {
            console.log('Tentative de capture d’écran à :', now);
            console.log('URL de la requête :', '{{ url("/teletravailleur/capture") }}');
            const response = await fetch('{{ url("/teletravailleur/capture") }}', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include'
            });

            console.log('Réponse de l’API :', response);

            if (!response.ok) {
                throw new Error(`Erreur réseau ou serveur : ${response.status} - ${await response.text()}`);
            }

            const data = await response.json();
            console.log('Données renvoyées par l’API :', data);

            if (data.message === 'Capture d’écran enregistrée avec succès.') {
                console.log('Capture d’écran réussie ! Image : ' + data.image_url);
                showNotification('Capture enregistrée avec succès !');
                lastScreenshotTime = new Date();
                console.log('Mise à jour de lastScreenshotTime à :', lastScreenshotTime);
                pausedTime = 0;
                console.log('Réinitialisation de pausedTime à 0 après la capture.');
            } else {
                console.error('Erreur lors de la capture d’écran :', data.message);
                showNotification('Erreur lors de la capture d’écran : ' + (data.message || 'Erreur inconnue'));
            }
        } catch (error) {
            console.error('Erreur lors de l’appel API pour la capture :', error);
            showNotification('Erreur lors de l’appel API pour la capture : ' + error.message);
        }
    }

    document.getElementById('startBtn').addEventListener('click', async () => {
        try {
            console.log('Démarrage de la session à :', new Date());
            const response = await fetch('{{ route("teletravailleur.working-hours.start") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include',
                body: JSON.stringify({ action: 'start' })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(`Erreur réseau ou serveur : ${response.status} - ${data.error || 'Erreur inconnue'}`);
            }

            if (data.error) {
                showNotification(data.error);
            } else {
                totalSeconds = 0;
                pausedTime = 0;
                timerInterval = setInterval(updateTimer, 1000);
                document.getElementById('startBtn').disabled = true;
                document.getElementById('pauseBtn').disabled = false;
                document.getElementById('stopBtn').disabled = false;
                document.getElementById('resumeBtn').disabled = true;
                isPaused = false;
                console.log(data.message);

                startScreenshotInterval();
            }
        } catch (error) {
            console.error('Erreur lors du démarrage:', error);
            showNotification('Une erreur s\'est produite lors du démarrage : ' + error.message);
        }
    });

    document.getElementById('pauseBtn').addEventListener('click', async () => {
        if (timerInterval && !isPaused) {
            try {
                console.log('Mise en pause à :', new Date());
                const response = await fetch('{{ route("teletravailleur.working-hours.pause") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'include'
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(`Erreur réseau ou serveur : ${response.status} - ${data.error || 'Erreur inconnue'}`);
                }

                if (data.error) {
                    showNotification(data.error);
                } else {
                    clearInterval(timerInterval);
                    isPaused = true;
                    pauseStartTime = new Date();
                    document.getElementById('pauseBtn').disabled = true;
                    document.getElementById('resumeBtn').disabled = false;
                    console.log(data.message);
                }
            } catch (error) {
                console.error('Erreur lors de la pause:', error);
                showNotification('Une erreur s\'est produite lors de la pause : ' + error.message);
            }
        }
    });

    document.getElementById('resumeBtn').addEventListener('click', async () => {
        if (isPaused) {
            try {
                console.log('Reprise à :', new Date());
                const response = await fetch('{{ route("teletravailleur.working-hours.resume") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'include'
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(`Erreur réseau ou serveur : ${response.status} - ${data.error || 'Erreur inconnue'}`);
                }

                if (data.error) {
                    showNotification(data.error);
                } else {
                    const pauseEndTime = new Date();
                    const pauseDuration = pauseEndTime - pauseStartTime;
                    pausedTime += pauseDuration;
                    console.log('Temps passé en pause :', pauseDuration / 1000, 'secondes');
                    console.log('Temps total passé en pause (pausedTime) :', pausedTime / 1000, 'secondes');

                    timerInterval = setInterval(updateTimer, 1000);
                    startScreenshotInterval();
                    isPaused = false;
                    document.getElementById('resumeBtn').disabled = true;
                    document.getElementById('pauseBtn').disabled = false;
                    console.log(data.message);
                }
            } catch (error) {
                console.error('Erreur lors de la reprise:', error);
                showNotification('Une erreur s\'est produite lors de la reprise : ' + error.message);
            }
        }
    });

    document.getElementById('stopBtn').addEventListener('click', async () => {
        try {
            console.log('Arrêt de la session à :', new Date());
            const response = await fetch('{{ route("teletravailleur.working-hours.stop") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include'
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(`Erreur réseau ou serveur : ${response.status} - ${data.error || 'Erreur inconnue'}`);
            }

            if (data.error) {
                showNotification(data.error);
            } else {
                if (totalSeconds >= 600) {
                    console.log('Déclenchement d’une capture d’écran à l’arrêt de la session.');
                    try {
                        await captureScreenshot();
                    } catch (captureError) {
                        console.error('Erreur lors de la capture à l’arrêt :', captureError);
                    }
                } else {
                    console.log('Pas de capture à l’arrêt : session trop courte.', totalSeconds);
                }

                clearInterval(timerInterval);
                stopScreenshotInterval();
                totalSeconds = 0;
                lastScreenshotTime = null;
                pausedTime = 0;
                isPaused = false;
                document.getElementById('timer').textContent = 'Temps de la session en cours : 0h 0m 0s';
                document.getElementById('session-time').textContent = '0h 0m 0s';
                document.getElementById('startBtn').disabled = false;
                document.getElementById('pauseBtn').disabled = true;
                document.getElementById('resumeBtn').disabled = true;
                document.getElementById('stopBtn').disabled = true;
                console.log(data.message);
                location.reload();
            }
        } catch (error) {
            console.error('Erreur lors de l\'arrêt:', error);
            showNotification('Une erreur s\'est produite lors de l\'arrêt : ' + error.message);
        }
    });

    window.onbeforeunload = function() {
        if (timerInterval || isPaused) {
            fetch('{{ route("teletravailleur.working-hours.stop") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'include',
                keepalive: true
            });
        }
    };

    function keepPageActive() {
        setInterval(() => {
            window.dispatchEvent(new Event('mousemove'));
        }, 30 * 1000);
    }
    keepPageActive();
</script>

<!-- Inclusion de Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@endsection
