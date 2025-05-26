@extends('layouts.base-interface')

@section('content')


<div style="position: relative; width: 100%; min-height: 100vh;overflow: visible;">

<div style="position: absolute; left: -210px; top: 60px; z-index: 10;">
    <div class="p-3" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; width: 574px; height: 220px; position: relative; border: 0.5px solid rgb(113, 113, 113); backdrop-filter: blur(10px);">
        <div class="d-flex align-items-center mb-2">
            <div style="position: relative;">
                <img src="{{ Auth::user()->photoProfil ? asset('storage/' . Auth::user()->photoProfil) : asset('images/avatar1.png') }}" alt="Admin Photo" class="rounded-circle me-3" style="width: 127px; height: 122px; margin-left: 190px;" onerror="this.src='{{ asset('images/avatar1.png') }}';">
                <a href="{{ route('profile.edit') }}" style="position: absolute; margin-top: -120px; margin-left: 290px;">
                    <img src="{{ asset('images/edit.png') }}" alt="Edit Icon" style="width: 29px; height: 29px;">
                </a>
            </div>
            <h5 class="mb-0" style="color: #E1E4E6; margin-left: 350px; margin-top: -70px; font-weight: bold;font-size: 23px;">{{ Auth::user()->nom ?? 'Admin' }} {{ Auth::user()->prenom ?? '' }}</h5>

            <div>
                <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 195px; margin-top: 60px; text-decoration: underline; font-weight: bold;">{{ Auth::user()->email ?? 'mail@gmail.com' }}</p>
                <p class="mb-0" style="color: #FFFFFF; width: 180px; height: 19px; margin-left: 420px; margin-top: -17px; font-weight: bold;"> +216 ******** </p>
            </div>
        </div>
    </div>
</div>


<div id="button-container" style="position: absolute; left: 10px; top: 300px; z-index: 10; display: flex; flex-direction: column; gap: 10px;" class="button-container">
    <a href="{{ route('admin.teletravailleurs.index') }}" class="btn btn-primary rounded-pill px-4 py-2" style="display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
        <img src="{{ asset('images/profile.png') }}" alt="Profile Icon" style="width: 51px; height: 36px; margin-right: 40px; margin-left: 60px;">
        Profiles
    </a>
    <div class="button-wrapper">
        <a href="#" class="btn btn-primary rounded-pill px-4 py-2 default-button" style="display: flex; align-items: center; justify-content: center; width: 525px; height: 75px; margin-left: -480px; ">
            <img src="{{ asset('images/Component.png') }}" alt="Component Icon" style="width: 38px; height: 22px; margin-right: 30px; margin-left: 460px;">

        </a>
        <a href="{{ route('calendars.index') }}" class="btn btn-primary rounded-pill px-4 py-2 hidden-button" style="display: none; align-items: center; justify-content: center; width: 525px; height: 75px; backdrop-filter: blur(10px);">
            <img src="{{ asset('images/calendar.png') }}" alt="Calendar Icon" style="width: 38px; height: 38px; margin-right: 40px; margin-left: 90px;">
            Calendar
        </a>
    </div>
</div>

