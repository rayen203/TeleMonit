@extends('layouts.base-interface')

@section('content')

<div style="position: relative; width: 100%; min-height: 100vh; overflow: visible;  padding: -10px; ">

    <div style="position: absolute; left: -210px; top: 60px; z-index: 10;">
        <div class="p-3" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; width: 574px; height: 220px; position: relative; border: 0.5px solid rgb(113, 113, 113); backdrop-filter: blur(10px);">
            <div class="d-flex align-items-center mb-2">
                <div style="position: relative;">
                    <img src="{{ Auth::user()->teletravailleur && Auth::user()->teletravailleur->photoProfil ? (Str::startsWith(Auth::user()->teletravailleur->photoProfil, 'images/') ? asset(Auth::user()->teletravailleur->photoProfil) : asset('storage/' . Auth::user()->teletravailleur->photoProfil)) : asset('images/default-profile.png') }}" alt="Admin Photo" class="rounded-circle me-3" style="width: 127px; height: 122px; margin-left: 190px; border-radius: 77px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
                    <a href="{{ route('profile.edit') }}" style="position: absolute; margin-top: -120px; margin-left: 290px;">
                        <img src="{{ asset('images/edit.png') }}" alt="Edit Icon" style="width: 29px; height: 29px;">
                    </a>
                </div>
                <h5 class="mb-0" style="color: #E1E4E6; margin-left: 350px; margin-top: -70px; font-weight: bold; font-size: 23px;">{{ Auth::user()->nom ?? 'Admin' }} {{ Auth::user()->prenom ?? '' }}</h5>
                <div>
                    <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 195px; margin-top: 60px; text-decoration: underline; font-weight: bold;">{{ Auth::user()->email ?? 'mail@gmail.com' }}</p>
                    <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 425px; margin-top: -17px; font-weight: bold;">+216 {{ $teletravailleur->telephone ?? 'N/A' }}</p>

                </div>
            </div>
        </div>
    </div>


    <div id="button-container" style="position: absolute; left: 10px; top: 300px; z-index: 10; display: flex; flex-direction: column; gap: 10px;" class="button-container">
        <a href="{{ route('teletravailleur.dashboard') }}" class="btn btn-primary rounded-pill px-4 py-2" style="display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
            <img src="{{ asset('images/chrono.png') }}" alt="Chrono Icon" style="width: 55px; height: 53px; margin-right: 10px; margin-left: 110px;">
            ChronoPanel
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




    <div style="margin-left: 360px; min-height: calc(100vh - 40px); padding: 20px 0 20px 20px; max-width: none; width: calc(100% - 300px);">
        <div class="container-fluid" style="color: white; padding-right: 0; margin-right: 0;">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($notification)
                <div class="alert alert-info" role="alert">{{ $notification }}</div>
            @endif
            <div id="captureNotification" class="alert alert-success" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
                Capture enregistrée avec succès !
            </div>

            <br>

            <div style="text-align: center; margin-bottom: 20px;  ">
                <div style="width: 675px; height: 80px; font-size: 60px; font-weight: bold; color: #FFFFFF; margin: 10px auto; background: rgba(0, 0, 0, 0.5); border-radius: 77px; border: 1px solid #444; display: flex; align-items: center; justify-content: center; padding: 0 15px; backdrop-filter: blur(10px); font-family: 'Jersey 25', sans-serif; ">
                    <span id="timerDisplay">00:00:00</span>
                </div>
                <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
                    <button id="startBtn" class="btn btn-success rounded-pill" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; border: 1px solid #444; padding: 10px 20px; display: flex; align-items: center; justify-content: center; width: 151px; height: 55px;backdrop-filter: blur(10px);">
                        <img src="{{ asset('images/start.png') }}" alt="Start Icon" style="width: 50px; height: 50px; margin-right: 10px;">
                        <span style="color: #FFFFFF; font-size: 16px; font-weight: bold;">Start</span>
                    </button>
                    <button id="pauseBtn" class="btn btn-warning rounded-pill" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; border: 1px solid #444; padding: 10px 20px; display: flex; align-items: center; justify-content: center; width: 151px; height: 55px; backdrop-filter: blur(10px);" disabled>
                        <img src="{{ asset('images/pause.png') }}" alt="Pause Icon" style="width: 35px; height: 35px; margin-right: 10px;">
                        <span style="color: #FFFFFF; font-size: 16px; font-weight: bold;">Pause</span>
                    </button>
                    <button id="resumeBtn" class="btn btn-info rounded-pill" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; border: 1px solid #444; padding: 10px 20px; display: flex; align-items: center; justify-content: center; width: 151px; height: 55px;backdrop-filter: blur(10px);" disabled>
                        <img src="{{ asset('images/resume.png') }}" alt="Resume Icon" style="width: 50px; height: 50px; margin-right: 10px;">
                        <span style="color: #FFFFFF; font-size: 16px; font-weight: bold;">Resume</span>
                    </button>
                    <button id="stopBtn" class="btn btn-danger rounded-pill" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; border: 1px solid #444; padding: 10px 20px; display: flex; align-items: center; justify-content: center; width: 151px; height: 55px;backdrop-filter: blur(10px);" disabled>
                        <img src="{{ asset('images/stop.png') }}" alt="Stop Icon" style="width: 28px; height: 28px; margin-right: 10px;">
                        <span style="color: #FFFFFF; font-size: 16px; font-weight: bold;">Stop</span>
                    </button>
                </div>
            </div>

            <br>


            <div class="card" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; padding: 15px; border: 1px solid #444; width: 1060px; height: 350px; overflow-y: auto; position: relative; padding-right: 10px; backdrop-filter: blur(10px); scrollbar-width: none; -ms-overflow-style: none; margin-top: 20px;">
                <br>


                <div class="section-content">

                    <a href="{{ route('teletravailleur.chat.index') }}" class="btn btn-primary rounded-pill px-4 py-2" style="display: flex; align-items: center; justify-content: end;  margin-bottom: -45px; margin-right: 20px;font-size: 20px; font-weight: bold;">
                        <img src="{{ asset('images/chatbot.png') }}" alt="Chatbot Icon" style="width: 44px; height: 26.5px; margin-right: 5px; margin-left: 30px; ">
                        CHATBOT
                    </a>

                    <h4 style="font-weight: bold; font-size: 25px; margin-left: 20px;">Hours Tracked</h4>

                    @php

    $todaySeconds = 0;
    $todaySessions = \App\Models\WorkingHour::where('teletravailleur_id', $teletravailleur->id)
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
    $todayFormattedCorrected = "{$hours}h {$minutes}m {$seconds}s";


    $selectedMonth = request()->input('month', now()->format('Y-m'));
    $startOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
    $endOfMonth = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();
    $monthlySessions = \App\Models\WorkingHour::where('teletravailleur_id', $teletravailleur->id)
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
    $monthlyFormattedCorrected = "{$hours}h {$minutes}m {$seconds}s";
