<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #0f2027;
        }

        /* HEADER */
        .header {
            background: #111;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 18px;
        }

        /* CHAT */
        #chat-box {
            height: 380px;
            overflow-y: auto;
            padding: 10px;
        }

        /* MSG */
        .msg {
            display: flex;
            margin: 6px 0;
        }

        .user-msg {
            justify-content: flex-end;
        }

        .bot-msg {
            justify-content: flex-start;
        }

        .bubble {
            padding: 10px 14px;
            border-radius: 15px;
            max-width: 70%;
        }

        /* COLORS */
        .user-msg .bubble {
            background: #00c6ff;
            color: #fff;
        }

        .bot-msg .bubble {
            background: #eee;
        }

        /* INPUT */
        .input-area {
            display: flex;
            padding: 8px;
            background: #222;
        }

        input {
            flex: 1;
            padding: 10px;
            border: none;
        }

        .send-btn {
            background: #00c6ff;
            border: none;
            padding: 10px;
            color: white;
        }

        /* CLEAR */
        .clear-btn {
            width: 100%;
            background: red;
            color: white;
            padding: 8px;
            border: none;
        }
    </style>
</head>

<body>

    <div class="header">🤖 ChatBot</div>

    <div id="chat-box"></div>

    <button class="clear-btn" onclick="clearChat()">Clear Chat</button>

    <div class="input-area">
        <input type="text" id="msg" placeholder="Type message..." onkeypress="if(event.key==='Enter')sendMsg()">
        <button class="send-btn" onclick="sendMsg()">Send</button>
    </div>

    <script>

        /* LOAD */
        function loadChat() {
            fetch("load_chat.php")
                .then(res => res.json())
                .then(data => {
                    let chat = document.getElementById("chat-box");
                    chat.innerHTML = "";

                    data.forEach(row => {
                        chat.innerHTML += `<div class="msg user-msg"><div class="bubble">${row.message}</div></div>`;
                        chat.innerHTML += `<div class="msg bot-msg"><div class="bubble">${row.reply}</div></div>`;
                    });

                    chat.scrollTop = chat.scrollHeight;
                });
        }

        window.onload = loadChat;

        /* SEND */
        function sendMsg() {
            let msg = document.getElementById("msg").value;
            if (msg.trim() == "") return;

            let chat = document.getElementById("chat-box");

            chat.innerHTML += `<div class="msg user-msg"><div class="bubble">${msg}</div></div>`;
            chat.innerHTML += `<div class="msg bot-msg" id="typing"><div class="bubble">Typing...</div></div>`;

            fetch("chatbot.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ message: msg })
            })
                .then(res => res.json())
                .then(data => {
                    document.getElementById("typing").remove();
                    chat.innerHTML += `<div class="msg bot-msg"><div class="bubble">${data.reply}</div></div>`;
                    chat.scrollTop = chat.scrollHeight;
                });

            document.getElementById("msg").value = "";
        }

        /* CLEAR */
        function clearChat() {
            fetch("chatbot_delete.php")
                .then(() => {
                    document.getElementById("chat-box").innerHTML = "";
                });
        }

    </script>

</body>

</html>