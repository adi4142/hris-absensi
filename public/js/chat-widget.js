// Toggle chatbot visibility
function toggleChat() {
    const widget = document.getElementById('chatWidget');
    widget.classList.toggle('minimized');
    if (!widget.classList.contains('minimized')) {
        document.getElementById('chatInput').focus();
    }
}

// Handle Enter key press
function handleKeyPress(e) {
    if (e.key === 'Enter') {
        sendChatMessage();
    }
}

// Quick reply button
function quickReply(message) {
    document.getElementById('chatInput').value = message;
    sendChatMessage();
}

// Send message to chatbot
async function sendChatMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    if (!message) return;

    // Append user message
    appendChatMessage('user', message);
    input.value = '';

    // Show typing indicator
    const typingId = 'typing-' + Date.now();
    appendChatMessage('bot', '<div class="typing-indicator"><span></span><span></span><span></span></div>', typingId);

    try {
        const response = await fetch('{{ route("admin.chat") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        });

        const data = await response.json();

        // Replace typing indicator with actual response
        const typingEl = document.getElementById(typingId);
        if (typingEl) {
            typingEl.innerHTML = data.reply;
        }
    } catch (error) {
        const typingEl = document.getElementById(typingId);
        if (typingEl) {
            typingEl.innerHTML = 'Maaf, terjadi kesalahan. Silakan coba lagi nanti.';
        }
    }
}

// Append message to chat body
function appendChatMessage(role, text, id = null) {
    const chatBody = document.getElementById('chatBody');
    const div = document.createElement('div');
    div.className = 'message ' + role;
    if (id) div.id = id;
    div.innerHTML = text;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}