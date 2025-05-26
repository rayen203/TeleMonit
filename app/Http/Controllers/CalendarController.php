<?php

namespace App\Http\Controllers;

use App\Models\Calendar;

use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {

        $email = auth()->id();

        $user = \App\Models\Utilisateur::where('email', $email)->first();

        if (!$user) {
            \Log::error('Utilisateur non trouvé dans index', ['email' => $email]);
            return redirect()->route('login')->withErrors(['error' => 'Utilisateur non trouvé. Veuillez vous reconnecter.']);
        }


        $userId = $user->id;

        $calendars = Calendar::where('user_id', $userId)->get();
        \Log::info('Calendriers récupérés dans index', [
            'user_id' => $userId,
            'calendars' => $calendars->toArray(),
        ]);
        return view('calendars.index', compact('calendars'));
    }



    public function createTask($date)
    {
        return view('calendars.create-task', compact('date'));
    }


    public function storeTask(Request $request, $date): \Illuminate\Http\RedirectResponse
    {
        \Log::info('Début de storeTask', [
            'date' => $date,
            'request_data' => $request->all(),
        ]);


        $email = auth()->id();
        \Log::info('Valeur de auth()->id() dans storeTask', ['email' => $email]);
        $user = \App\Models\Utilisateur::where('email', $email)->first();
        if (!$user) {
            \Log::error('Utilisateur non trouvé dans storeTask', ['email' => $email]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Utilisateur non trouvé.']);
        }
        $userId = $user->id;
        \Log::info('Utilisateur trouvé, ID converti', ['user_id' => $userId]);

        if (!$userId) {
            \Log::error('Utilisateur non connecté', ['user_id' => $userId]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Utilisateur non connecté. Veuillez vous reconnecter.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
        ]);

        try {

            $calendarDate = \Carbon\Carbon::parse($date)->toDateString();


            \Log::info('Avant firstOrCreate', [
                'user_id' => $userId,
                'date' => $calendarDate,
            ]);


            $calendar = Calendar::firstOrCreate(
                ['user_id' => $userId, 'date' => $calendarDate],
                ['tacheList' => []]
            );


            \Log::info('Après firstOrCreate', [
                'calendar_id' => $calendar->id,
                'tacheList' => $calendar->tacheList,
            ]);


            $calendar->ajouterTache(
                $request->title,
                $request->description,
                $request->start_date,
                $request->deadline,
                'pending'
            );


            \Log::info('Après ajouterTache', [
                'calendar_id' => $calendar->id,
                'tacheList' => $calendar->tacheList,
            ]);

            return redirect()->route('calendars.index')->with('success', 'Tâche ajoutée avec succès !');
        } catch (\Exception $e) {
            \Log::error('Erreur dans storeTask', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'ajout de la tâche : ' . $e->getMessage()]);
        }
    }


    public function editTask($date, $taskId)
    {
        $email = auth()->id();
        \Log::info('Début de editTask', [
            'email' => $email,
            'date' => $date,
            'taskId' => $taskId,
        ]);

        $user = \App\Models\Utilisateur::where('email', $email)->first();

        if (!$user) {
            \Log::error('Utilisateur non trouvé dans editTask', ['email' => $email]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Utilisateur non trouvé. Veuillez vous reconnecter.']);
        }

        $userId = $user->id;


        $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');

        \Log::info('Recherche du calendrier dans editTask', [
            'user_id' => $userId,
            'date' => $formattedDate,
        ]);


        $allCalendars = Calendar::where('user_id', $userId)->get()->toArray();
        \Log::info('Tous les calendriers pour cet utilisateur', [
            'user_id' => $userId,
            'calendars' => $allCalendars,
        ]);

        try {

            $calendar = Calendar::where('user_id', $userId)
                ->whereRaw("DATE(date) = ?", [$formattedDate])
                ->firstOrFail();
            \Log::info('Calendrier trouvé dans editTask', [
                'calendar_id' => $calendar->id,
                'tacheList' => $calendar->tacheList,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Calendrier non trouvé dans editTask', [
                'user_id' => $userId,
                'date' => $formattedDate,
            ]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Calendrier non trouvé pour cette date.']);
        }

        $task = collect($calendar->tacheList)->firstWhere('id', $taskId);

        if (!$task) {
            \Log::error('Tâche non trouvée dans editTask', [
                'calendar_id' => $calendar->id,
                'task_id' => $taskId,
            ]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Tâche non trouvée.']);
        }

        \Log::info('Affichage de la page de modification', [
            'calendar_id' => $calendar->id,
            'task_id' => $taskId,
        ]);

        return view('calendars.edit-task', compact('calendar', 'task', 'formattedDate'));
    }

    public function updateTask(Request $request, $date, $taskId)
    {
        $email = auth()->id();
        \Log::info('Début de updateTask', [
            'email' => $email,
            'date' => $date,
            'taskId' => $taskId,
        ]);

        $user = \App\Models\Utilisateur::where('email', $email)->first();

        if (!$user) {
            \Log::error('Utilisateur non trouvé dans updateTask', ['email' => $email]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Utilisateur non trouvé. Veuillez vous reconnecter.']);
        }

        $userId = $user->id;


        $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
        \Log::info('Valeur de date dans updateTask', [
            'date' => $date,
            'formattedDate' => $formattedDate,
        ]);


        $allCalendars = Calendar::where('user_id', $userId)->get()->toArray();
        \Log::info('Tous les calendriers pour cet utilisateur dans updateTask', [
            'user_id' => $userId,
            'calendars' => $allCalendars,
        ]);

        try {

            $calendar = Calendar::where('user_id', $userId)
                ->whereRaw("DATE(date) = ?", [$formattedDate])
                ->firstOrFail();
            \Log::info('Calendrier trouvé dans updateTask', [
                'calendar_id' => $calendar->id,
                'tacheList' => $calendar->tacheList,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Calendrier non trouvé dans updateTask', [
                'user_id' => $userId,
                'date' => $formattedDate,
            ]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Calendrier non trouvé pour cette date.']);
        }

        $tasks = collect($calendar->tacheList);
        $taskIndex = $tasks->search(function ($task) use ($taskId) {
            return $task['id'] == $taskId;
        });

        if ($taskIndex === false) {
            \Log::error('Tâche non trouvée dans updateTask', [
                'calendar_id' => $calendar->id,
                'task_id' => $taskId,
            ]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Tâche non trouvée.']);
        }


        $tasks[$taskIndex] = [
            'id' => (int)$taskId,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'deadline' => $request->input('deadline'),
            'status' => $tasks[$taskIndex]['status'],
        ];

        $calendar->tacheList = $tasks->toArray();
        $calendar->save();

        \Log::info('Tâche mise à jour avec succès dans updateTask', [
            'calendar_id' => $calendar->id,
            'task_id' => $taskId,
        ]);

        return redirect()->route('calendars.index')->with('success', 'Tâche mise à jour avec succès !');
    }





    public function destroy($date): \Illuminate\Http\RedirectResponse
{
    \Log::info('Début de destroy', ['date' => $date]);

    try {
        $userId = auth()->id();
        $user = \App\Models\Utilisateur::where('email', $userId)->first();

        if (!$user) {
            \Log::error('Utilisateur non trouvé dans destroy', ['userId' => $userId]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Utilisateur non trouvé.']);
        }

        $formattedDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
        $calendar = Calendar::where('user_id', $user->id)
            ->whereRaw("DATE(date) = ?", [$formattedDate])
            ->first();

        if (!$calendar) {
            \Log::error('Calendrier non trouvé dans destroy', [
                'user_id' => $user->id,
                'date' => $formattedDate,
            ]);
            return redirect()->route('calendars.index')->withErrors(['error' => 'Calendrier non trouvé.']);
        }

        $calendar->delete();

        \Log::info('Calendrier supprimé dans destroy', [
            'calendar_id' => $calendar->id,
            'user_id' => $user->id,
            'date' => $formattedDate,
        ]);

        return redirect()->route('calendars.index')->with('success', 'Calendrier supprimé avec succès !');
    } catch (\Exception $e) {
        \Log::error('Erreur lors de la suppression dans destroy', [
            'error' => $e->getMessage(),
            'date' => $date,
        ]);
        return redirect()->route('calendars.index')->withErrors(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
    }
}
}
