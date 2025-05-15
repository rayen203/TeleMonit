@extends('layouts.base-interface')

@section('content')
<!-- Conteneur principal pour gérer le positionnement -->
<div style="position: relative; width: 100%; min-height: 100vh; overflow: visible;">
    <!-- Section admin ancrée à gauche -->
    <div style="position: absolute; left: -210px; top: 60px; z-index: 10;">
        <div class="p-3" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; width: 574px; height: 220px; position: relative; border: 0.5px solid rgb(113, 113, 113); backdrop-filter: blur(10px);">
            <div class="d-flex align-items-center mb-2">
                <div style="position: relative;">
                    <img src="{{ Auth::user()->photoProfil ? asset('storage/' . Auth::user()->photoProfil) : asset('images/avatar1.png') }}" alt="Admin Photo" class="rounded-circle me-3" style="width: 127px; height: 122px; margin-left: 190px;" onerror="this.src='{{ asset('images/avatar1.png') }}';">
                    <a href="{{ route('profile.edit') }}" style="position: absolute; margin-top: -120px; margin-left: 290px;">
                        <img src="{{ asset('images/edit.png') }}" alt="Edit Icon" style="width: 29px; height: 29px;">
                    </a>
                </div>
                <h5 class="mb-0" style="color: #E1E4E6; margin-left: 350px; margin-top: -70px; font-weight: bold; font-size: 23px;">{{ Auth::user()->nom ?? 'Admin' }} {{ Auth::user()->prenom ?? '' }}</h5>
                <div>
                    <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 195px; margin-top: 60px; text-decoration: underline; font-weight: bold;">{{ Auth::user()->email ?? 'mail@gmail.com' }}</p>
                    <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 420px; margin-top: -17px; font-weight: bold;"> +216 ******** </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Boutons -->
    <div id="button-container" style="position: absolute; left: 10px; top: 300px; z-index: 10; display: flex; flex-direction: column; gap: 10px;" class="button-container">
        <a href="{{ route('admin.teletravailleurs.index') }}" class="btn btn-primary rounded-pill px-4 py-2" style="display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
            <img src="{{ asset('images/profile.png') }}" alt="Profile Icon" style="width: 51px; height: 36px; margin-right: 40px; margin-left: 60px;">
            Profiles
        </a>
        <div class="button-wrapper">
            <a href="#" class="btn btn-primary rounded-pill px-4 py-2 default-button" style="display: flex; align-items: center; justify-content: center; width: 525px; height: 75px; margin-left: -480px;">
                <img src="{{ asset('images/Component.png') }}" alt="Component Icon" style="width: 38px; height: 22px; margin-right: 30px; margin-left: 460px;">
            </a>
            <a href="{{ route('calendars.index') }}" class="btn btn-primary rounded-pill px-4 py-2 hidden-button" style="display: none; align-items: center; justify-content: center; width: 525px; height: 75px; backdrop-filter: blur(10px);">
                <img src="{{ asset('images/calendar.png') }}" alt="Calendar Icon" style="width: 38px; height: 38px; margin-right: 40px; margin-left: 90px;">
                Calendar
            </a>
        </div>
    </div>

    <div style="margin-left: 435px; margin-top: 20px; margin-bottom: -56px;">
        <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; text-decoration: none;">
            <img src="{{ asset('images/fleche.png') }}" alt="Back Arrow" style="width: 50px; height: 30px; margin-right: 10px;">
        </a>
    </div>


    <!-- Conteneur pour les détails du télétravailleur -->
    <div style="margin-left: 360px; min-height: calc(100vh - 40px); padding: 20px 0 20px 20px; max-width: none; width: calc(100% - 300px);">
        <div class="container-fluid" style="color: white; padding-right: 0; margin-right: 0;">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }} - Please contact the administrator .
                </div>
            @endif

            <!-- Une seule carte pour toutes les sections avec défilement -->
            <div class="card" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; padding: 15px; border: 1px solid #444; width: 1060px; height: 579px; overflow-y: auto; position: relative; padding-right: 10px; backdrop-filter: blur(10px); scrollbar-width: none; -ms-overflow-style: none; margin-top: 45px;">
                <!-- Section avec photo, nom, prénom, email, statut et numéro de téléphone -->
                <div class="section-content">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center position-relative">
                            <img src="{{ $teletravailleur->teletravailleur->photoProfil ? asset('storage/' . $teletravailleur->teletravailleur->photoProfil) : asset('images/default-profile.png') }}" alt="Teleworker Photo" class="rounded-circle me-3" style="width: 90px; height: 90px; margin-top: 20px;border-radius: 77px; margin-left: 20px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
                            <div>
                                <h5 class="mb-1" style="color: #E1E4E6; font-weight: bold; font-size: 23px; margin-left: 150px; margin-top: -95px;">{{ $teletravailleur->nom ?? 'N/A' }} {{ $teletravailleur->prenom ?? '' }}</h5>
                                <p class="mb-1" style="color: #FFFFFF; font-size: 15px; font-weight: bold; margin-left: 150px; margin-top: 15px; text-decoration: underline; ">{{ $teletravailleur->email ?? 'email@example.com' }}</p>
                                <p class="mb-1" style="color: #FFFFFF; font-size: 15px; font-weight: bold; margin-left: 390px; margin-top: -25px;">+216 {{ $teletravailleur->teletravailleur->telephone ?? 'N/A' }}</p>
                                <p class="mb-0 d-flex align-items-center" style="font-family: 'Bold', sans-serif; margin-left: 174px; margin-top: 5px;">
                                    <span class="bg-{{ $teletravailleur->isOnline() ? 'green' : 'red' }}-500 rounded-full w-3 h-3 mr-2" style="display: inline-block; vertical-align: middle;"></span>
                                    <span class="text-m font-bold {{ $teletravailleur->isOnline() ? 'text-green-500' : 'text-red-500' }}" style="display: inline-block; vertical-align: middle;">{{ $teletravailleur->isOnline() ? 'Connected' : 'Disconnected' }}</span>
                                </p>
                            </div>

                        </div>

                    </div>
                </div>

                <!-- Ligne de séparation -->
                <hr class="section-divider">

                <!-- Section Suivi des Heures -->
                <div class="section-content">
                    <h4 style="font-weight: bold; font-size:25px; margin-left: 20px;  ">Hours Tracked </h4>

                    @php
    $teletravailleurId = isset($teletravailleur->teletravailleur) ? $teletravailleur->teletravailleur->id : null;
    if ($teletravailleurId) {
        // Calculer le total du jour
        $todaySeconds = 0;
        $todaySessions = \App\Models\WorkingHour::where('teletravailleur_id', $teletravailleurId)
            ->where('date', now()->toDateString())
            ->whereNotNull('stop_time')
            ->get();
        foreach ($todaySessions as $session) {
            $effectiveSeconds = $session->total_seconds - ($session->pause_total_seconds ?? 0);
            $todaySeconds += max(0, $effectiveSeconds);
        }
        $hours = floor($todaySeconds / 3600);
        $remainingSeconds = $todaySeconds % 3600;
        $minutes = floor($remainingSeconds / 60);
        $seconds = $remainingSeconds % 60;
        $todayFormatted = "{$hours}h {$minutes}m {$seconds}s";

        // Calculer le total mensuel
        $selectedMonth = request()->input('month', now()->format('Y-m'));
        $startOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        $endOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();
        $monthlySessions = \App\Models\WorkingHour::where('teletravailleur_id', $teletravailleurId)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('stop_time')
            ->get();
        $monthlySeconds = 0;
        foreach ($monthlySessions as $session) {
            $effectiveSeconds = $session->total_seconds - ($session->pause_total_seconds ?? 0);
            $monthlySeconds += max(0, $effectiveSeconds);
        }
        $hours = floor($monthlySeconds / 3600);
        $remainingSeconds = $monthlySeconds % 3600;
        $minutes = floor($remainingSeconds / 60);
        $seconds = $remainingSeconds % 60;
        $monthlyFormatted = "{$hours}h {$minutes}m {$seconds}s";
    } else {
        $todayFormatted = "0h 0m 0s";
        $monthlyFormatted = "0h 0m 0s";
    }
