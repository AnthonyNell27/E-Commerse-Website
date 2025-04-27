<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "antots1";
$password = "Antots@123";
$dbname = "chatbot_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$sql = "SELECT user_input FROM chatbot_responses";
$result = $conn->query($sql);

$questions = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $questions[] = $row["user_input"];
    }
}

echo json_encode(["status" => "success", "questions" => $questions]);

$conn->close();
?>
