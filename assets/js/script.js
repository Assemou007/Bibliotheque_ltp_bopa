// assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    initMobileMenu();
    initChat();
    initAutoComplete();
    initSmoothScroll();
});

// ============================================
// MENU MOBILE
// ============================================
function initMobileMenu() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
   
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
            const expanded = this.getAttribute('aria-expanded') === 'true' ? false : true;
            this.setAttribute('aria-expanded', expanded);
            navMenu.classList.toggle('active');
        });
       
        // Fermer le menu en cliquant en dehors
        document.addEventListener('click', function(event) {
            if (!menuToggle.contains(event.target) && !navMenu.contains(event.target)) {
                menuToggle.setAttribute('aria-expanded', 'false');
                navMenu.classList.remove('active');
            }
        });
    }
}

// ============================================
// CHAT WIDGET
// ============================================
function initChat() {
    const chatWidget = document.getElementById('chatWidget');
    if (!chatWidget) return;
   
    const chatHeader = document.getElementById('chatHeader');
    const chatBody = document.getElementById('chatBody');
    const chatToggle = document.getElementById('chatToggle');
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');
    const chatMessages = document.getElementById('chatMessages');
    const suggestionBtns = document.querySelectorAll('.suggestion-btn');
   
    // État du chat
    let isOpen = false;
   
    // Toggle chat window
    if (chatHeader) {
        chatHeader.addEventListener('click', function(e) {
            if (e.target !== chatToggle && !chatToggle.contains(e.target)) {
                toggleChat();
            }
        });
    }
   
    // Toggle button
    if (chatToggle) {
        chatToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleChat();
        });
    }
   
    function toggleChat() {
        isOpen = !isOpen;
        chatBody.classList.toggle('active', isOpen);
        chatToggle.textContent = isOpen ? '−' : '+';
        chatToggle.setAttribute('aria-expanded', isOpen);
       
        if (isOpen && chatInput) {
            chatInput.focus();
        }
    }
   
    // Send message function
    async function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;
       
        // Afficher le message de l'utilisateur
        addMessage(message, 'user');
       
        // Effacer l'input
        chatInput.value = '';
       
        // Afficher l'indicateur de frappe
        const typingIndicator = showTypingIndicator();
       
        try {
            const response = await fetch('api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ question: message })
            });
           
            const data = await response.json();
           
            // Enlever l'indicateur de frappe
            removeTypingIndicator(typingIndicator);
           
            // Afficher la réponse
            addMessage(data.reponse, 'bot');
           
        } catch (error) {
            console.error('Erreur chat:', error);
            removeTypingIndicator(typingIndicator);
            addMessage('Désolé, une erreur technique est survenue. Veuillez réessayer.', 'bot');
        }
    }
   
    // Envoyer avec le bouton
    if (chatSend) {
        chatSend.addEventListener('click', sendMessage);
    }
   
    // Envoyer avec Entrée
    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });
    }
   
    // Suggestions
    suggestionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const question = this.dataset.question || this.textContent;
            if (chatInput) {
                chatInput.value = question;
                chatInput.focus();
            }
        });
    });
   
    // Helper functions
    function addMessage(text, sender) {
        const template = document.getElementById(sender === 'bot' ? 'botMessageTemplate' : 'userMessageTemplate');
        if (!template) return;
       
        const clone = template.content.cloneNode(true);
        const messageContent = clone.querySelector('.message-content');
       
        // Formatage simple pour les listes
        if (text.includes('\n')) {
            const lines = text.split('\n');
            let html = '';
            lines.forEach(line => {
                if (line.startsWith('•') || line.startsWith('-')) {
                    html += `<li>${line.substring(1)}</li>`;
                } else if (line.startsWith('📋') || line.startsWith('💬') || line.startsWith('📧')) {
                    html += `<p class="suggestion-link">${line}</p>`;
                } else {
                    html += `<p>${line}</p>`;
                }
            });
            if (html.includes('<li>')) {
                html = `<ul>${html}</ul>`;
            }
            messageContent.innerHTML = html;
        } else {
            messageContent.textContent = text;
        }
       
        chatMessages.appendChild(clone);
        scrollToBottom();
    }
   
    function showTypingIndicator() {
        const template = document.getElementById('typingTemplate');
        if (!template) return null;
       
        const clone = template.content.cloneNode(true);
        chatMessages.appendChild(clone);
        scrollToBottom();
        return chatMessages.lastChild;
    }
   
    function removeTypingIndicator(indicator) {
        if (indicator && indicator.parentNode) {
            indicator.parentNode.removeChild(indicator);
        }
    }
   
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

