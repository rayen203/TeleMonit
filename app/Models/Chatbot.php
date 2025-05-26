<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Chatbot extends Model
{
    use HasFactory;

    protected $table = 'chatbots';

    protected $fillable = [
        'teletravailleur_id',
        'sessionId',
        'historique',
    ];

    protected $casts = [
        'historique' => 'json',
    ];

    public function teletravailleur(): BelongsTo
    {
        return $this->belongsTo(Teletravailleur::class, 'teletravailleur_id');
    }

    public function repondreQuestion($question)
    {

        $faqs = [
            ['question' => 'How many vacation days do I have per year?', 'answer' => 'You are entitled to 30 vacation days per year.'],
            ['question' => 'How do I request a leave?', 'answer' => 'You can request a vacation through the online HR portal.'],
            ['question' => 'Who should I contact for HR-related questions?', 'answer' => 'Please contact the HR department at Contact@waydev.com.'],
        ];

        $question = strtolower(trim($question));
        $answer = 'Sorry, I don’t have an answer to that question. Please contact support for more information.';


        foreach ($faqs as $faq) {
            if (str_contains(strtolower($faq['question']), $question)) {
                $answer = $faq['answer'];
                break;
            }
        }


        $historique = $this->historique ?? [];
        $historique[] = [
            'question' => $question,
            'answer' => $answer,
            'timestamp' => now()->toDateTimeString(),
        ];


        $this->historique = $historique;
        $this->save();

        return $answer;
    }
}
