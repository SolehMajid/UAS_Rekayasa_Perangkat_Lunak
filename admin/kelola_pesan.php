<?php
session_start();
require_once '../config/app.php';
require_once '../config/database.php';

$active_page = 'chat';

// Pastikan user sudah login sebagai admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin' || !isset($_SESSION['id_admin'])) {
    header("Location: ../customers/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Squashy - Kelola Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700;900&family=Nunito:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --mint-bg: #8DE3C7;
            --cream-bg: #FFEAA7;
            --dark-purple: #3A3063;
            --orange-card: #F2994A;
            --green-card: #27AE60;
            --soft-green: #A8E6CF;
            --white: #FFFFFF;
            --pink: #FF6FB7;
            --yellow: #FFD93D;
            --blue: #4DC8F0;
            --light-cream: #FFFDF9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--cream-bg);
            color: var(--dark-purple);
            min-height: 100vh;
            display: flex;
        }

        /* ── MAIN CONTENT AREA ── */
        .main-content {
            margin-left: 260px;
            flex: 1;
            padding: 40px;
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header-dash {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header-dash h1 {
            font-family: 'Nunito', sans-serif;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 0.5px;
        }

        /* ── CHAT SPLIT LAYOUT ── */
        .chat-layout {
            display: flex;
            gap: 25px;
            flex: 1;
            height: calc(100vh - 170px);
            min-height: 500px;
        }

        /* ── LEFT COLUMN: CUSTOMER LIST ── */
        .customer-list-panel {
            width: 320px;
            background: var(--white);
            border-radius: 25px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 3px solid var(--white);
        }

        .panel-title {
            font-family: 'Nunito', sans-serif;
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #E5E5E5;
        }

        .customer-list-scroll {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-right: 5px;
        }

        .customer-list-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .customer-list-scroll::-webkit-scrollbar-track {
            background: #F5F5F5;
            border-radius: 10px;
        }
        .customer-list-scroll::-webkit-scrollbar-thumb {
            background: var(--mint-bg);
            border-radius: 10px;
        }

        .customer-strip {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 15px;
            border-radius: 18px;
            background: #FDFDFD;
            border: 2px solid #F0F0F0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .customer-strip:hover {
            transform: translateY(-2px);
            background: var(--light-cream);
            border-color: var(--mint-bg);
        }

        .customer-strip.active {
            background: #EBFDF8;
            border-color: var(--mint-bg);
            box-shadow: 0 4px 10px rgba(141, 227, 199, 0.15);
        }

        .customer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            font-size: 16px;
            color: var(--white);
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(0,0,0,0.06);
        }

        .customer-info {
            flex: 1;
            min-width: 0;
        }

        .customer-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3px;
        }

        .customer-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--dark-purple);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .customer-time {
            font-size: 10px;
            color: #AAA;
            font-weight: 600;
            flex-shrink: 0;
        }

        .customer-last-msg {
            font-size: 12px;
            color: #666;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── RIGHT COLUMN: CHAT ROOM ── */
        .chat-room-panel {
            flex: 1;
            background: var(--white);
            border-radius: 25px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 3px solid var(--white);
            position: relative;
        }

        /* Welcome Screen */
        .welcome-screen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--white);
            z-index: 10;
            text-align: center;
            padding: 30px;
        }

        .welcome-icon {
            font-size: 64px;
            margin-bottom: 15px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .welcome-screen h3 {
            font-family: 'Nunito', sans-serif;
            font-size: 20px;
            font-weight: 900;
            margin-bottom: 8px;
        }

        .welcome-screen p {
            font-size: 14px;
            color: #777;
            font-weight: 700;
        }

        /* Active Chat Room */
        .chat-room-header {
            padding: 15px 25px;
            background: #FFEAA7;
            border-bottom: 2px dashed rgba(58, 48, 99, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .active-user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .active-user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--pink);
            color: white;
            font-family: 'Nunito', sans-serif;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .active-user-name {
            font-family: 'Nunito', sans-serif;
            font-size: 16px;
            font-weight: 900;
        }

        .active-user-email {
            font-size: 12px;
            color: #555;
            font-weight: 700;
        }

        .chat-room-body {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: #FFFBF7;
        }

        .chat-room-body::-webkit-scrollbar {
            width: 6px;
        }
        .chat-room-body::-webkit-scrollbar-track {
            background: #F5F5F5;
            border-radius: 10px;
        }
        .chat-room-body::-webkit-scrollbar-thumb {
            background: var(--mint-bg);
            border-radius: 10px;
        }

        .message {
            max-width: 75%;
            padding: 12px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.5;
            position: relative;
            animation: slideUp 0.25s ease;
            display: flex;
            flex-direction: column;
        }

        @keyframes slideUp {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .message.customer-msg {
            background-color: var(--blue);
            color: white;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            box-shadow: 0 4px 10px rgba(77, 200, 240, 0.15);
        }

        .message.bot-msg {
            background-color: #FFF2CC;
            color: var(--dark-purple);
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            border: 2px solid #FFE599;
        }

        .message.admin-msg {
            background-color: var(--mint-bg);
            color: var(--dark-purple);
            align-self: flex-end;
            border-bottom-right-radius: 5px;
            box-shadow: 0 4px 10px rgba(141, 227, 199, 0.15);
        }

        .msg-sender {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .customer-msg .msg-sender { color: #FFF; }
        .bot-msg .msg-sender { color: var(--orange-card); }
        .admin-msg .msg-sender { color: var(--pink); }

        .msg-text {
            word-break: break-word;
        }

        .msg-time {
            font-size: 9px;
            color: #888;
            align-self: flex-end;
            font-weight: 600;
            margin-top: 4px;
        }

        .customer-msg .msg-time {
            color: rgba(255, 255, 255, 0.85);
        }

        .admin-msg .msg-time {
            color: rgba(58, 48, 99, 0.6);
        }

        .chat-room-footer {
            padding: 15px 25px;
            background: var(--white);
            border-top: 2px dashed rgba(58, 48, 99, 0.1);
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .chat-room-footer textarea {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid #F0F0F0;
            border-radius: 18px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 700;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            color: var(--dark-purple);
            resize: none;
            height: 48px;
            line-height: 1.5;
        }

        .chat-room-footer textarea:focus {
            border-color: var(--mint-bg);
            box-shadow: 0 0 10px rgba(141, 227, 199, 0.2);
        }

        .send-reply-btn {
            background-color: var(--mint-bg);
            color: var(--dark-purple);
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
            box-shadow: 0 4px 10px rgba(141, 227, 199, 0.2);
            font-weight: 800;
        }

        .send-reply-btn:hover {
            transform: scale(1.1);
            background-color: #7bd3b7;
        }

        .send-reply-btn:active {
            transform: scale(0.95);
        }

        .decor-flower-bottom {
            position: fixed;
            bottom: 0;
            right: 0;
            height: 90px;
            pointer-events: none;
            z-index: 10;
        }
    </style>
</head>
<body>

    <?php require_once '../components/layout/header_admin.php'; ?>

    <div class="main-content">
        <div class="header-dash">
            <h1>KELOLA CHAT CUSTOMER</h1>
        </div>

        <div class="chat-layout">
            <!-- LEFT PANEL: CUSTOMERS -->
            <div class="customer-list-panel">
                <div class="panel-title">💬 Obrolan Masuk</div>
                <div class="customer-list-scroll" id="customerListScroll">
                    <p style="text-align:center; color:#888; margin-top:20px; font-size:13px; font-weight:700;">Memuat customer... 🐰</p>
                </div>
            </div>

            <!-- RIGHT PANEL: CHAT ROOM -->
            <div class="chat-room-panel">
                <!-- Welcome Overlay -->
                <div class="welcome-screen" id="welcomeScreen">
                    <div class="welcome-icon">💬</div>
                    <h3>Halo, Admin <?= htmlspecialchars($_SESSION['nama']) ?>!</h3>
                    <p>Silakan pilih salah satu customer di sebelah kiri untuk mulai membalas pesan.</p>
                </div>

                <!-- Chat Room Active UI -->
                <div class="chat-room-header">
                    <div class="active-user-profile">
                        <div class="active-user-avatar" id="activeUserAvatar">C</div>
                        <div>
                            <div class="active-user-name" id="activeUserName">Customer Name</div>
                            <div class="active-user-email" id="activeUserEmail">customer@example.com</div>
                        </div>
                    </div>
                </div>

                <div class="chat-room-body" id="chatRoomBody">
                    <!-- Dynamic chat messages go here -->
                </div>

                <div class="chat-room-footer">
                    <textarea id="replyTextarea" placeholder="Tulis balasan Admin..." onkeydown="if(event.key==='Enter' && !event.shiftKey) { event.preventDefault(); sendReplyMessage(); }"></textarea>
                    <button class="send-reply-btn" onclick="sendReplyMessage()">➤</button>
                </div>
            </div>
        </div>
    </div>

    <img src="../assets/images/decor-right.png" class="decor-flower-bottom">

    <script>
        let selectedUserId = null;
        let loadedMsgIds = new Set();
        let avatarColors = [
            '#FF6FB7', // pink
            '#FF852D', // orange
            '#4DC8F0', // blue
            '#6EDB8F', // green
            '#9B5DE5', // purple
            '#F15BB5'  // magenta
        ];

        function getAvatarColor(name) {
            let code = name.charCodeAt(0) || 0;
            return avatarColors[code % avatarColors.length];
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Ambil list customer yang melakukan chat
        async function fetchCustomerList() {
            try {
                const response = await fetch('ajax_pesan_admin.php?action=get_customers');
                const data = await response.json();
                
                if (data.status === 'success') {
                    const scrollContainer = document.getElementById('customerListScroll');
                    
                    if (data.customers.length === 0) {
                        scrollContainer.innerHTML = `<p style="text-align:center; color:#888; margin-top:20px; font-size:13px; font-weight:700;">Belum ada riwayat chat.</p>`;
                        return;
                    }

                    // Simpan scroll position jika ada
                    const scrollPos = scrollContainer.scrollTop;

                    let htmlContent = '';
                    data.customers.forEach(cust => {
                        const avatarChar = cust.nama_lengkap.charAt(0).toUpperCase();
                        const bgColor = getAvatarColor(cust.nama_lengkap);
                        const activeClass = selectedUserId === cust.id_user ? 'active' : '';
                        
                        htmlContent += `
                            <div class="customer-strip ${activeClass}" onclick="selectCustomer(${cust.id_user}, '${cust.nama_lengkap.replace(/'/g, "\\'")}', '${cust.email}')">
                                <div class="customer-avatar" style="background-color: ${bgColor};">${avatarChar}</div>
                                <div class="customer-info">
                                    <div class="customer-header-row">
                                        <span class="customer-name">${escapeHtml(cust.nama_lengkap)}</span>
                                        <span class="customer-time">${cust.last_time}</span>
                                    </div>
                                    <div class="customer-last-msg">${escapeHtml(cust.last_message)}</div>
                                </div>
                            </div>
                        `;
                    });

                    scrollContainer.innerHTML = htmlContent;
                    scrollContainer.scrollTop = scrollPos;
                }
            } catch (error) {
                console.error("Gagal memuat daftar customer:", error);
            }
        }

        // Pilih customer dari daftar kiri
        function selectCustomer(id, name, email) {
            if (selectedUserId === id) return;
            
            selectedUserId = id;
            loadedMsgIds.clear();
            
            // Perbarui UI Header
            document.getElementById('activeUserName').innerText = name;
            document.getElementById('activeUserEmail').innerText = email;
            
            const avatarChar = name.charAt(0).toUpperCase();
            const avatarColor = getAvatarColor(name);
            const avatarDiv = document.getElementById('activeUserAvatar');
            avatarDiv.innerText = avatarChar;
            avatarDiv.style.backgroundColor = avatarColor;

            // Bersihkan pesan lama
            document.getElementById('chatRoomBody').innerHTML = '';
            
            // Sembunyikan welcome screen
            document.getElementById('welcomeScreen').style.display = 'none';

            // Muat daftar customer untuk menandai strip aktif
            fetchCustomerList();

            // Ambil percakapan aktif
            fetchConversation();
        }

        // Ambil riwayat chat obrolan aktif
        async function fetchConversation() {
            if (!selectedUserId) return;
            try {
                const response = await fetch(`ajax_pesan_admin.php?action=get_chat&id_user=${selectedUserId}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    const chatBody = document.getElementById('chatRoomBody');
                    let hasNew = false;

                    data.messages.forEach(msg => {
                        if (!loadedMsgIds.has(msg.id_chat)) {
                            loadedMsgIds.add(msg.id_chat);
                            
                            const msgDiv = document.createElement('div');
                            
                            if (msg.pengirim === 'customer') {
                                msgDiv.className = 'message customer-msg';
                                msgDiv.innerHTML = `
                                    <span class="msg-sender">👤 Customer</span>
                                    <span class="msg-text">${escapeHtml(msg.isi_chat)}</span>
                                    <span class="msg-time">${msg.waktu}</span>
                                `;
                            } else if (msg.pengirim === 'bot') {
                                msgDiv.className = 'message bot-msg';
                                msgDiv.innerHTML = `
                                    <span class="msg-sender">🤖 Bot CS</span>
                                    <span class="msg-text">${escapeHtml(msg.isi_chat)}</span>
                                    <span class="msg-time">${msg.waktu}</span>
                                `;
                            } else { // admin
                                msgDiv.className = 'message admin-msg';
                                msgDiv.innerHTML = `
                                    <span class="msg-sender">👩‍💼 ${escapeHtml(msg.nama_admin)}</span>
                                    <span class="msg-text">${escapeHtml(msg.isi_chat)}</span>
                                    <span class="msg-time">${msg.waktu}</span>
                                `;
                            }
                            
                            chatBody.appendChild(msgDiv);
                            hasNew = true;
                        }
                    });

                    if (hasNew) {
                        chatBody.scrollTop = chatBody.scrollHeight;
                    }
                }
            } catch (error) {
                console.error("Gagal memuat percakapan:", error);
            }
        }

        // Kirim balasan chat admin
        async function sendReplyMessage() {
            if (!selectedUserId) return;
            
            const textarea = document.getElementById('replyTextarea');
            const message = textarea.value.trim();
            if (!message) return;

            textarea.value = '';

            try {
                const formData = new FormData();
                formData.append('id_user', selectedUserId);
                formData.append('message', message);

                const response = await fetch('ajax_pesan_admin.php?action=send_reply', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.status === 'success') {
                    // Muat percakapan & customer list dengan cepat
                    await fetchConversation();
                    await fetchCustomerList();
                } else {
                    alert("Gagal membalas chat: " + data.message);
                }
            } catch (error) {
                console.error("Gagal mengirim balasan:", error);
            }
        }

        // Setup Polling & Inisialisasi
        window.addEventListener('DOMContentLoaded', () => {
            fetchCustomerList();
            
            // Polling berkala (Customer List & Percakapan)
            setInterval(() => {
                fetchCustomerList();
                if (selectedUserId) {
                    fetchConversation();
                }
            }, 2000);
        });
    </script>
</body>
</html>
