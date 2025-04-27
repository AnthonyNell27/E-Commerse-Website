<?php
session_start();

$servername = "localhost";
$username = "antots1";
$password = "Antots@123";
$dbname = "login_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}


// ✅ Handle Reset Password Request
if ($_POST['action'] === "reset_password") {
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);

    if (empty($new_password)) {
        echo json_encode(["status" => "error", "message" => "Password cannot be empty"]);
        exit;
    }

    // Check if the email exists in users table
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Email not found"]);
        exit;
    }

    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Password has been reset successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Password reset failed"]);
    }
    exit;
}




if ($_POST['action'] === "signup") {
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit;
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (email, password, role, created_at) VALUES (?, ?, 'user', NOW())");
    $stmt->bind_param("ss", $email, $password);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id; // Auto-login after signup
        $_SESSION['user_role'] = 'user';
        echo json_encode(["status" => "success", "message" => "Account created"]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Signup failed"]);
        exit;
    }
}  

if ($_POST['action'] === "login") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Trim spaces

    // Check user exists
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "No user found with that email"]);
        exit;
    }

    $stmt->bind_result($user_id, $hashed_password, $role);
    $stmt->fetch();

    // Debugging: Log entered password and stored hash
    error_log("Entered Password: " . $password);
    error_log("Stored Hash: " . $hashed_password);

    // Verify password correctly
    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_role'] = $role;

        // Log the login time
        $stmt = $conn->prepare("INSERT INTO login_history (user_id, login_time) VALUES (?, NOW())");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        echo json_encode(["status" => "success", "role" => $role]);
    } else {
        echo json_encode(["status" => "error", "message" => "Incorrect password"]);
    }
    exit;
}



// ✅ Handle Logout
if ($_POST['action'] === "logout") {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // ✅ Update logout time
        $stmt = $conn->prepare("UPDATE login_history SET logout_time = NOW() WHERE user_id = ? AND logout_time IS NULL");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }

    session_start();
    session_unset();
    session_destroy();
    echo json_encode(["status" => "success"]);
    exit;
}


if ($_GET['action'] === "fetch_profile") {
    session_start(); 

    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email);
        $stmt->fetch();

        $defaultName = ucfirst(explode("@", $email)[0]);

        echo json_encode([
            "status" => "success",
            "full_name" => $defaultName,
            "email" => $email,
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Profile not found"]);
    }
    exit;
}




// ✅ Update Profile
if ($_POST['action'] === "update_profile") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);

    // Update profile in database
    $stmt = $conn->prepare("UPDATE profile_account SET full_name = ?, email = ?, phone_number = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $full_name, $email, $phone_number, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update profile"]);
    }
    exit;
}


$conn->close();
?>