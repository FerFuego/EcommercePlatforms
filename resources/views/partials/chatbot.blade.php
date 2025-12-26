<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<div id="chatbot-container">
    <div id="chatbot-window">
        <div class="chatbot-header">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div
                    style="width: 32px; height: 32px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 0.95rem; font-weight: 600;">Asistente Virtual</h4>
                    <span style="font-size: 0.7rem; opacity: 0.8;">En línea ahora</span>
                </div>
            </div>
            <button id="close-chatbot"
                style="background: none; border: none; color: white; cursor: pointer; padding: 5px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="chatbot-messages" id="chatbot-messages">
            <div class="message assistant">
                Hola! Soy tu asistente virtual. ¿En qué puedo ayudarte hoy?
            </div>
        </div>

        <div id="typing-indicator" style="display: none;">
            <div class="typing-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>

        <form class="chatbot-input" id="chatbot-form">
            <input type="text" id="chatbot-input" placeholder="Escribe tu mensaje..." autocomplete="off">
            <button type="submit">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"></line>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                </svg>
            </button>
        </form>
    </div>

    <div id="chatbot-button">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
    </div>
</div>