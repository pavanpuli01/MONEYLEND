<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "money_lend";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Make sure email is set in session after login
if (!isset($_SESSION['user_email'])) {
    echo "Session expired. Please login again.";
    exit();
}

// Get the user's email address (assumed to be in session after login)
$email = $_SESSION['user_email']; 

$max_failed_attempts = 3; // Max failed attempts before freeze
$block_duration = 2 * 60; // Freeze for 2 minutes (120 seconds)

// Debugging: Check if email is being retrieved correctly
echo "Email from session: " . $email . "<br>";

// Check for failed login attempts from the current email address
$query = "SELECT * FROM login_attempts WHERE email = '$email' ORDER BY attempt_time DESC LIMIT 1";
$result = $conn->query($query);

// Debugging: Check if query is returning results
if ($result === false) {
    echo "Query failed: " . $conn->error . "<br>";
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $failed_attempts = $row['failed_attempts'];
    $last_attempt_time = strtotime($row['attempt_time']); // Convert to timestamp

    // Debugging: Output failed attempts and last attempt time
    echo "Failed attempts: " . $failed_attempts . "<br>";

    // If the user has exceeded the max failed attempts and is within the block time
    if ($failed_attempts >= $max_failed_attempts && (time() - $last_attempt_time) < $block_duration) {
        $blocked_time = $block_duration - (time() - $last_attempt_time); // Time left for the block to expire
        echo "<div style='color: red;'>Too many failed login attempts. Please try again after 5 minutes.</div>";
        exit(); // Stop further execution
    }
} else {
    // No previous failed login attempts found, so the user is not blocked
    echo "No previous login attempts found.<br>";  // Debugging line
}

// If the user is not blocked, allow the login attempt
header('Location: 2login.php');
exit();
?>
