<!-- resources/views/calendars/index.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Calendrier et Tâches</title>
    @vite('resources/css/app.css') <!-- Utilisation de Vite pour charger app.css -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <style>
        #calendar {
            max-width: 900px;
            margin: 20px auto;
            min-height: 400px;
            display: block; /* Assure que le calendrier est visible */
        }
        .task-overdue {
            color: red;
            font-weight: bold;
        }
        .task-completed {
            text-decoration: line-through;
            color: gray;
        }
        .task-list {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Mon Calendrier et Tâches</h1>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Boutons pour ajouter et voir le calendrier -->
        <div class="mb-3">
            <a href="{{ route('calendars.tasks.create', \Carbon\Carbon::today()->format('Y-m-d')) }}" class="btn btn-success">Ajouter une Tâche</a>
            <button onclick="scrollToCalendar()" class="btn btn-primary">Voir Calendrier</button>
        </div>

        <!-- Calendrier -->
        <div id="calendar"></div>

        <!-- Liste des tâches -->
        <h2>Liste des Tâches</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Début</th>
                    <th>Échéance</th>
                    <th>Statut</th>
                    <th>Heures Travaillées</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($calendars as $calendar)
                    <!-- Ligne pour la date avec un bouton "Supprimer le Calendrier" -->
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($calendar->date)->format('d/m/Y') }}</td>
                        <td colspan="6"></td>
                        <td>
                            <form action="{{ route('calendars.destroy', $calendar->date) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce calendrier et toutes ses tâches ?')">Supprimer le Calendrier</button>
                            </form>
                        </td>
                    </tr>
                    <!-- Afficher les tâches si elles existent -->
                    @if (!empty($calendar->tacheList))
                        @foreach ($calendar->tacheList as $tache)
                            <tr class="{{ $calendar->isTacheOverdue($tache) ? 'task-overdue' : '' }} {{ $tache['status'] === 'completed' ? 'task-completed' : '' }}">
                                <td>{{ \Carbon\Carbon::parse($tache['start_date'])->format('d/m/Y') }}</td>
                                <td>{{ $tache['title'] }}</td>
                                <td>{{ $tache['description'] ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($tache['start_date'])->format('d/m/Y H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($tache['deadline'])->format('d/m/Y H:i') }}</td>
                                <td>{{ $tache['status'] === 'completed' ? 'Terminée' : 'En cours' }}</td>
                                <td>{{ $calendar->getWorkedHoursForTache($tache) }}</td>
                                <td>
                                    <a href="{{ route('calendars.tasks.edit', [\Carbon\Carbon::parse($tache['start_date'])->format('Y-m-d'), $tache['id']]) }}" class="btn btn-warning btn-sm">Modifier</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">Aucune tâche pour cette date.</td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="8">Aucune tâche pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <script>
        // Définir la fonction scrollToCalendar avant son utilisation
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

                // Préparer les événements côté PHP
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
                        // Rediriger vers le formulaire d'ajout de tâche pour la date sélectionnée
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
    </script>
</body>
</html>