@endphp

<p style="margin-top: 17px; margin-left: 20px;">Time Tracked Today : <span id="todayHours" style="margin-left: 60px">{{ $todayFormattedCorrected }}</span></p>
<p style="margin-top: 5px; margin-left: 20px;">Monthly Total : <span id="monthlyHours" style="margin-left: 114px">{{ $monthlyFormattedCorrected }}</span></p>

                </div>


                <hr class="section-divider">


                <div class="section-content">
                    <h4 style="font-weight: bold; font-size: 25px; margin-left: 20px;">Screenshots History</h4>
                    <br>
                    <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 20px; padding: 10px 20px;">
                        <p style="color: #A19B9B; font-weight: bold; text-align: left; font-size: 18px;">Date</p>
                        <p style="color: #A19B9B; font-weight: bold; text-align: center; font-size: 18px;">Screenshots</p>
                        <p style="color: #A19B9B; font-weight: bold; text-align: center; font-size: 18px;">Actions</p>
                    </div>
                    <hr class="section-divider">
                    @if($screenshots->isEmpty())
                        <p class="text-muted" style="margin-left: 20px;">No screenshots recorded.</p>
                    @else
                        @foreach($screenshots as $screenshot)
                            <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 20px; padding: 10px 20px; align-items: center; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
                                <p style="color: #FFFFFF; font-size: 16px; text-align: left;">{{ $screenshot->created_at->format('d/m/Y H:i') }}</p>
                                <div style="text-align: center; display: flex; justify-content: center;">
                                    <img src="{{ asset('storage/' . $screenshot->image_path) }}" alt="Screenshot" style="max-width: 100px; max-height: 100px; border-radius: 10px;" onerror="this.src='{{ asset('images/default-screenshot.png') }}';">
                                </div>
                                <div style="display: flex; justify-content: center; gap: 10px; align-items: center;">
                                    <a href="{{ asset('storage/' . $screenshot->image_path) }}" target="_blank" class="btn btn-secondary btn-sm rounded-pill" style="font-size: 16px; padding: 5px 10px;">View In Full Screen</a>
                                </div>
                            </div>
                        @endforeach
                        <div style="display: grid; grid-template-columns: 1fr 2fr 1fr; gap: 20px; padding: 10px 20px; margin-top: 20px;">
                            <div></div>
                            <div></div>
                            <div style="text-align: center;">
                                {{ $screenshots->links() }}
                            </div>
                        </div>
                    @endif
                </div>


                <hr class="section-divider">


                <div class="section-content">
                    <h4 style="font-weight: bold; font-size: 25px; margin-left: 20px;">Hours History</h4>
                    <br>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 2fr; gap: 20px; padding: 10px 20px;">
                        <p style="color: #A19B9B; font-weight: bold; text-align: left; font-size: 18px;">Date</p>
                        <p style="color: #A19B9B; font-weight: bold; text-align: center; font-size: 18px;">Start</p>
                        <p style="color: #A19B9B; font-weight: bold; text-align: center; font-size: 18px;">End</p>
                        <p style="color: #A19B9B; font-weight: bold; text-align: center; font-size: 18px;">Total Hours</p>
                    </div>
                    <hr class="section-divider">
                    @if($workingHours->isEmpty())
                        <p class="text-muted" style="margin-left: 20px;">No hours recorded.</p>
                    @else
                        @foreach($workingHours as $hour)
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 2fr; gap: 20px; padding: 10px 20px; align-items: center; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);">
                                <p style="color: #FFFFFF; font-size: 16px; text-align: left;">{{ $hour->date ? $hour->date->format('d/m/Y') : 'Not set' }}</p>
                                <p style="color: #FFFFFF; font-size: 16px; text-align: center;">{{ $hour->start_time ? $hour->start_time->format('H:i:s') : 'Not set' }}</p>
                                <p style="color: #FFFFFF; font-size: 16px; text-align: center;">{{ $hour->stop_time ? $hour->stop_time->format('H:i:s') : 'Not set' }}</p>
                                <p style="color: #FFFFFF; font-size: 16px; text-align: center;">
                                    @php
                                        $effectiveSeconds = $hour->total_seconds - ($hour->pause_total_seconds ?? 0);
                                        if ($effectiveSeconds < 0) $effectiveSeconds = $hour->total_seconds;
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
                                {{ $workingHours->links() }}
                            </div>
                        </div>
                    @endif
                </div>


                <hr class="section-divider">


                <div class="section-content">
                    <h4 style="font-weight: bold; font-size: 25px; margin-left: 20px;">Monthly Statistics</h4>
                    <br>
                    <form method="GET" action="{{ route('teletravailleur.dashboard') }}" class="mb-3">
                        <div class="form-group">
                            <label style="font-weight: bold; font-size: 17px; margin-left: 20px;" for="month">Select a month :</label>
                            <input type="month" id="month" name="month" value="{{ request()->input('month', now()->setTimezone('UTC')->format('Y-m')) }}" class="form-control d-inline-block w-auto text-dark" style="background-color: #f0f0f0; border-radius: 7px; padding: 8px 12px; font-size: 16px; color: #000A44; border: 1px solid #ccc; width: 150px; height: 38px;">
                            <input type="hidden" name="t" value="{{ now()->timestamp }}">
                            <button type="submit" class="btn btn-primary" style="background-color: #0A22B9; color: #FFFFFF; border-radius: 7px; padding: 10px 20px; font-size: 15px; border: none; height: 40px; width: 100px;">Show</button>
                        </div>
                    </form>
                    @php
                        $userId = $user->id;
                        $teletravailleurId = $teletravailleur->id;

                        $selectedMonth = request()->input('month', now()->setTimezone('UTC')->format('Y-m'));
                        $selectedDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth, 'UTC')->startOfMonth();
                        $startOfMonth = $selectedDate->copy()->startOfDay();
                        $endOfMonth = $selectedDate->copy()->endOfMonth()->endOfDay();


                        $monthlySeconds = $monthlySeconds ?? 0;
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
    .section-divider { border-top: 1px solid #444; margin: 20px 0; }
    .section-content { padding: 10px 0; }

</style>

<script>
    function formatTime(hours) {
        const totalSeconds = Math.round(hours * 3600);
        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;
        return `${h}h ${m}m ${s}s`;
    }




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
        document.getElementById('timerDisplay').textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
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
        const effectiveSeconds = totalSeconds - Math.floor(pausedTime / 1000);
        const response = await fetch('{{ route("teletravailleur.working-hours.stop") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'include',
            body: JSON.stringify({ total_seconds: effectiveSeconds })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(`Erreur réseau ou serveur : ${response.status} - ${data.error || 'Erreur inconnue'}`);
        }

        if (data.error) {
            showNotification(data.error);
        } else {
            if (effectiveSeconds >= 600) {
                console.log('Déclenchement d’une capture d’écran à l’arrêt de la session.');
                try {
                    await captureScreenshot();
                } catch (captureError) {
                    console.error('Erreur lors de la capture à l’arrêt :', captureError);
                }
            } else {
                console.log('Pas de capture à l’arrêt : session trop courte.', effectiveSeconds);
            }

            clearInterval(timerInterval);
            stopScreenshotInterval();
            totalSeconds = 0;
            lastScreenshotTime = null;
            pausedTime = 0;
            isPaused = false;
            document.getElementById('timerDisplay').textContent = '00:00:00';
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
@endsection
