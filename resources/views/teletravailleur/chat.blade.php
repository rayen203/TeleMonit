@extends('layouts.base-interface')

@section('content')
<div class="d-flex flex-column align-items-center justify-content-center min-vh-100">



    <div style="display: flex; justify-content: center; margin-bottom: 70px; margin-top: -40px; ">
        <div style="background: rgba(0, 0, 0, 0.5); border: 1px solid #444; border-radius: 77px; width: 766px ; height: 55px; display: flex; gap: 20px; backdrop-filter: blur(10px);">

            <a href="{{ route('teletravailleur.dashboard') }}" style="color: white; text-decoration: none; display: flex; align-items: center; font-size: 16px; margin-left: 30px; ">
                <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back
            </a>

            <button onclick="clearChatHistory()" style="color: #D94141; background: none; border: none; display: flex; align-items: center; font-size: 16px; cursor: pointer; margin-left:480px; ">
                <i class="fas fa-trash-alt" style="margin-right: 8px;"></i> Remove History
            </button>
        </div>
    </div>


    <div id="chatbot-window" style="background: rgba(0, 0, 0, 0.5); border-radius: 77px; padding: 15px; border: 1px solid #444; width: 766px; height: 550px; overflow-y: auto; position: relative; backdrop-filter: blur(10px); margin-top: -55px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);">

        <div style="background: rgba(0, 0, 0, 0.5); color: white; text-align: center; padding: 15px; font-weight: 600; border-radius: 77px 77px 0 0;">
            Chatbot Assistance
        </div>


        <div id="chatbot-messages" style="height: 480px; overflow-y: auto; padding: 15px; background: transparent;">

            <div class="message bot-message mb-3">
                <div style="background: rgba(255, 255, 255, 0.1) !important; color: #D3D3D3 !important; padding: 10px; border-radius: 20px; display: inline-block;">
                    Hi! I'm your virtual assistant for HR-related questions (leave, HR policies, etc.). Feel free to ask me anything!
                </div>
            </div>
        </div>


        <div style="border-top: 1px solid #444; padding: 15px; display: flex; align-items: center; justify-content: center; position: sticky; bottom: 0; background: rgba(0, 0, 0, 0.5); border-radius: 0 0 77px 77px;">
            <div style="position: relative; width: 712px; height: 55px;">
                <input type="text" id="chatbot-input" placeholder="Ask your question..." class="p-2 border border-gray-500 rounded-pill text-white" style="background: rgba(255, 255, 255, 0.1); width: 100%; height: 100%; border-radius: 77px; padding-right: 40px;" onkeypress="if(event.key === 'Enter') sendMessage();">
                <img src="/images/ask.png" alt="Send" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); width: 25px; height: 25px; cursor: pointer;" onclick="sendMessage()">
            </div>
        </div>
    </div>
</div>


<style>
    #chatbot-window::-webkit-scrollbar,
    #chatbot-messages::-webkit-scrollbar {
        display: none;
    }
</style>


<script>

    document.addEventListener('DOMContentLoaded', function() {
        loadChatHistory();
    });


    async function loadChatHistory() {
        try {
            const response = await fetch('{{ route("teletravailleur.chatbot.response") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ question: '' })
            });

            const data = await response.json();
            const messagesContainer = document.getElementById('chatbot-messages');


            messagesContainer.innerHTML = `
                <div class="message bot-message" style="margin-bottom: 10px;">
                    <div style="background: rgba(255, 255, 255, 0.1) !important; color: #D3D3D3 !important; padding: 10px; border-radius: 20px; display: inline-block;">
                        Hi! I'm your virtual assistant for HR-related questions (leave, HR policies, etc.). Feel free to ask me anything!
                    </div>
                </div>
            `;


            if (data.historique && Array.isArray(data.historique)) {
                data.historique.forEach(message => {

                    const userMessage = document.createElement('div');
                    userMessage.className = 'message user-message';
                    userMessage.style.marginBottom = '10px';
                    userMessage.style.textAlign = 'right';
                    userMessage.innerHTML = `<div style="background: #007bff; color: white; padding: 10px; border-radius: 20px; display: inline-block;">${message.question}</div>`;
                    messagesContainer.appendChild(userMessage);


                    const botMessage = document.createElement('div');
                    botMessage.className = 'message bot-message';
                    botMessage.style.marginBottom = '10px';
                    botMessage.innerHTML = `<div style="background: rgba(255, 255, 255, 0.1) !important; color: #D3D3D3 !important; padding: 10px; border-radius: 20px; display: inline-block;">${message.answer}</div>`;
                    messagesContainer.appendChild(botMessage);
                });


                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        } catch (error) {
            console.error('Erreur lors du chargement de l\'historique du chat :', error);
        }
    }


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

                const messagesContainer = document.getElementById('chatbot-messages');
                messagesContainer.innerHTML = `
                    <div class="message bot-message" style="margin-bottom: 10px;">
                        <div style="background: rgba(255, 255, 255, 0.1) !important; color: #D3D3D3 !important; padding: 10px; border-radius: 20px; display: inline-block;">
                            Hi! I'm your virtual assistant for HR-related questions (leave, HR policies, etc.). Feel free to ask me anything!
                        </div>
                    </div>
                `;
                alert('History successfully deleted. !');
            } else {
                alert('Error while deleting history : ' + (data.message || 'Erreur inconnue.'));
            }
        } catch (error) {
            console.error('Error while deleting history :', error);
            alert('Error while deleting history. Please try again. ');
        }
    }


    async function sendMessage() {
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();

        if (!message) return;


        const messagesContainer = document.getElementById('chatbot-messages');
        const userMessage = document.createElement('div');
        userMessage.className = 'message user-message';
        userMessage.style.marginBottom = '10px';
        userMessage.style.textAlign = 'right';
        userMessage.innerHTML = `<div style="background: #007bff; color: white; padding: 10px; border-radius: 20px; display: inline-block;">${message}</div>`;
        messagesContainer.appendChild(userMessage);


        messagesContainer.scrollTop = messagesContainer.scrollHeight;


        input.value = '';


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


            const botMessage = document.createElement('div');
            botMessage.className = 'message bot-message';
            botMessage.style.marginBottom = '10px';
            botMessage.innerHTML = `<div style="background: rgba(255, 255, 255, 0.1) !important; color: #D3D3D3 !important; padding: 10px; border-radius: 20px; display: inline-block;">${data.answer}</div>`;
            messagesContainer.appendChild(botMessage);


            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } catch (error) {
            console.error('Erreur lors de l\'appel API du chatbot :', error);
            const errorMessage = document.createElement('div');
            errorMessage.className = 'message bot-message';
            errorMessage.style.marginBottom = '10px';
            errorMessage.innerHTML = `<div style="background: rgba(255, 255, 255, 0.1) !important; color: #D3D3D3 !important; padding: 10px; border-radius: 20px; display: inline-block;">Erreur lors de la récupération de la réponse. Veuillez réessayer.</div>`;
            messagesContainer.appendChild(errorMessage);

            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
</script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@endsection
