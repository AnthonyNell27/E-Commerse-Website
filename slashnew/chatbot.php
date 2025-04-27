<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "antots1"; 
$password = "Antots@123"; 
$dbname = "chatbot_db";

session_start();
if (!isset($_SESSION['chatbot_session_id'])) {
    $_SESSION['chatbot_session_id'] = session_id();
}
$session_id = $_SESSION['chatbot_session_id'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["response" => "Database connection failed"]);
    exit;
}

// ✅ Fetch user session details
$stmt = $conn->prepare("SELECT customer_name, customer_contact, chat_state FROM user_sessions WHERE session_id = ?");
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

$chat_state = $user_data ? $user_data['chat_state'] : "ask_name";
$customer_name = $user_data['customer_name'] ?? null;
$customer_contact = $user_data['customer_contact'] ?? null;

$userMessage = trim($_POST['message'] ?? '');
$response = "";

// ✅ Check if the question exists in chatbot_responses
$stmt = $conn->prepare("SELECT bot_response FROM chatbot_responses WHERE LOWER(user_input) = LOWER(?)");
$stmt->bind_param("s", $userMessage);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response = $row['bot_response'];
} else {
    // ✅ Process chatbot flow logic only if no predefined response exists
    if ($chat_state === "ask_name") {
        if (empty($userMessage)) {
            $response = "Hello! What is your name?";
        } else {
            $stmt = $conn->prepare("UPDATE user_sessions SET customer_name = ?, chat_state = 'ask_contact' WHERE session_id = ?");
            $stmt->bind_param("ss", $userMessage, $session_id);
            $stmt->execute();
            $chat_state = "ask_contact";
            $response = "Nice to meet you, " . ucfirst($userMessage) . "! Please provide your email or phone number.";
        }
    } elseif ($chat_state === "ask_contact") {
        if (!filter_var($userMessage, FILTER_VALIDATE_EMAIL) && !preg_match("/^\+?\d{10,15}$/", $userMessage)) {
            $response = "That doesn't look like a valid email or phone number. Please enter a correct contact detail.";
        } else {
            $stmt = $conn->prepare("UPDATE user_sessions SET customer_contact = ?, chat_state = 'conversation' WHERE session_id = ?");
            $stmt->bind_param("ss", $userMessage, $session_id);
            $stmt->execute();
            $chat_state = "conversation";
            $response = "Thank you! How can I assist you today?";
        }
    } else {
        $response = "I'm not sure how to answer that yet. Can you ask something else?";
    }
}


echo json_encode(["response" => $response, "chat_state" => $chat_state]);

$conn->close();
?>
