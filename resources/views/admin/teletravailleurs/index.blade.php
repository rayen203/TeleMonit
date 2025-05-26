@extends('layouts.base-interface')

@section('content')
<div style="position: relative; width: 100%; min-height: 100vh; overflow: visible; margin-left: 13%; ">
    <h2 style="color: #E1E4E6; font-weight: bold; font-size: 35px; margin-left: 27%; margin-bottom: 80px; margin-top: 20px; text-decoration: underline ;">Remote Employees</h2>

    <div style="margin-left: 20px; margin-bottom: 20px;">
        <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; text-decoration: none;">
            <img src="{{ asset('images/fleche.png') }}" alt="Back Arrow" style="width: 50px; height: 30px; margin-right: 10px;">
        </a>
    </div>

    @if ($teletravailleurs->isEmpty())
        <p style="color: #6c757d; margin-left: 20px;">No remote employees found.</p>
    @else



        <div class="d-flex justify-content-start align-items-center mb-3" style="padding: 10px 0; gap: 10px; height: 94px; display: flex; flex-direction: row; margin-top: -17px;">
            <div class="btn btn-primary rounded-pill px-4 py-2" style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; color: #E1E4E6; font-weight: bold; font-size: 20px; border-radius: 77px; display: flex; align-items: center; justify-content: center; text-align: center; backdrop-filter: blur(10px);">Name & Surname</div>
            <div class="btn btn-primary rounded-pill px-4 py-2" style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; color: #E1E4E6; font-weight: bold; font-size: 20px; border-radius: 77px; width: 290px; display: flex; align-items: center; justify-content: center; text-align: center; backdrop-filter: blur(10px);">Total Hours/Day</div>
            <div class="btn btn-primary rounded-pill px-4 py-2" style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; color: #E1E4E6; font-weight: bold; font-size: 20px; border-radius: 77px; width: 290px; display: flex; align-items: center; justify-content: center; text-align: center; backdrop-filter: blur(10px);">Statut</div>
            <div class="btn btn-primary rounded-pill px-4 py-2" style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; color: #E1E4E6; font-weight: bold; font-size: 20px; border-radius: 77px; width: 236px; display: flex; align-items: center; justify-content: center; text-align: center; backdrop-filter: blur(10px);">Details & Actions</div>
        </div>


        <div style="display: flex; flex-direction: column; gap: 20px; max-width: 100%; height: 360px; overflow-y: auto; position: relative; padding-right: 10px;">
            @foreach ($teletravailleurs as $teletravailleur)
                @php
                    $workingHours = $teletravailleur->workingHours->filter(function($session) {
                        return \Carbon\Carbon::parse($session->start_time)->toDateString() === now()->toDateString();
                    });

                    $totalSeconds = 0;
                    foreach ($workingHours as $session) {
                        if ($session->stop_time) {
                            $totalSeconds += $session->total_seconds;
                        } else {
                            $startTime = \Carbon\Carbon::parse($session->start_time);
                            $currentTime = now();
                            $seconds = $currentTime->diffInSeconds($startTime);

                            if ($session->pause_time && $session->resume_time) {
                                $pauseTime = \Carbon\Carbon::parse($session->pause_time);
                                $resumeTime = \Carbon\Carbon::parse($session->resume_time);
                                $pauseSeconds = $resumeTime->diffInSeconds($pauseTime);
                                $seconds -= $pauseSeconds;
                            } elseif ($session->pause_time) {
                                $pauseTime = \Carbon\Carbon::parse($session->pause_time);
                                $seconds = $pauseTime->diffInSeconds($startTime);
                            }
                            $totalSeconds += $seconds;
                        }
                    }

                    $hours = floor($totalSeconds / 3600);
                    $minutes = floor(($totalSeconds % 3600) / 60);
                    $displayTime = $hours > 0 ? ($minutes > 0 ? "{$hours}H{$minutes}min" : "{$hours}H") : ($minutes > 0 ? "{$minutes}min" : "0H");
                @endphp
                <div style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; padding: 15px; display: flex; flex-direction: row; align-items: center; border: 1px solid #444; height: 80px; width: 1060px; justify-content: space-between; gap: 10px; backdrop-filter: blur(10px);">

                    <div style="display: flex; align-items: center; flex: 1; margin-left: 5px; margin-right: 10px; font-weight: bold;">
                        <img src="{{ $teletravailleur->photoProfil ? (Str::startsWith($teletravailleur->photoProfil, 'images/') ? asset($teletravailleur->photoProfil) : asset('storage/' . $teletravailleur->photoProfil)) : asset('images/default-profile.png') }}" alt="Profile Photo" class="rounded-circle mr-2" style="width: 46px; height: 44px; border-radius: 77px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
                        <span style="color: #E1E4E6; font-size: 16px; font-family: 'Inter', sans-serif;">{{ $teletravailleur->utilisateur?->nom ?? 'N/A' }} {{ $teletravailleur->utilisateur?->prenom ?? '' }}</span>
                    </div>


                    <div style="flex: 1; text-align: center; margin-left: 20px; margin-right: 120px;">
                        <span style="color: #E1E4E6; font-size: 18px; font-family: 'Inter', sans-serif; font-weight: bold;">{{ $displayTime }}</span>
                    </div>


                    <div style="display: flex; align-items: center; justify-content: center; margin-left: 50px; margin-right: 125px;">
                        <div class="bg-{{ $teletravailleur->utilisateur?->isOnline() ? 'green' : 'red' }}-500 rounded-full w-4 h-4 mr-2" style="display: inline-block; vertical-align: middle;"></div>
                        <span class="text-xl font-bold font-['Inter'] {{ $teletravailleur->utilisateur?->isOnline() ? 'text-green-500' : 'text-red-500' }}" style="display: inline-block; vertical-align: middle; font-size: 16px;">{{ $teletravailleur->utilisateur?->isOnline() ? 'Connected' : 'Disconnected' }}</span>
                    </div>


                    <div style="display: flex; align-items: center; justify-content: flex-end; margin-left: 30px; margin-right: 90px;">
                        <a href="{{ route('admin.teletravailleur.details', $teletravailleur->utilisateur->id) }}" style="color: #E1E4E6; font-size: 16px; font-family: 'Inter', sans-serif; text-decoration: none; margin-right: 10px;">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="12" cy="12" r="8" stroke-width="1.5"></circle>
                                <line x1="16.5" y1="16.5" x2="20" y2="20" stroke-width="1.5"></line>
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('admin.teletravailleur.destroy', $teletravailleur->id) }}" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce télétravailleur ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; padding: 0; margin: 0;">
                                <img src="{{ asset('images/delete.png') }}" alt="Delete Icon" style="width: 20px; height: 20px; vertical-align: middle;">
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif


    <script>
        function updateStatus() {
            fetch('/admin/teletravailleurs/status')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur réseau ou route non trouvée');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.teletravailleurs) {
                        data.teletravailleurs.forEach(teletravailleur => {
                            const statusElement = document.querySelector(`[data-status="${teletravailleur.id}"]`);
                            const onlineElement = document.querySelector(`[data-online="${teletravailleur.id}"]`);
                            if (statusElement && onlineElement) {
                                const isActive = teletravailleur.last_activity && (new Date() - new Date(teletravailleur.last_activity)) / 60000 < 5;
                                statusElement.className = `bg-${isActive ? 'green' : 'red'}-500 rounded-full w-4 h-4 mr-2`;
                                onlineElement.textContent = isActive ? 'Connected' : 'Disconnected';
                                onlineElement.className = `text-xl font-bold font-['Inter'] ${isActive ? 'text-green-500' : 'text-red-500'}`;
                            }
                        });
                    } else {
                        console.warn('Aucune donnée de télétravailleurs reçue');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la mise à jour des statuts:', error.message);
                    document.querySelectorAll('[data-status]').forEach(element => {
                        element.className = 'bg-red-500 rounded-full w-4 h-4 mr-2';
                        element.nextElementSibling.textContent = 'Disconnected (Erreur API)';
                        element.nextElementSibling.className = 'text-xl font-bold font-['Inter'] text-red-500';
                    });
                });
        }

        updateStatus();
        setInterval(updateStatus, 10000);
    </script>
</div>
@endsection
