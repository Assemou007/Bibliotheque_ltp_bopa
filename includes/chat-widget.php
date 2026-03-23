<!-- Chat Widget -->
<div class="chat-widget" id="chatWidget" aria-label="Assistant virtuel">
    <div class="chat-header" id="chatHeader">
        <div class="chat-title">
            <span class="chat-icon"><i class="fas fa-robot"></i></span>
            <span>Assistant LTP-BOPA</span>
        </div>
        <button class="chat-toggle" id="chatToggle" aria-label="Ouvrir/Fermer le chat"><i class="fas fa-robot"></i></button>
    </div>
   
    <div class="chat-body" id="chatBody">
        <div class="chat-messages" id="chatMessages">
            <div class="message bot-message">
                <div class="message-avatar"><i class="fas fa-robot"></i></div>
                <div class="message-content">
                    Bonjour ! Je suis l'assistant virtuel du LTP-BOPA. Je peux vous aider à :
                    <ul>
                        <li>📚 Trouver des documents par filière</li>
                        <li>🔍 Chercher des ressources spécifiques</li>
                        <li>❓ Répondre à vos questions</li>
                    </ul>
                    Comment puis-je vous aider aujourd'hui ?
                </div>
            </div>
        </div>
       
        <div class="chat-input-area">
            <div class="chat-suggestions">
                <button class="suggestion-btn" data-question="Quelles sont les filières ?">📋 Filières</button>
                <button class="suggestion-btn" data-question="Comment chercher un document ?">🔍 Recherche</button>
                <button class="suggestion-btn" data-question="Puis-je télécharger ?">📥 Téléchargement</button>
                <button class="suggestion-btn" data-question="Nouveaux documents">🆕 Nouveautés</button>
            </div>
           
            <div class="input-wrapper">
                <input type="text"
                       id="chatInput"
                       placeholder="Posez votre question..."
                       aria-label="Votre question"
                       autocomplete="off">
                <button id="chatSend" aria-label="Envoyer">📤</button>
            </div>
        </div>
    </div>
</div>

<!-- Templates pour les messages -->
<template id="botMessageTemplate">
    <div class="message bot-message">
        <div class="message-avatar"><i class="fas fa-robot"></i></div>
        <div class="message-content"></div>
    </div>
</template>

<template id="userMessageTemplate">
    <div class="message user-message">
        <div class="message-content"></div>
    </div>
</template>

<template id="typingTemplate">
    <div class="message bot-message typing-indicator">
        <div class="message-avatar"><i class="fas fa-robot"></i></div>
        <div class="message-content">
            <span>.</span><span>.</span><span>.</span>
        </div>
    </div>
</template>