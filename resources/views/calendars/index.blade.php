<!-- resources/views/calendars/index.blade.php -->
@extends('layouts.base-interface')

@section('content')

<div class="d-flex justify-content-center min-vh-100" style="padding: 20px;">
    <!-- Section Gauche (Détails Utilisateur) -->
    <br>
    <div style="width: 574px; margin-right: 1300px;">
        <!-- Détails Utilisateur -->
        <div class="p-3" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; width: 574px; height: 220px; position: relative; border: 0.5px solid rgb(113, 113, 113); backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);">
            <div class="d-flex align-items-center mb-2">
                <div style="position: relative;">
                    <!-- Photo de profil -->
                    @if(auth()->user()->teletravailleur)
                        <img src="{{ auth()->user()->teletravailleur && auth()->user()->teletravailleur->photoProfil ? (Str::startsWith(auth()->user()->teletravailleur->photoProfil, 'images/') ? asset(auth()->user()->teletravailleur->photoProfil) : asset('storage/' . auth()->user()->teletravailleur->photoProfil)) : asset('images/default-profile.png') }}" alt="Profile Photo" class="rounded-circle me-3" style="width: 127px; height: 122px; margin-left: 190px; border-radius: 77px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
                    @else
                        <img src="{{ auth()->user()->photoProfil ? asset('storage/' . auth()->user()->photoProfil) : asset('images/avatar1.png') }}" alt="Profile Photo" class="rounded-circle me-3" style="width: 127px; height: 122px; margin-left: 190px;" onerror="this.src='{{ asset('images/avatar1.png') }}';">
                    @endif
                    <!-- Bouton d'édition -->
                    <a href="{{ route('profile.edit') }}" style="position: absolute; margin-top: -120px; margin-left: 290px;">
                        <img src="{{ asset('images/edit.png') }}" alt="Edit Icon" style="width: 29px; height: 29px;">
                    </a>
                </div>
                <!-- Nom -->
                <h5 class="mb-0" style="color: #E1E4E6; margin-left: 350px; margin-top: -70px; font-weight: bold; font-size: 23px;">{{ auth()->user()->nom ?? 'Utilisateur' }} {{ auth()->user()->prenom ?? '' }}</h5>
                <div>
                    <!-- Email -->
                    <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 195px; margin-top: 60px; text-decoration: underline; font-weight: bold;">{{ auth()->user()->email ?? 'mail@gmail.com' }}</p>
                    <!-- Téléphone -->
                    @if(auth()->user()->teletravailleur)
                        <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 425px; margin-top: -17px; font-weight: bold;">+216 {{ auth()->user()->teletravailleur->telephone ?? 'N/A' }}</p>
                    @else
                        <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 425px; margin-top: -17px; font-weight: bold;">+216 ********</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Boutons -->
        <div id="button-container" style="margin-top: 20px; display: flex; flex-direction: column; gap: 10px;" class="button-container">
            @if(!auth()->user()->teletravailleur)
                <!-- Boutons pour l'administrateur -->
                <!-- Bouton Profiles (caché par défaut, affiché au survol) -->
                <div class="button-wrapper">
                    <a href="#" class="btn btn-primary rounded-pill px-4 py-2 default-button" style="display: flex; align-items: center; justify-content: center; width: 525px; height: 75px; margin-left: 0;">
                        <img src="{{ asset('images/Component.png') }}" alt="Component Icon" style="width: 38px; height: 22px; margin-right: 30px; margin-left: 460px;">
                    </a>
                    <a href="{{ route('admin.teletravailleurs.index') }}" class="btn btn-primary rounded-pill px-4 py-2 hidden-button" style="display: none; align-items: center; justify-content: center; width: 525px; height: 75px; backdrop-filter: blur(10px);">
                        <img src="{{ asset('images/profile.png') }}" alt="Profile Icon" style="width: 51px; height: 36px; margin-right: 40px; margin-left: 60px;">
                        Profiles
                    </a>
                </div>
            @else

                <!-- Bouton ChronoPanel (caché par défaut, affiché au survol) -->
                <div class="button-wrapper">
                    <a href="#" class="btn btn-primary rounded-pill px-4 py-2 default-button" style="display: flex; align-items: center; justify-content: center; width: 525px; height: 75px; margin-left: 0;">
                        <img src="{{ asset('images/Component.png') }}" alt="Component Icon" style="width: 38px; height: 22px; margin-right: 30px; margin-left: 460px;">
                    </a>
                    <a href="{{ route('teletravailleur.dashboard') }}" class="btn btn-primary rounded-pill px-4 py-2 hidden-button" style="display: none; align-items: center; justify-content: center; width: 525px; height: 75px; backdrop-filter: blur(10px);">
                        <img src="{{ asset('images/chrono.png') }}" alt="Chrono Icon" style="width: 55px; height: 53px; margin-right: 10px; margin-left: 110px;">
                        ChronoPanel
                    </a>
                </div>
            @endif

            <!-- Bouton Calendar (toujours affiché, sans effet de survol) -->
            <a href="{{ route('calendars.index') }}" class="btn btn-primary rounded-pill px-4 py-2" style="display: flex; align-items: center; justify-content: center; width: 525px; height: 75px; backdrop-filter: blur(10px);">
                <img src="{{ asset('images/calendar.png') }}" alt="Calendar Icon" style="width: 38px; height: 38px; margin-right: 40px; margin-left: 90px;">
                Calendar
            </a>



        </div>
    </div>

   <!-- Section Droite (Calendrier et Tâches) -->
    <div class="flex-grow-1"  style="max-width: 1050px;  ;margin-left: 600px; ">
    <!-- Message de succès -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif



    <!-- Calendrier -->
    <div id="calendar-container" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; padding: 15px; border: 1px solid #444; width: 100%; height: 570px; overflow-y: auto; position: relative; backdrop-filter: blur(10px); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3); margin-bottom: 20px;  margin-top: -410px; scrollbar-width: none;">
        <!-- Navigation par mois -->
        @php
            $selectedMonth = request()->input('month', now()->format('Y-m'));
            $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
        @endphp
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-bottom: 30px; margin-top: 40px; ">
            <button onclick="window.location.href='?month={{ $currentMonth->copy()->subMonth()->format('Y-m') }}';" style="background: rgba(0, 0, 0, 0.5); color: white; border: 1px solid #444; border-radius: 10px; padding: 5px 10px; font-size: 16px; cursor: pointer;">< Previous</button>
            <h2 style="color: white; font-size: 30px; margin: 0; font-weight: bold; ">{{ $currentMonth->format('F Y') }}</h2>
            <button onclick="window.location.href='?month={{ $currentMonth->copy()->addMonth()->format('Y-m') }}';" style="background: rgba(0, 0, 0, 0.5); color: white; border: 1px solid #444; border-radius: 10px; padding: 5px 10px; font-size: 16px; cursor: pointer;  ">Next ></button>
        </div>
        <div style="display: flex; justify-content: space-between; color: #A9A9A9; font-weight: bold; padding: 5px 0;">
            <span>Sun</span>
            <span>Mon</span>
            <span>Tue</span>
            <span>Wed</span>
            <span>Thu</span>
            <span>Fri</span>
            <span>Sat</span>
        </div>
        <hr style="border: 1px solid #666; margin: 10px 0;">
        <div style="color: white;">
            @php
                $endOfMonth = $currentMonth->copy()->endOfMonth();
                $days = [];
                $date = $currentMonth->copy()->startOfWeek();
                while ($date <= $endOfMonth) {
                    $days[] = $date->copy();
                    $date->addDay();
                }
                $tasks = [];
                foreach ($calendars as $calendar) {
                    if (!empty($calendar->tacheList)) {
                        foreach ($calendar->tacheList as $tache) {
                            $taskDate = \Carbon\Carbon::parse($tache['start_date'])->toDateString();
                            if (\Carbon\Carbon::parse($taskDate)->between($currentMonth, $endOfMonth)) {
                                $tasks[$taskDate][] = $tache;
                            }
                        }
                    }
                }
            @endphp
            @foreach($days as $day)
                @php
                    $dayDate = $day->toDateString();
                    $isToday = $day->isToday();
                    $dayTasks = isset($tasks[$dayDate]) ? $tasks[$dayDate] : [];
                    $isInCurrentMonth = $day->month === $currentMonth->month;
                @endphp
                @if($isInCurrentMonth)
                    <div class="calendar-day" data-date="{{ $day->format('Y-m-d') }}" style="border-bottom: 1px solid #444; padding: 25px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span class="day-label" style="font-weight: bold; color: #A9A9A9; {{ $isToday ? 'color: #FF4500 !important;' : '' }}">{{ $day->format('D d') }}</span>
                            <div>
                                <a href="{{ route('calendars.tasks.create', $day->format('Y-m-d')) }}" style="color: #98FB98; text-decoration: none; margin-right: 10px;">+ add Task</a>
                            </div>
                        </div>
                        @if(!empty($dayTasks))
                            @foreach($dayTasks as $task)
                                @php
                                    $calendar = $calendars->firstWhere('date', $day->format('Y-m-d'));
                                @endphp
                                <div style="margin-left: 20px; display: flex; justify-content: space-between; align-items: center; color: {{ $calendar && $calendar->isTacheOverdue($task) ? '#FF4500' : '#FFFFFF' }};">
                                    <span>- {{ $task['title'] }}: {{ $task['description'] ?? 'No description' }}</span>
                                    <div>
                                        <a href="{{ route('calendars.tasks.edit', [$day->format('Y-m-d'), $task['id']]) }}" style="color: #FFD700; text-decoration: none; margin-right: 10px;">Update task</a>
                                        <a href="#" onclick="if(confirm('Are you sure you want to delete this task ?')) { document.getElementById('delete-task-form-{{ $task['id'] }}-{{ $day->format('Y-m-d') }}').submit(); }" style="color: #FF6347; text-decoration: none;">× Delete task</a>
                                        <form id="delete-task-form-{{ $task['id'] }}-{{ $day->format('Y-m-d') }}" action="{{ route('calendars.destroy', [$day->format('Y-m-d'), $task['id']]) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>


