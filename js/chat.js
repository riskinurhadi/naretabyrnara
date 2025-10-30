document.addEventListener('DOMContentLoaded', () => {
    const userInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');
    const chatBody = document.getElementById('chat-body');
    const chatToggle = document.getElementById('chat-toggle'); // Elemen tombol ikon
    const chatBox = document.getElementById('chat-box');       // Elemen container chat
    const closeChatButton = document.getElementById('close-chat'); // Tombol tutup di header

    // Event listener untuk tombol kirim pesan
    sendButton.addEventListener('click', sendMessage);
    userInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Event listener untuk tombol ikon chat (membuka/menutup)
    chatToggle.addEventListener('click', () => {
        chatBox.classList.toggle('hidden'); // Memastikan ini benar
        if (!chatBox.classList.contains('hidden')) {
            scrollToBottom();
            userInput.focus();
        }
    });

    // Event listener untuk tombol tutup chat
    closeChatButton.addEventListener('click', () => {
        chatBox.classList.add('hidden'); // Memastikan ini benar
    });


    function sendMessage() {
        const message = userInput.value.trim();
        if (message === '') return;

        appendMessage(message, 'user-message');
        userInput.value = '';
        scrollToBottom();

        fetch('php/chatbot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => {
            // Check if response is OK before trying to parse JSON
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            appendMessage(data.reply, 'bot-message');
            scrollToBottom();
        })
        .catch(error => {
            console.error('Error:', error);
            appendMessage('Maaf, Nareta sedang tidak bisa merespons. Silakan coba lagi nanti.', 'bot-message');
            scrollToBottom();
        });
    }

    function appendMessage(text, className) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', className);
        messageDiv.textContent = text;
        chatBody.appendChild(messageDiv);
    }

    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }
});