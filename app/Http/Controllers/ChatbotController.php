<?php

namespace App\Http\Controllers;

use App\Models\Chatbot;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function showChat(Request $request)
    {
        \Log::info('ChatbotController@showChat appelé pour user_id: ' . Auth::id());
        return view('teletravailleur.chat');
    }

    public function getResponse(Request $request)
    {
        $question = $request->input('question');
        $teletravailleurId = auth()->user()->id;


        $chatbot = Chatbot::where('teletravailleur_id', $teletravailleurId)->first();


        if (!$chatbot) {
            $chatbot = Chatbot::create([
                'teletravailleur_id' => $teletravailleurId,
                'sessionId' => Str::uuid()->toString(),
                'historique' => [],
            ]);
        }


        if (empty($question) || trim($question) === '') {
            return response()->json([
                'success' => true,
                'answer' => null,
                'historique' => $chatbot->historique,
            ]);
        }


        $answer = $chatbot->repondreQuestion($question);

        return response()->json([
            'success' => true,
            'answer' => $answer,
            'historique' => $chatbot->historique,
        ]);
    }
    public function clearHistory(Request $request)
    {
        $teletravailleurId = auth()->user()->id;


        $chatbot = Chatbot::where('teletravailleur_id', $teletravailleurId)->first();

        if ($chatbot) {

            $chatbot->historique = [];
            $chatbot->save();

            return response()->json([
                'success' => true,
                'message' => 'Historique effacé avec succès.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Aucune session de chat trouvée.'
        ]);
    }
}
