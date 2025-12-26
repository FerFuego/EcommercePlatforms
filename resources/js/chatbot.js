document.addEventListener('DOMContentLoaded', () => {
    const chatbotButton = document.getElementById('chatbot-button');
    const chatbotWindow = document.getElementById('chatbot-window');
    const closeChatbot = document.getElementById('close-chatbot');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatbotInput = document.getElementById('chatbot-input');
    const chatbotMessages = document.getElementById('chatbot-messages');
    const typingIndicator = document.getElementById('typing-indicator');

    let messages = JSON.parse(localStorage.getItem('chatbot_messages')) || [];

    // Toggle Chatbot
    chatbotButton.addEventListener('click', () => {
        chatbotWindow.classList.toggle('open');
        scrollToBottom();
    });

    closeChatbot.addEventListener('click', () => {
        chatbotWindow.classList.remove('open');
    });

    // Load messages from localStorage
    if (messages.length > 0) {
        messages.forEach(msg => addMessageToUI(msg.role, msg.content, false));
    }

    // Handle Form Submit
    chatbotForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const text = chatbotInput.value.trim();
        if (!text) return;

        chatbotInput.value = '';
        addMessageToUI('user', text);

        messages.push({ role: 'user', content: text });
        saveMessages();

        // Show typing indicator
        typingIndicator.style.display = 'block';
        scrollToBottom();

        try {
            const response = await fetch('/api/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ messages: messages })
            });

            const data = await response.json();
            typingIndicator.style.display = 'none';

            if (data.message) {
                addMessageToUI('assistant', data.message);
                messages.push({ role: 'assistant', content: data.message });
                saveMessages();
            } else {
                addMessageToUI('assistant', 'Lo siento, hubo un error al procesar tu mensaje.');
            }
        } catch (error) {
            typingIndicator.style.display = 'none';
            addMessageToUI('assistant', 'Error de conexión. Por favor intenta de nuevo.');
            console.error('Chatbot Error:', error);
        }
    });

    function addMessageToUI(role, content, animate = true) {
        const div = document.createElement('div');
        div.className = `message ${role}`;

        if (role === 'assistant') {
            // Usamos marked para parsear markdown a HTML
            // Sanitización básica: marked ya hace algo de escapado, pero innerHTML requiere cuidado
            div.innerHTML = marked.parse(content);
        } else {
            div.textContent = content;
        }

        if (!animate) div.style.animation = 'none';
        chatbotMessages.appendChild(div);
        scrollToBottom();
    }

    function saveMessages() {
        // Keep only last 10 messages for context
        if (messages.length > 20) messages = messages.slice(-20);
        localStorage.setItem('chatbot_messages', JSON.stringify(messages));
    }

    function scrollToBottom() {
        chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
    }
});
