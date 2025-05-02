@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chatbot Assistance</h2>

    <!-- Bouton Retour -->
    <a href="{{ route('teletravailleur.dashboard') }}" class="btn btn-secondary mb-3">Retour au Tableau de Bord</a>
    <!-- Bouton pour effacer l'historique -->
    <button onclick="clearChatHistory()" class="btn btn-danger mb-3">Effacer l'Historique</button>

    <!-- Chatbot Interface -->
    <div id="chatbot-window" style="width: 100%; max-width: 600px; height: 500px; background: #fff; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.3); margin: 20px auto; overflow: hidden;">
        <!-- En-tête du chatbot -->
        <div style="background: #007bff; color: white; padding: 10px; text-align: center; font-weight: bold;">
            Chatbot Assistance
        </div>

        <!-- Zone de conversation -->
        <div id="chatbot-messages" style="height: 390px; overflow-y: auto; padding: 10px; background: #f8f9fa;">
            <!-- Message de bienvenue -->
            <div class="message bot-message" style="margin-bottom: 10px;">
                <div style="background: #e9ecef; padding: 8px; border-radius: 10px; display: inline-block;">
                    Bonjour ! Je suis ici pour répondre à vos questions sur la société (congés, RH, etc.). Posez-moi une question !
                </div>
            </div>
        </div>

        <!-- Zone de saisie -->
        <div style="border-top: 1px solid #ddd; padding: 10px; display: flex; align-items: center;">
            <input type="text" id="chatbot-input" placeholder="Tapez votre question..." style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 20px; margin-right: 10px;" onkeypress="if(event.key === 'Enter') sendMessage();">
            <button onclick="sendMessage()" class="btn btn-primary rounded-circle" style="width: 40px; height: 40px;">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<!-- JavaScript pour gérer le Chatbot -->
<script>
    // Charger l'historique des conversations au démarrage
    document.addEventListener('DOMContentLoaded', function() {
        loadChatHistory();
    });

    // Charger l'historique des conversations
    async function loadChatHistory() {
        try {
            const response = await fetch('{{ route("teletravailleur.chatbot.response") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ question: '' }) // Question vide pour charger l'historique
            });

            const data = await response.json();
            const messagesContainer = document.getElementById('chatbot-messages');

            // Vider les messages existants sauf le message de bienvenue
            messagesContainer.innerHTML = `
                <div class="message bot-message" style="margin-bottom: 10px;">
                    <div style="background: #e9ecef; padding: 8px; border-radius: 10px; display: inline-block;">
                        Bonjour ! Je suis ici pour répondre à vos questions sur la société (congés, RH, etc.). Posez-moi une question !
                    </div>
                </div>
            `;

            // Ajouter les messages de l'historique
            if (data.historique && Array.isArray(data.historique)) {
                data.historique.forEach(message => {
                    // Ajouter le message de l'utilisateur
                    const userMessage = document.createElement('div');
                    userMessage.className = 'message user-message';
                    userMessage.style.marginBottom = '10px';
                    userMessage.style.textAlign = 'right';
                    userMessage.innerHTML = `<div style="background: #007bff; color: white; padding: 8px; border-radius: 10px; display: inline-block;">${message.question}</div>`;
                    messagesContainer.appendChild(userMessage);

                    // Ajouter la réponse du bot
                    const botMessage = document.createElement('div');
                    botMessage.className = 'message bot-message';
                    botMessage.style.marginBottom = '10px';
                    botMessage.innerHTML = `<div style="background: #e9ecef; padding: 8px; border-radius: 10px; display: inline-block;">${message.answer}</div>`;
                    messagesContainer.appendChild(botMessage);
                });

                // Faire défiler la conversation vers le bas
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        } catch (error) {
            console.error('Erreur lors du chargement de l\'historique du chat :', error);
        }
    }

    // Vider l'historique des conversations
    async function clearChatHistory() {
        try {
            const response = await fetch('{{ route("teletravailleur.teletravailleur.chatbot.clear") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                // Vider l'affichage sauf le message de bienvenue
                const messagesContainer = document.getElementById('chatbot-messages');
                messagesContainer.innerHTML = `
                    <div class="message bot-message" style="margin-bottom: 10px;">
                        <div style="background: #e9ecef; padding: 8px; border-radius: 10px; display: inline-block;">
                            Bonjour ! Je suis ici pour répondre à vos questions sur la société (congés, RH, etc.). Posez-moi une question !
                        </div>
                    </div>
                `;
                alert('Historique effacé avec succès !');
            } else {
                alert('Erreur lors de l\'effacement de l\'historique : ' + (data.message || 'Erreur inconnue.'));
            }
        } catch (error) {
            console.error('Erreur lors de l\'effacement de l\'historique :', error);
            alert('Erreur lors de l\'effacement de l\'historique. Veuillez réessayer.');
        }
    }

    // Envoyer un message et obtenir une réponse
    async function sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();

        if (!message) return;

        // Ajouter le message de l'utilisateur à la conversation
        const messagesContainer = document.getElementById('chatbot-messages');
        const userMessage = document.createElement('div');
        userMessage.className = 'message user-message';
        userMessage.style.marginBottom = '10px';
        userMessage.style.textAlign = 'right';
        userMessage.innerHTML = `<div style="background: #007bff; color: white; padding: 8px; border-radius: 10px; display: inline-block;">${message}</div>`;
        messagesContainer.appendChild(userMessage);

        // Faire défiler la conversation vers le bas
        messagesContainer.scrollTop = messagesContainer.scrollHeight;

        // Vider le champ de saisie
        input.value = '';

        // Envoyer la question au serveur via AJAX
        try {
            const response = await fetch('{{ route("teletravailleur.chatbot.response") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ question: message })
            });

            const data = await response.json();

            // Ajouter la réponse du bot à la conversation
            const botMessage = document.createElement('div');
            botMessage.className = 'message bot-message';
            botMessage.style.marginBottom = '10px';
            botMessage.innerHTML = `<div style="background: #e9ecef; padding: 8px; border-radius: 10px; display: inline-block;">${data.answer}</div>`;
            messagesContainer.appendChild(botMessage);

            // Faire défiler la conversation vers le bas
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } catch (error) {
            console.error('Erreur lors de l\'appel API du chatbot :', error);
            const errorMessage = document.createElement('div');
            errorMessage.className = 'message bot-message';
            errorMessage.style.marginBottom = '10px';
            errorMessage.innerHTML = `<div style="background: #e9ecef; padding: 8px; border-radius: 10px; display: inline-block;">Erreur lors de la récupération de la réponse. Veuillez réessayer.</div>`;
            messagesContainer.appendChild(errorMessage);

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
</script>

<!-- Inclusion de Font Awesome pour les icônes -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@endsection
