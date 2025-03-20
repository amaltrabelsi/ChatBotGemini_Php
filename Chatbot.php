<?php
$apiKey = "YourKey ******";
function getGeminiResponse($message, $apiKey) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $message]
                ]
            ]
        ]
    ];

    $jsonData = json_encode($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Vérifier si la requête vient d'AJAX
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["message"])) {
    $userMessage = trim($_POST["message"]);
    $response = getGeminiResponse($userMessage, $apiKey);

    $botResponse = "Je ne comprends pas.";
    if (isset($response["candidates"][0]["content"]["parts"][0]["text"])) {
        $botResponse = $response["candidates"][0]["content"]["parts"][0]["text"];
    }

    echo json_encode(["user" => $userMessage, "bot" => $botResponse]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Flottant</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        /* Bouton flottant */
        #chat-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background-color: #007bff;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            transition: background 0.3s;
        }

        #chat-button:hover {
            background-color: #0056b3;
        }

        /* Conteneur du chat flottant */
        #chat-container {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 350px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: none; /* Caché au début */
            flex-direction: column;
        }

        .chat-box {
            padding: 15px;
            height: 300px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .message {
            max-width: 70%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 10px;
            word-wrap: break-word;
        }

        .user-message {
            background: #007bff;
            color: white;
            align-self: flex-end;
        }

        .bot-message {
            background: #e9ecef;
            color: black;
            align-self: flex-start;
        }

        .input-box {
            display: flex;
            padding: 10px;
            background: #fff;
            border-top: 1px solid #ddd;
        }

        .input-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }

        .input-box button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .input-box button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <!-- Bouton flottant -->
    <div id="chat-button">💬</div>

    <!-- Conteneur du chat -->
    <div class="chat-container" id="chat-container">
        <div class="chat-box" id="chat-box"></div>
        <div class="input-box">
            <input type="text" id="user-message" placeholder="Écrivez un message...">
            <button id="send-btn">Envoyer</button>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Afficher/Masquer le chat
            $("#chat-button").click(function () {
                $("#chat-container").fadeToggle();
            });

            $("#send-btn").click(function () {
                sendMessage();
            });

            $("#user-message").keypress(function (e) {
                if (e.which == 13) { // Touche "Entrée"
                    sendMessage();
                }
            });

            function sendMessage() {
                var userMessage = $("#user-message").val().trim();
                if (userMessage === "") return;

                // Ajouter le message utilisateur à l'interface
                $("#chat-box").append('<div class="message user-message">' + userMessage + '</div>');
                $("#user-message").val("");
                $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);

                // Envoyer la requête AJAX au serveur
                $.ajax({
                    url: "",
                    type: "POST",
                    data: { message: userMessage },
                    dataType: "json",
                    success: function (response) {
                        // Ajouter la réponse du bot à l'interface
                        $("#chat-box").append('<div class="message bot-message">' + response.bot + '</div>');
                        $("#chat-box").scrollTop($("#chat-box")[0].scrollHeight);
                    }
                });
            }
        });
    </script>

</body>
</html>
