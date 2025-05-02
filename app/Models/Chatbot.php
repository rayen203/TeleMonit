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
        return $this->belongsTo(Teletravailleur::class, 'teletravailleur_id'); // Relation avec Teletravailleur
    }

    public function repondreQuestion($question)
    {
        // Liste statique des FAQs (peut être remplacée par une table faqs)
        $faqs = [
            ['question' => 'combien de jours de congé ai-je par an ?', 'answer' => 'Vous avez droit à 30 jours de congé par an.'],
            ['question' => 'comment demander un congé ?', 'answer' => 'Vous pouvez demander un congé via le portail RH en ligne.'],
            ['question' => 'qui contacter pour des questions rh ?', 'answer' => 'Veuillez contacter le département RH à rh@societe.com.'],
        ];

        $question = strtolower(trim($question));
        $answer = 'Désolé, je n’ai pas de réponse à cette question. Veuillez contacter le support pour plus d’informations.';

        // Recherche de correspondance dans les FAQs
        foreach ($faqs as $faq) {
            if (str_contains(strtolower($faq['question']), $question)) {
                $answer = $faq['answer'];
                break;
            }
        }

        // Ajouter la question et la réponse à l'historique
        $historique = $this->historique ?? [];
        $historique[] = [
            'question' => $question,
            'answer' => $answer,
            'timestamp' => now()->toDateTimeString(),
        ];

        // Mettre à jour l'historique dans la base de données
        $this->historique = $historique;
        $this->save();

        return $answer;
    }
}