@endphp

<p style="margin-top: 17px; margin-left:20px;">Time Tracked Today : <span id="todayHours" style="margin-left: 60px">{{ $todayFormatted }}</span></p>
<p style="margin-top: 5px; margin-left:20px;">Monthly Total : <span id="monthlyHours" style="margin-left: 114px">{{ $monthlyFormatted }}</span></p>


                </div>

                <!-- Ligne de séparation -->
                <hr class="section-divider">

                <!-- Section Historique des Captures d'Écran -->
                <div class="section-content">
                    <h4 style="font-weight: bold; font-size: 25px; margin-left: 20px;">Screenshots History</h4>
                    <br>
                    <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 20px; padding: 10px 20px;">
                        <p style="color: #A19B9B; font-weight: bold; text-align: left;font-size: 18px;">Date</p>
                        <p style="color: #A19B9B; font-weight: bold; text-align: center;font-size: 18px;">Screenshots</p>
                        <p style="color: #A19B9B; font-weight: bold; text-align: center;font-size: 18px;">Actions</p>
                    </div>

                    <!-- Ligne de séparation -->
                    <hr class="section-divider">

                    @if($screenshots->isEmpty())
                        <p class="text-muted" style="margin-left: 20px;">No screenshots recorded.</p>
                    @else
                        @foreach($screenshots as $screenshot)
                            <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 20px; padding: 10px 20px; align-items: center;border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
                                <p style="color: #FFFFFF; font-size: 16px; text-align: left;">{{ $screenshot->created_at->format('d/m/Y H:i') }}</p>

                                <div style="text-align: center;display: flex; justify-content: center;">
                                    <img src="{{ asset('storage/' . $screenshot->image_path) }}" alt="Screenshot" style="max-width: 100px; max-height: 100px; border-radius: 10px;" onerror="this.src='{{ asset('images/default-screenshot.png') }}';">
                                </div>
                                <div style="display: flex; justify-content: center; gap: 10px;align-items: center; ">
                                    <a href="{{ asset('storage/' . $screenshot->image_path) }}" target="_blank" class="btn btn-secondary btn-sm rounded-pill" style="font-size: 16px; padding: 5px 10px;">View In Full Screen</a>
                                </div>
                            </div>
                        @endforeach
                        <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 20px; padding: 10px 20px; margin-top: 20px;">
                            <div></div> <!-- Espace vide sous "Date" -->
                            <div></div> <!-- Espace vide sous "Screenshots" -->
                            <div style="text-align: center; " >
                                {{ $screenshots->links() }} <!-- Pagination -->
                            </div>
                        </div>

                    @endif
                </div>

                <!-- Ligne de séparation -->
                <hr class="section-divider">

                <!-- Section Historique des Heures -->
