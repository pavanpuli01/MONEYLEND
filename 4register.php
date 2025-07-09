<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "money_lend"; 
$upload_dir = "uploads/";
session_start();

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// File upload directory

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);  // Ensure directory exists
}

// Upload file paths


// Function to handle file upload
// Function to handle file upload
function handleFileUpload($file_input_name, $upload_dir) {
    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
    
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return "File upload error: " . $_FILES[$file_input_name]['error'];
    }

    $file_name = basename($_FILES[$file_input_name]['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_size = $_FILES[$file_input_name]['size'];
    $file_tmp = $_FILES[$file_input_name]['tmp_name'];

    if (!in_array($file_ext, $allowed_types)) {
        return "Error: Invalid file type. Only jpg, jpeg, png, pdf are allowed.";
    }

    if ($file_size > 5 * 1024 * 1024) {
        return "Error: File size should be less than 5MB.";
    }

    $new_file_name = uniqid($file_input_name . "_") . "." . $file_ext;
    $file_path = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp, $file_path)) {
        return $file_path;
    } else {
        return "Upload failed: Unable to move file.";
    }



}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize user inputs
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $pan_number = trim($_POST['pan_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // File uploads
    $pan_front_path = $pan_back_path = $profile_photo_path = $bank_statement_path = "";

  

    // Validate inputs
    if (empty($first_name)) $errors['first_name'] = "First Name is required.";
    if (empty($last_name)) $errors['last_name'] = "Last Name is required.";
    if (empty($dob)) $errors['dob'] = "Date of Birth is required.";
    if (empty($email)) $errors['email'] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Invalid email format.";
    if (empty($phone)) $errors['phone'] = "Phone number is required.";
    if (!preg_match('/^[0-9]{10}$/', $phone)) $errors['phone'] = "Invalid phone number format.";
    if (empty($address)) $errors['address'] = "Address is required.";
    if (empty($pincode)) $errors['pincode'] = "Pincode is required.";
    if (empty($password)) $errors['password'] = "Password is required.";
    if ($password !== $confirm_password) $errors['confirm_password'] = "Passwords do not match.";

    // Check if email already exists
    $stmt = $conn->prepare("SELECT email FROM applications WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors['email'] = "Email already registered. Try logging in.";
    }
    $stmt->close();

    // If no errors, insert data
    if (empty($errors)) {
        $sql = "INSERT INTO applications (first_name, last_name, dob, email, phone, address, pincode, pan_number, pan_front, pan_back, profile_photo, bank_statement, password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters (including the file paths)
            $stmt->bind_param("sssssssssssss", $first_name, $last_name, $dob, $email, $phone, $address, $pincode, $pan_number, $pan_front_path, $pan_back_path, $profile_photo_path, $bank_statement_path, $password);
            if (!empty($_FILES['pan_front']['name'])) {
                $pan_front_path = handleFileUpload('pan_front', $upload_dir);
            }else {
                echo "File 1 is required";
            }
            if (!empty($_FILES['pan_back']['name'])) {
                $pan_back_path = handleFileUpload('pan_back', $upload_dir);
            }else {
                echo "File 2 is required";
            }
        
            if (!empty($_FILES['profile_photo']['name'])) {
                $profile_photo_path = handleFileUpload('profile_photo', $upload_dir);
            }else {
                echo "File 3 is required";
            }
            if (!empty($_FILES['bank_statement']['name'])) {
                $bank_statement_path = handleFileUpload('bank_statement', $upload_dir);
            }else {
                echo "File 4 is required";
            }
            // Execute the query
            $execute_success = $stmt->execute();

            // Check if the query was successful
            if ($execute_success) {
                $_SESSION['user_email'] = $email;
                header("Location: dashboard.php");
                exit; 
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error preparing the query: " . $conn->error;
        }
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Lend</title>
    <link rel="stylesheet" href="register2.css">
</head>
<body>
    <div class="top-container">
        <div class="logo"><h3>Money Lend</h3></div>
    </div>
    <div class="down-container">
        <div class="inner-container">
            <div class="top">
                <h3 class="heading">Complete your application to get approved.</h3>
            </div>
            <div class="bottom">
                <form action="4register.php" method="post" enctype="multipart/form-data">
                    <div class="left">

                        <div class="input-container">
                        <label for="first_name">First Name</label>
                        <span class="error"><?= $errors['first_name'] ?? '' ?></span>
                        </div>
                        <input type="text" name="first_name" placeholder="First Name" value="<?= htmlspecialchars($first_name) ?>">
                        

                        <div class="input-container">
                        <label for="last_name">Last Name</label>
                        <span class="error"><?= $errors['last_name'] ?? '' ?></span>
                        </div>
                        <input type="text" name="last_name" placeholder="Last Name" value="<?= htmlspecialchars($last_name) ?>">
                        
                        <div class="input-container">
                        <label for="dob">DOB</label>
                        <span class="error"><?= $errors['dob'] ?? '' ?></span>
                        </div>
                        <input type="date" name="dob" value="<?= htmlspecialchars($dob) ?>">
                        

                        <div class="input-container">
                        <label for="email">Email</label>
                        <span class="error"><?= $errors['email'] ?? '' ?></span>
                        </div>
                        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>">

                        <div class="input-container">
                        <label for="phone">Phone Number (+91)</label>
                        <span class="error"><?= $errors['phone'] ?? '' ?></span>
                        </div>
                        <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($phone) ?>">
                        
                        <div class="input-container">
                        <label for="address">Address</label>
                        <span class="error"><?= $errors['address'] ?? '' ?></span>
                        </div>
                        <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($address) ?>">
                        
                        <div class="input-container">
                        <label for="pincode">Pincode</label>
                        <span class="error"><?= $errors['pincode'] ?? '' ?></span>
                        </div>
                        <input type="text" name="pincode" placeholder="Pincode" value="<?= htmlspecialchars($pincode) ?>">
                        

                    </div>

                    <div class="right">

                        <div class="input-container">
                        <label for="pan_number">PAN Number</label>
                        <span class="error"><?= $errors['pan_number'] ?? '' ?></span>
                        </div>
                        <input type="text"  name="pan_number" placeholder="pan_number" value="<?= htmlspecialchars($pan_number) ?>">

                        <div class="input-container"> 
                        <label for="pan_front">Upload PAN Card Front</label>
                        <span class="error"><?= $errors['pan_front'] ?? '' ?></span>
                        </div>
                        <input type="file" name="pan_front">
                        
                        <div class="input-container">
                        <label for="pan_back">Upload PAN Card Back</label>
                        <span class="error"><?= $errors['pan_back'] ?? '' ?></span>  
                        </div>
                        <input type="file" name="pan_back">
                                             
                        <div class="input-container">
                        <label for="profile_photo">Upload Your Photo</label>
                        <span class="error"><?= $errors['profile_photo'] ?? '' ?></span>
                        </div>
                        <input type="file" name="profile_photo">
                        
                        <div class="input-container">
                        <label for="bank_statement">Bank Statement</label>
                        <span class="error"><?= $errors['bank_statement'] ?? '' ?></span>
                        </div>
                        <input type="file" name="bank_statement" accept=".jpg,.jpeg,.png,.pdf">
                        
                        <div class="input-container">
                        <label for="password">Password</label>
                        <span class="error"><?= $errors['password'] ?? '' ?></span>
                        </div>
                        <input type="password" name="password" placeholder="Password">
                        
                        <div class="input-container">
                        <label for="confirm_password">Confirm Password</label>
                        <span class="error"><?= $errors['confirm_password'] ?? '' ?></span>
                        </div>
                        <input type="password" name="confirm_password" placeholder="Confirm Password">
                        
                        

                        <button type="submit">Submit</button>
                    </div>
                </form>
                <p class="error"><?= $errors['general'] ?? '' ?></p>
            </div>
        </div>
    </div>
</body>
</html>