<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "money_lend";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error_message = "Invalid credentials!";

// Check if it's a login action
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_ip = $_POST['user_ip'];

 
    // Log the IP address and location using ipinfo.io
    $api_url = "https://ipinfo.io/$user_ip/json"; // Using ipinfo.io for IP geolocation

    // Use cURL to get the IP information
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        // Handle the error if any
        echo 'cURL error: ' . curl_error($ch);
    }
    curl_close($ch);

    // Decode JSON response from ipinfo.io
    $location_data = json_decode($response, true);

    // Check if location data is available and set defaults if not
    $city = $location_data['city'] ?? 'Unknown';
    $region = $location_data['region'] ?? 'Unknown';
    $country = $location_data['country'] ?? 'Unknown';

    // Validate login credentials
    $sql = "SELECT * FROM applications WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if email exists
    if ($result->num_rows > 0) {
        // Check password if email exists
        $user = $result->fetch_assoc();
        if ($user['password'] === $password) {
            // Successful login
            $_SESSION['user_email'] = $email;
            $login_status = 'successful';
            // Redirect to dashboard after successful logi
        
            header("Location: log_user2.php");
            exit();
        } else {
            // Invalid password
            $login_status = 'failed';
            $error_message = "Invalid credentials!";
                    // Log the login attempt with email, IP, location, and login status
            $sql_log = "INSERT INTO user_logins (email, ip_address, city, region, country, login_status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_log = $conn->prepare($sql_log);
            if ($stmt_log === false) {
                die("Error preparing statement: " . $conn->error);
            }

            $stmt_log->bind_param("ssssss", $email, $user_ip, $city, $region, $country, $login_status);
            $_SESSION['user_email'] = $email;
            // Invalid credentials, increment failed attempts or insert new record
                // Check if the email exists in the login_attempts table
                $query = "SELECT * FROM login_attempts WHERE email = '$email'";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    // If record exists, update failed attempts and attempt_time
                    $conn->query("UPDATE login_attempts SET failed_attempts = failed_attempts + 1, attempt_time = CURRENT_TIMESTAMP WHERE email = '$email'");
                } else {
                    // If no record, insert a new record for the email with 1 failed attempt
                    $conn->query("INSERT INTO login_attempts (email, failed_attempts, attempt_time) VALUES ('$email', 1, CURRENT_TIMESTAMP)");
                }




            
            header("Location: failed_logins.php");
            // Execute and check for success
            if (!$stmt_log->execute()) {
                echo "Error logging attempt: " . $stmt_log->error;
            }

            $stmt_log->close();
        }
    } else {
        // Email does not exist
        $login_status = 'failed';
        $error_message = "Invalid credentials!";
    }

    $stmt->close();

    
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Lend - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="left">
            <h1>Money Lend</h1>
            <ul>
                <li>Success Loans & Credit in Minutes</li>
                <li><img src="images/check.png" alt="Check" class="check-icon"> Loans up to ₹2,00,000 in 2 minutes</li>
                <li><img src="images/check.png" alt="Check" class="check-icon"> Available to use across India</li>
            </ul>
        </div>
        <div class="right">
            <h2>Welcome back!</h2>
            <h3>Please login to access your account.</h3>

            <!-- Login Form -->
            <form action="2login.php" method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <!-- Hidden input for IP address -->
                <input type="hidden" name="user_ip" value="<?php echo isset($_GET['user_ip']) ? $_GET['user_ip'] : ''; ?>">

                <button type="submit">Login</button>
            </form>

            <!-- Display error message if credentials are wrong -->
            <?php
            // Display error message if credentials are wrong
            if (isset($error_message)) {
                echo "<p style='color:red; text-align:center;'>$error_message</p>";
            }
            ?>

<p>Don't have an account? <a href="log_user1.php?create_account=true&user_ip=" id="create-account-link">Create</a></p>
</div>
    </div>
    <script>
        // Get the user's IP address using JavaScript
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then(data => {
                document.getElementById('user_ip').value = data.ip;
                document.getElementById('create-account-link').href = `log_user1.php?create_account=true&user_ip=${data.ip}`;
            });
    </script>
</body>
</html>
