<?php
// Start session to access session variables
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    echo "You are not logged in!";
    header('Location: 2login.php'); // Redirect to login page if not logged in
    exit();
}
if (isset($array['profile_photo'])) {
    // Code to handle the profile photo
    echo $array['profile_photo'];
} 


$email = $_SESSION['user_email']; // Get the logged-in user's email

// Database connection setup (combine database.php code here)
$host = 'localhost';  // Database host
$dbname = 'money_lend';  // Your database name
$username = 'root';  // MySQL default username (root)
$password = '';  // MySQL default password for root in XAMPP

// Create a new PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Enable exception mode for errors
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit();
}

// Fetch the user's details from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Fetch user details as an associative array
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
  

    // Check if user was found
    if (!$user) {
        echo "User not found.";
        exit();
    }

    // Extract user details

    $first_name = $user['first_name'];
    $last_name = $user['last_name'];
    $address = $user['address'];
    $phone = $user['phone'];
    $profile_photo = $user['profile_photo'];
   


    
} catch (PDOException $e) {
    echo "Error fetching user details: " . $e->getMessage();
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Lend</title>
    <link rel="stylesheet" href="dash.css">
</head>
<body>
    <div class="top-container">
        <div class="logo"><h3>Money Lend</h3></div>
    </div>

    <div class="down-container">

        <div class="left-section">
            <div class="dashboard"><h1>Dashboard</h1></div>

            <div class="profile-section">
            <img class="profile-photo" src="<?php echo htmlspecialchars($profile_photo); ?>" height="100px" width="100px" alt="Profile-Photo" >
            <h2 class="full-name"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></h2>
            <p class="email"><?php echo htmlspecialchars($email); ?></p>
            </div>


            <div class="personal-info">
              <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
              <p><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>
            </div>
            <form action="logout.php" method="POST" class="logout-form">
                <button type="submit" class="logout-btn">
                Logout  <img src="images/exit.png" alt="Logout Icon" class="logout-icon">
                </button>
            </form>
        </div>


        <div class="right-section">
            <div class="loan-status">
                <h2>We're processing your loan!</h2>
                <p>Your application is being reviewed by our team. We are excited to help you with your financial goals!</p>
                <p>Rest assured, you're on the right track! We'll notify you once the final decision is made.</p>
                <p><strong>Thank you for choosing us!</strong></p>
            </div>
        </div>
        
    </div>
      
</body>
</html>