<br><br><br><br>

    <div style="margin-left: 360px; min-height: calc(100vh - 40px); padding: 20px 0 20px 20px; max-width: none; width: calc(100% - 300px);">
        <div class="container-fluid" style="color: white; padding-right: 0; margin-right: 0;">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }} - Veuillez contacter l'administrateur.
                </div>
            @endif



            <br><br>

           <div style="display: flex; justify-content: flex-end; margin-bottom: 0.5rem; margin-right: 135px; padding-right: 0;  ">
                <a href="{{ route('admin.teletravailleur.create') }}" class="btn text-zinc-200 rounded-[36.5px] text-[12px] font-black font-['Inter'] tracking-wide" style="width: 114px; height: 46px; background-color: #43D941; display: flex; align-items: center; justify-content: center; transform: translateY(-25px); ">+ ADD PROFILE</a>
            </div>


            <div class="row">
                <div class="col-12">
                    @if ($teletravailleurs->isEmpty())
                        <p class="text-muted">Aucun télétravailleur enregistré.</p>
                    @else
                        <div class="card">
                            <div class="card-body">

                                <div class="d-flex justify-content-start align-items-center mb-3" style="padding: 10px 0; gap: 10px; height: 94px; display: flex; flex-direction: row; margin-top: -17px; ">
                                    <div class="btn btn-primary rounded-pill px-4 py-2" style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; color: #E1E4E6; font-weight: bold; font-size: 20px; border-radius: 77px;  display: flex; align-items: center; justify-content: center; text-align: center; backdrop-filter: blur(10px);">Name & Surname</div>
                                    <div class="btn btn-primary rounded-pill px-4 py-2" style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; color: #E1E4E6; font-weight: bold; font-size: 20px; border-radius: 77px; width: 290px; display: flex; align-items: center; justify-content: center; text-align: center; backdrop-filter: blur(10px);">Total Hours/Day</div>
                                    <div class="btn btn-primary rounded-pill px-4 py-2" style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; color: #E1E4E6; font-weight: bold; font-size: 20px; border-radius: 77px; width: 290px; display: flex; align-items: center; justify-content: center; text-align: center; backdrop-filter: blur(10px);">Statut</div>
                                    <div class="btn btn-primary rounded-pill px-4 py-2" style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; color: #E1E4E6; font-weight: bold; font-size: 20px; border-radius: 77px; width: 236px; display: flex; align-items: center; justify-content: center; text-align: center; backdrop-filter: blur(10px);">Details & Actions</div>
                                </div>


                                <div   style="display: flex; flex-direction: column; gap: 20px; max-width: 100%;height: 280px; overflow-y: auto; position: relative; padding-right: 10px; ">

                                @foreach ($teletravailleurs as $utilisateur)
                                    @if ($utilisateur->teletravailleur)
                                        @php
                                            $workingHours = $utilisateur->teletravailleur->workingHours->filter(function($session) {
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

                                        <div style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; padding: 15px; display: flex; flex-direction: row; align-items: center; border: 1px solid #444; height: 80px; width: 1060px; justify-content: space-between; gap: 10px; backdrop-filter: blur(10px); ">

                                            <div style="display: flex; align-items: center; flex: 1; margin-left: 5px; margin-right: 10px; font-weight: bold ;">
                                                <img src="{{ $utilisateur->teletravailleur->photoProfil && strpos($utilisateur->teletravailleur->photoProfil, 'images/') === 0 ? asset($utilisateur->teletravailleur->photoProfil) : ($utilisateur->teletravailleur->photoProfil ? asset('storage/' . $utilisateur->teletravailleur->photoProfil) : asset('images/default-profile.png')) }}" alt="Profile Photo" class="rounded-circle mr-2" style="width: 46px; height: 44px; border-radius: 77px;" onerror="this.src='{{ asset('images/default-profile.png') }}';">
                                                <span class="text-m font-black font-['Inter'] text-zinc-200">{{ $utilisateur->nom ?? 'N/A' }} {{ $utilisateur->prenom ?? '' }}</span>
                                            </div>

                                            <div class="text-xl font-bold font-['Inter'] text-zinc-200" style="flex: 1; text-align: center; margin-left: 20px; margin-right: 120px; font-weight: inter ;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $displayTime }}</div>

                                            <div class="d-flex align-items-center" style="justify-content: center; margin-left: 50px; margin-right: 125px; font-weight: inter; white-space: nowrap;">
                                                <div class="bg-{{ $utilisateur->isOnline() ? 'green' : 'red' }}-500 rounded-full w-4 h-4 mr-2" style="display: inline-block; vertical-align: middle;"></div>
                                                <span class="text-xl font-bold font-['Inter'] {{ $utilisateur->isOnline() ? 'text-green-500' : 'text-red-500' }}" style="display: inline-block; vertical-align: middle;">{{ $utilisateur->isOnline() ? 'Connected ' : 'Disconnected' }}</span>
                                            </div>

                                            <div class="d-flex align-items-center" style="justify-content: flex-end; margin-left: 30px; margin-right: 90px; white-space: nowrap;">
                                                <a href="{{ route('admin.teletravailleur.details', $utilisateur->id) }}" class="text-white" style="display: inline-block; vertical-align: middle; line-height: 20px; margin-right: 10px; margin-top: -8.5px; ">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <circle cx="12" cy="12" r="8" stroke-width="1.5"></circle>
                                                        <line x1="16.5" y1="16.5" x2="20" y2="20" stroke-width="1.5"></line>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.teletravailleur.destroy', $utilisateur->teletravailleur->id) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Are you sure you want to delete this remote worker ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <button type="submit" style="background: none; border: none; padding: 0; margin: 0; line-height: 20px; margin-right: -30px;">
                                                        <img src="{{ asset('images/delete.png') }}" alt="Delete Icon" style="width: 20px; height: 20px; vertical-align: middle;">
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
    .table th, .table td {
        vertical-align: middle;
    }

    .d-flex > .btn {
    flex-shrink: 0;
    white-space: nowrap;
    }
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
        margin-left: -180px;


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
</style>


@endsection