<div class="section-content">
    <h4 style="font-weight: bold; font-size: 25px; margin-left: 20px;">Hours History</h4>
    <br>
    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 2fr; gap: 20px; padding: 10px 20px;">
        <p style="color: #A19B9B; font-weight: bold; text-align: left; font-size: 18px;">Date</p>
        <p style="color: #A19B9B; font-weight: bold; text-align: center;font-size: 18px;">Start</p>
        <p style="color: #A19B9B; font-weight: bold; text-align: center;font-size: 18px;">End</p>
        <p style="color: #A19B9B; font-weight: bold; text-align: center;font-size: 18px;">Total Hours</p>
    </div>

    <!-- Ligne de séparation -->
    <hr class="section-divider">

    @if($workingHours->isEmpty())
        <p class="text-muted" style="margin-left: 20px;">No hours recorded.</p>
    @else
        @foreach($workingHours as $hour)
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 2fr; gap: 20px; padding: 10px 20px; align-items: center;  border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
                <p style="color: #FFFFFF; font-size: 16px; text-align: left;">{{ $hour->date ? $hour->date->format('d/m/Y') : 'Not set' }}</p>
                <p style="color: #FFFFFF; font-size: 16px; text-align: center;">{{ $hour->start_time ? $hour->start_time->format('H:i:s') : 'Not set' }}</p>
                <p style="color: #FFFFFF; font-size: 16px; text-align: center;">{{ $hour->stop_time ? $hour->stop_time->format('H:i:s') : 'Not set' }}</p>
                <p style="color: #FFFFFF; font-size: 16px; text-align: center;">
                    @php
                        $effectiveSeconds = $hour->total_seconds - ($hour->pause_total_seconds ?? 0);
                        if ($effectiveSeconds < 0) {
                            $effectiveSeconds = $hour->total_seconds;
                        }
                        $hours = floor($effectiveSeconds / 3600);
                        $remainingSeconds = $effectiveSeconds % 3600;
                        $minutes = floor($remainingSeconds / 60);
                        $seconds = $remainingSeconds % 60;
                        echo "{$hours}h {$minutes}m {$seconds}s";
                    @endphp
                </p>
            </div>
        @endforeach
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 2fr; gap: 20px; padding: 10px 20px; margin-top: 20px;">
            <div></div>
            <div></div>
            <div></div>
            <div style="text-align: center;">
                {{ $workingHours->links() }} <!-- Pagination -->
            </div>
        </div>
    @endif
