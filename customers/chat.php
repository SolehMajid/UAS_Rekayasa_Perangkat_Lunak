<?php
session_start();
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

$theme = "mainan"; // Green theme for friendly CS chat
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Live Chat CS</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@800&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink: #FF6FB7;
            --yellow: #FFD93D;
            --blue: #4DC8F0;
            --green: #6EDB8F;
            --orange: #FF852D;
            --white: #FFFFFF;
            --dark: #3A3063;
            --theme-color: #A8D695;
        }

        body {
            background-color: var(--theme-color);
            background-image: url('../assets/images/tbg.png');
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Quicksand', sans-serif;
        }

        .chat-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .chat-card {
            width: 100%;
            max-width: 650px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 15px 35px rgba(58, 48, 99, 0.15);
            display: flex;
            flex-direction: column;
            height: 600px;
            overflow: hidden;
            border: 4px solid var(--white);
        }

        .chat-header {
            background: #FFEAA7;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px dashed rgba(58, 48, 99, 0.1);
        }

        .cs-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .cs-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--pink);
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .cs-info h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 18px;
            color: var(--dark);
            margin: 0;
        }

        .cs-status {
            font-size: 13px;
            color: #27AE60;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: #27AE60;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }

        .chat-body {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: #FFFBF7;
        }

        .message {
            max-width: 75%;
            padding: 14px 20px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 700;
            line-height: 1.5;
            position: relative;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(15px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .message.incoming {
            background-color: white;
            color: var(--dark);
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border: 2px solid #F0F0F0;
        }

        .message.outgoing {
            background-color: var(--pink);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
            box-shadow: 0 4px 12px rgba(255, 111, 183, 0.2);
        }

        /* Typing Indicator */
        .message.typing {
            background-color: white;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            border: 2px solid #F0F0F0;
            display: none;
            align-items: center;
            gap: 5px;
            padding: 12px 20px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: var(--dark);
            opacity: 0.4;
            border-radius: 50%;
            animation: typing 1s infinite alternate;
        }

        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing {
            from { transform: translateY(0); }
            to { transform: translateY(-5px); }
        }

        .chat-input-area {
            padding: 20px 25px;
            background: white;
            border-top: 2px dashed rgba(58, 48, 99, 0.1);
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .chat-input-area input {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid #F0F0F0;
            border-radius: 20px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
            color: var(--dark);
        }

        .chat-input-area input:focus {
            border-color: var(--pink);
            box-shadow: 0 0 10px rgba(255, 111, 183, 0.15);
        }

        .send-btn {
            background-color: var(--pink);
            color: white;
            border: none;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(255, 111, 183, 0.2);
        }

        .send-btn:hover {
            transform: scale(1.1);
            background-color: #e6559d;
        }

        .send-btn:active {
            transform: scale(0.95);
        }

        /* Quick Questions */
        .quick-options {
            display: flex;
            gap: 8px;
            padding: 10px 25px;
            background: #FFFBF7;
            flex-wrap: wrap;
            border-top: 1px solid #FFF5EB;
        }

        .quick-btn {
            background: white;
            border: 1px solid #FFD9D9;
            color: var(--pink);
            padding: 8px 14px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .quick-btn:hover {
            background: #FFF0F5;
            transform: translateY(-1px);
        }
    </style>
</head>
<body data-theme="<?= $theme ?>">

    <?= require_once __DIR__ . '/../components/layout/header.php' ?>

    <div class="chat-container">
        <div class="chat-card">
            <div class="chat-header">
                <div class="cs-profile">
                    <div class="cs-avatar">🤖</div>
                    <div class="cs-info">
                        <h3>Customer Service Squashy</h3>
                        <div class="cs-status">
                            <span class="status-dot"></span> Online
                        </div>
                    </div>
                </div>
            </div>

            <div class="chat-body" id="chatContainer">
                <div class="message incoming">
                    Halo Bunda/Ayah! 👋 Selamat datang di Squashy! Ada yang bisa kami bantu hari ini?
                </div>
                
                <!-- Typing indicator -->
                <div class="message typing" id="typingIndicator">
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                    <span class="typing-dot"></span>
                </div>
            </div>

            <div class="quick-options">
                <button class="quick-btn" onclick="sendQuickMessage('Apakah produk ini ready stok?')">📦 Cek Stok</button>
                <button class="quick-btn" onclick="sendQuickMessage('Bagaimana cara melakukan retur?')">🔄 Cara Retur</button>
                <button class="quick-btn" onclick="sendQuickMessage('Berapa biaya ongkir ke Jakarta?')">🚚 Info Ongkir</button>
                <button class="quick-btn" onclick="sendQuickMessage('Apakah ada promo hari ini?')">🎁 Info Promo</button>
            </div>

            <div class="chat-input-area">
                <input type="text" id="chatInputMessage" placeholder="Ketik pesan Bunda..." onkeydown="if(event.key==='Enter') sendMessage()" autocomplete="off">
                <button class="send-btn" onclick="sendMessage()">➤</button>
            </div>
        </div>
    </div>

    <script>
        const autoReplies = [
            "Terima kasih atas pertanyaannya! Tim admin kami akan segera membalas pesan Bunda/Ayah dalam beberapa menit ya 😊",
            "Tentu! Semua produk Squashy terbuat dari bahan premium yang 100% aman dan sangat nyaman digunakan si kecil 🌟",
            "Tenang saja Bun, Squashy menyediakan garansi retur 7 hari jika produk tidak pas atau rusak. Uang kembali penuh! 🔄",
            "Squashy juga punya promo Gratis Ongkir lho untuk minimal belanja Rp 100.000! Yuk buruan checkout 🚚",
            "Bunda bisa cek halaman Promo Spesial kami di Kategori untuk melihat diskon-diskon seru hingga 50%! 🎁"
        ];
        let replyIndex = 0;

        function scrollChatToBottom() {
            const chatContainer = document.getElementById('chatContainer');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function sendQuickMessage(text) {
            const inputField = document.getElementById('chatInputMessage');
            inputField.value = text;
            sendMessage();
        }

        function sendMessage() {
            const inputField = document.getElementById('chatInputMessage');
            const messageText = inputField.value.trim();
            if (!messageText) return;

            const chatContainer = document.getElementById('chatContainer');
            const typingIndicator = document.getElementById('typingIndicator');

            // 1. Add outgoing message
            const outgoingMsg = document.createElement('div');
            outgoingMsg.className = 'message outgoing';
            outgoingMsg.innerText = messageText;
            chatContainer.insertBefore(outgoingMsg, typingIndicator);
            inputField.value = '';
            scrollChatToBottom();

            // 2. Show typing indicator
            typingIndicator.style.display = 'flex';
            scrollChatToBottom();

            // 3. Simulate CS response
            setTimeout(() => {
                typingIndicator.style.display = 'none';
                
                const incomingMsg = document.createElement('div');
                incomingMsg.className = 'message incoming';
                incomingMsg.innerText = autoReplies[replyIndex % autoReplies.length];
                replyIndex++;
                
                chatContainer.insertBefore(incomingMsg, typingIndicator);
                scrollChatToBottom();
            }, 1000 + Math.random() * 800); // randomize response time slightly
        }

        // Initialize scroll position
        window.addEventListener('DOMContentLoaded', () => {
            scrollChatToBottom();
        });
    </script>
</body>
</html>