</div>
</div>

<!-- Styles spécifiques -->
<style>
    .alert-success {
        background: rgba(40, 167, 69, 0.5);
        color: white;
        border: 1px solid #28a745;
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 20px;
        width: 766px;
        text-align: center;
    }
    #calendar-container {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 77px;
        padding: 15px;
        border: 1px solid #444;
        width: 100%;
        height: 600px;
        overflow-y: auto;
        position: relative;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        margin-bottom: 20px;
    }
    #calendar {
        max-width: 100%;
        margin: 0;
        min-height: 400px;
        display: block;
        color: white;
    }
    .fc-toolbar-title, .fc-col-header-cell, .fc-daygrid-day-number {
        color: white !important;
    }
    .fc-button {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: white !important;
        border: 1px solid #444 !important;
    }
    .fc-button:hover {
        background-color: rgba(255, 255, 255, 0.2) !important;
    }
    .task-overdue {
        color: #dc3545;
        font-weight: bold;
    }
    .task-completed {
        text-decoration: line-through;
        color: gray;
    }
    .task-table {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 20px;
        padding: 15px;
        border: 1px solid #444;
        width: 100%;
        margin: 0;
        backdrop-filter: blur(10px);
    }
    .task-table table {
        width: 100%;
        color: white;
    }
    .task-table th, .task-table td {
        border: 1px solid #444;
        padding: 8px;
        text-align: left;
    }
    .task-table th {
        background: rgba(255, 255, 255, 0.1);
    }
    .task-table a, .task-table button {
        color: white;
        border: 1px solid #444;
        padding: 5px 10px;
        border-radius: 5px;
        text-decoration: none;
    }
    .task-table a:hover, .task-table button:hover {
        background: rgba(255, 255, 255, 0.1);
    }
    /* Styles pour les boutons de la section gauche */
    .button-container {
        align-items: flex-start;
        padding: 5px;
        border-radius: 10px;
    }
    .button-container a {
        display: inline-block;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        color: #E1E4E6;
        background: rgba(0, 0, 0, 0.5);
        border: 1px solid #444;
        border-radius: 77px;
        font-weight: bold;
        font-size: 28px;
        width: 525px;
        height: 75px;
    }
    .hidden-button {
        display: none;
    }
    .button-wrapper {
        position: relative;
        width: 525px;
        height: 75px;
    }
    .button-wrapper .default-button {
        display: flex;
        position: absolute;
        top: 0;
        left: 0;
    }
    .button-wrapper .hidden-button {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
    }
    .button-wrapper:hover .default-button {
        display: none !important;
    }
    .button-wrapper:hover .hidden-button {
        display: flex !important;
    }
    /* Ajustement pour le bouton + ADD PROFILE */
    .button-wrapper .hidden-button.add-profile {
        width: 114px;
        height: 46px;
        font-size: 12px;
        margin-left: 0;
        margin-top: 10px;
    }
