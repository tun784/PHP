<?php
session_start();

// Include the database connection file
require_once 'db.php';

// Check if the user is logged in and user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=please_login");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id'];

    // Validate the message
    if (empty($message)) {
        header("Location: contact.php?error=empty_message");
        exit();
    }

    // Escape the message to prevent SQL injection
    $message = $conn->real_escape_string($message);

    // Prepare and execute the SQL query to insert feedback
    $query = "INSERT INTO feedback (feedback_text, user_id) VALUES ('$message', $user_id)";
    if ($conn->query($query) === TRUE) {
        // Redirect back to contact.php with a success message
        header("Location: contact.php?success=feedback_submitted");
        exit();
    } else {
        // Handle database errors
        header("Location: contact.php?error=database_error");
        exit();
    }
} else {
    // Redirect if the request method is not POST
    header("Location: contact.php?error=invalid_request");
    exit();
}
?>