// ============================================
// AUTO-COMPLÉTION RECHERCHE
// ============================================
function initAutoComplete() {
    const searchInputs = document.querySelectorAll('input[type="search"]');
   
    searchInputs.forEach(input => {
        let timeoutId;
       
        input.addEventListener('input', function() {
            clearTimeout(timeoutId);
           
            const query = this.value.trim();
            if (query.length < 2) {
                removeSuggestions(this);
                return;
            }
           
            timeoutId = setTimeout(() => {
                fetchSuggestions(this, query);
            }, 300);
        });
       
        // Fermer les suggestions en cliquant dehors
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target)) {
                removeSuggestions(input);
            }
        });
    });
   
    async function fetchSuggestions(input, query) {
        try {
            const response = await fetch(`api/recherche.php?q=${encodeURIComponent(query)}`);
            const suggestions = await response.json();
            displaySuggestions(input, suggestions);
        } catch (error) {
            console.error('Erreur suggestions:', error);
        }
    }
   
    function displaySuggestions(input, suggestions) {
        removeSuggestions(input);
       
        if (!suggestions || suggestions.length === 0) return;
       
        const container = document.createElement('div');
        container.className = 'autocomplete-suggestions';
       
        suggestions.forEach(sugg => {
            const item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.innerHTML = `
                <span class="suggestion-icon">${sugg.type === 'document' ? '📄' : '📚'}</span>
                <span class="suggestion-text">${sugg.text}</span>
            `;
           
            item.addEventListener('click', () => {
                input.value = sugg.text;
                removeSuggestions(input);
                // Soumettre le formulaire si c'est une recherche
                const form = input.closest('form');
                if (form) form.submit();
            });
           
            container.appendChild(item);
        });
       
        input.parentNode.appendChild(container);
    }
   
    function removeSuggestions(input) {
        const existing = input.parentNode.querySelector('.autocomplete-suggestions');
        if (existing) {
            existing.remove();
        }
    }
}

// ============================================
// SMOOTH SCROLL
// ============================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
           
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ============================================
// UTILITAIRES
// ============================================
function formatDate(dateString) {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('fr-FR', options);
}

function confirmAction(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir continuer ?');
}

const chatWidget = document.getElementById('chatWidget');
if (chatWidget) {
    let isDragging = false;
    let offsetX, offsetY;

    chatWidget.addEventListener('mousedown', (e) => {
        if (e.target.closest('.chat-button')) {
            isDragging = true;
            offsetX = e.clientX - chatWidget.getBoundingClientRect().left;
            offsetY = e.clientY - chatWidget.getBoundingClientRect().top;
            chatWidget.style.cursor = 'grabbing';
        }
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            e.preventDefault();
            const x = e.clientX - offsetX;
            const y = e.clientY - offsetY;
            chatWidget.style.left = x + 'px';
            chatWidget.style.top = y + 'px';
            chatWidget.style.right = 'auto';
            chatWidget.style.bottom = 'auto';
        }
    });

    document.addEventListener('mouseup', () => {
        isDragging = false;
        chatWidget.style.cursor = 'grab';
    });
}

// ========== NOTIFICATIONS ==========
const notificationContainer = document.createElement('div');
notificationContainer.className = 'notification-container';
document.body.appendChild(notificationContainer);

window.showNotification = function(title, message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
   
    const icons = {
        success: '✅',
        info: 'ℹ️',
        warning: '⚠️',
        error: '❌'
    };
   
    notification.innerHTML = `
        <div class="notification-icon">${icons[type] || '📢'}</div>
        <div class="notification-content">
            <div class="notification-title">${title}</div>
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close">&times;</button>
    `;
   
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.style.animation = 'fadeOut 0.2s forwards';
        setTimeout(() => notification.remove(), 200);
    });
   
    notificationContainer.appendChild(notification);
   
    if (duration > 0) {
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'fadeOut 0.2s forwards';
                setTimeout(() => notification.remove(), 200);
            }
        }, duration);
    }
};

// Exemples d'utilisation :
// showNotification('Bienvenue', 'Vous êtes connecté', 'success');
// showNotification('Téléchargement', 'Le fichier a été téléchargé', 'info');