</style>

<!-- Inclusion de FullCalendar (si non présent dans base-interface) -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<!-- Script pour FullCalendar -->
<script>
    function scrollToCalendar() {
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            calendarEl.scrollIntoView({ behavior: 'smooth' });
        } else {
            console.error("Élément #calendar non trouvé pour le défilement.");
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        try {
            var calendarEl = document.getElementById('calendar');
            if (!calendarEl) {
                console.error("Élément #calendar non trouvé dans le DOM.");
                return;
            }

            var events = [
                @foreach ($calendars as $calendar)
                    @if (!empty($calendar->tacheList))
                        @foreach ($calendar->tacheList as $tache)
                            @if (isset($tache['title']) && isset($tache['start_date']) && isset($tache['deadline']))
                                @php
                                    $title = htmlspecialchars($tache['title'], ENT_QUOTES, 'UTF-8');
                                    if ($calendar->isTacheOverdue($tache)) {
                                        $title .= ' (En retard)';
                                    }
                                    $backgroundColor = $tache['status'] === 'completed' ? '#28a745' : ($calendar->isTacheOverdue($tache) ? '#dc3545' : '#007bff');
                                    $borderColor = $backgroundColor;
                                @endphp
                                {
                                    title: '{{ $title }}',
                                    start: '{{ \Carbon\Carbon::parse($tache['start_date'])->toIso8601String() }}',
                                    end: '{{ \Carbon\Carbon::parse($tache['deadline'])->toIso8601String() }}',
                                    backgroundColor: '{{ $backgroundColor }}',
                                    borderColor: '{{ $borderColor }}'
                                },
                            @else
                                console.warn("Tâche invalide pour le calendrier :", {{ json_encode($tache) }});
                            @endif
                        @endforeach
                    @else
                        {
                            title: 'Aucune tâche',
                            start: '{{ \Carbon\Carbon::parse($calendar->date)->toIso8601String() }}',
                            backgroundColor: '#6c757d',
                            borderColor: '#6c757d'
                        },
                    @endif
                @endforeach
            ];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                select: function(info) {
                    window.location.href = '{{ url("/calendars") }}/' + info.startStr + '/tasks/create';
                },
                events: events,
                eventClick: function(info) {
                    alert('Tâche : ' + info.event.title + '\nDébut : ' + info.event.start.toLocaleString() + '\nFin : ' + (info.event.end ? info.event.end.toLocaleString() : 'N/A'));
                }
            });
            calendar.render();
        } catch (error) {
            console.error("Erreur lors de l'initialisation de FullCalendar :", error);
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        const days = document.querySelectorAll('.calendar-day');
        days.forEach(day => {
            day.addEventListener('click', function() {
                // Réinitialiser la couleur de tous les jours à gris
                days.forEach(d => {
                    const label = d.querySelector('.day-label');
                    if (d.getAttribute('data-date') !== '{{ \Carbon\Carbon::today()->format('Y-m-d') }}') {
                        label.style.color = '#A9A9A9';
                    }
                });
                // Mettre le jour cliqué en blanc
                const label = this.querySelector('.day-label');
                label.style.color = '#FFFFFF';
                // Si c'est aujourd'hui, conserver la priorité de la couleur rouge
                if (this.getAttribute('data-date') === '{{ \Carbon\Carbon::today()->format('Y-m-d') }}') {
                    label.style.color = '#FF4500';
                }
            });
        });
    });

    function scrollToCalendar() {
        var calendarEl = document.getElementById('calendar-container');
        if (calendarEl) {
            calendarEl.scrollIntoView({ behavior: 'smooth' });
        } else {
            console.error("Élément #calendar-container non trouvé pour le défilement.");
        }
    }

</script>
@endsection