</div>
                <!-- Ligne de séparation -->
                <hr class="section-divider">

                <!-- Section Statistiques Mensuelles -->
                <div class="section-content">
                    <h4 style="font-weight: bold; font-size: 25px; margin-left: 20px;">Monthly Statistics</h4>
                    <br>
                    <form method="GET" action="{{ route('admin.teletravailleur.details', $teletravailleur->id) }}" class="mb-3">
                        <div class="form-group">
                            <label style="font-weight: bold; font-size: 17px; margin-left: 20px;" for="month">Select a month :</label> &nbsp; &nbsp;
                            <input type="month" id="month" name="month" value="{{ request()->input('month', now()->setTimezone('UTC')->format('Y-m')) }}" class="form-control d-inline-block w-auto text-dark" style="background-color: #f0f0f0; border-radius: 7px; padding: 8px 12px; font-size: 16px; color: #000A44; border: 1px solid #ccc; width: 150px; height: 38px;">
                            <input type="hidden" name="t" value="{{ now()->timestamp }}"> &nbsp;
                            <button type="submit" class="btn btn-primary" style="background-color: #0A22B9; color: #FFFFFF; border-radius: 7px; padding: 10px 20px; font-size: 15px; border: none; height: 40px; width: 100px;  ">Show</button>
                        </div>
                    </form>

                    @php
                    $userId = $teletravailleur->id;
                    $teletravailleurId = $teletravailleur->teletravailleur->id;

                    $selectedMonth = request()->input('month', now()->setTimezone('UTC')->format('Y-m'));
                    $selectedDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth, 'UTC')->startOfMonth();
                    $startOfMonth = $selectedDate->copy()->startOfDay();
                    $endOfMonth = $selectedDate->copy()->endOfMonth()->endOfDay();

                    $totalHours = round($monthlySeconds / 3600, 2);

                    $monthlyScreenshots = \App\Models\Screenshot::where('teletravailleur_id', $teletravailleurId)
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->count();

                    $totalTasks = 0;
                    $calendars = \App\Models\Calendar::where('user_id', $userId)->get();
                    foreach ($calendars as $calendar) {
                        if (!empty($calendar->tacheList)) {
                            $tasks = is_array($calendar->tacheList) ? $calendar->tacheList : [];
                            foreach ($tasks as $task) {
                                $taskDateKey = isset($task['deadline']) ? 'deadline' : null;
                                $taskDate = $taskDateKey ? \Carbon\Carbon::parse($task[$taskDateKey])->setTimezone('UTC') : null;
                                if ($taskDate && $taskDate->between($startOfMonth, $endOfMonth)) {
                                    $totalTasks += 1;
                                }
                            }
                        }
                    }
                    @endphp

                    <div id="chartContainer">
                        <canvas id="monthlyStatsChart" style="max-width: 400px; max-height: 400px; margin: 0 auto;"></canvas>
                    </div>
                    <div id="noDataMessage" style="display: none; text-align: center; margin-top: 10px;">
                        <p>No data available for this month.</p>
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
                                    document.head.appendChild(datalabelsScript);
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
                                    labels: ['Worked Hours (h)', 'Screenshots', 'Tasks'],
                                    datasets: [{
                                        label: 'Monthly Statistics',
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
                                        legend: { position: 'top', labels: { color: '#fff' } },
                                        title: { display: true, text: 'Monthly Statistics - <?php echo $selectedDate->format('F Y'); ?>', font: { size: 16 }, color: '#fff' },
                                        tooltip: { enabled: true, callbacks: { label: function(context) { let label = context.label || ''; if (label) label += ': '; let value = context.parsed; if (value === 0.001) value = 0; label += value + (context.label === 'Worked Hours (h)' ? ' hours' : ''); return label; } } },
                                        datalabels: { color: '#fff', font: { weight: 'bold', size: 14 }, formatter: (value, context) => { let realValue = value === 0.001 ? 0 : value; if (realValue === 0) return ''; const percentage = ((realValue / total) * 100).toFixed(1); return percentage + '%'; }, anchor: 'center', align: 'center' }
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
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<style>
    .table th, .table td { vertical-align: middle; }
    .d-flex > .btn { flex-shrink: 0; white-space: nowrap; }
    .button-container { align-items: flex-start; padding: 5px; border-radius: 10px; }
    .button-container a { display: inline-block; padding: 10px 20px; text-align: center; text-decoration: none; color: #E1E4E6; background: rgba(0, 0, 0, 0.5); border: 1px solid #444; border-radius: 77px; font-weight: bold; font-size: 28px; width: 525px; height: 75px; margin-left: -180px; }
    .hidden-button { display: none; }
    .button-wrapper { position: relative; width: 525px; height: 75px; }
    .button-wrapper .default-button { display: flex; position: absolute; top: 0; left: 0; }
    .button-wrapper .hidden-button { display: none; position: absolute; top: 0; left: 0; }
    .button-wrapper:hover .default-button { display: none !important; }
    .button-wrapper:hover .hidden-button { display: flex !important; }
    .table-dark th, .table-dark td { border: none; }
    .table-dark tr:hover { background: rgba(255, 255, 255, 0.1); }
    .badge.bg-success { background-color: #28a745 !important; }
    .badge.bg-danger { background-color: #dc3545 !important; }
    /* Style pour les lignes de séparation entre les sections */
    .section-divider { border-top: 1px solid #444; margin: 20px 0; }
    .section-content { padding: 10px 0; }

</style>


@endsection
