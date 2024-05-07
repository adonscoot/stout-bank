<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
      <header class="banner">
        <div class="container">
            <div class="logo-container">
                <img src="img/stout.png" alt="Stout Bank Logo" class="logo">
            </div>
            <div class="text-container">
                <h1>Bank on Stout: Where Stability Meets Service</h1>
            </div>
        </div>
    </header>
<?php

session_start();

// Include the configuration file
require_once(__DIR__ . '/config/config.php');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$username_input = $_POST['username'];
$password_input = $_POST['password'];

// Check if the user is currently locked out
if (isset($_SESSION['lockout']) && $_SESSION['lockout'] > time()) {
    $time_left = $_SESSION['lockout'] - time();
    echo "Account locked. Please try again in " . $time_left . " seconds.";
    exit;
}

// Prepare SQL statement to retrieve user data based on the username
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username_input);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User found, verify password
    $row = $result->fetch_assoc();
    $stored_password = $row["password"];
    $hashed_password_input = hash('sha256', $password_input); // Hash the input password

    if ($hashed_password_input == $stored_password) {
        // Password is correct, set session variables and redirect the user
        $_SESSION['username'] = $row['username'];
        $_SESSION['first_name'] = $row['first_name'];
        $_SESSION['last_name'] = $row['last_name'];
        $_SESSION['routing_number'] = $row['routing_number'];
        header("Location: /StoutBank/home.php");
        exit; // Ensure that no further code is executed after the redirection
    } else {
        // Password is incorrect
        // Increment failed login attempts for this username
        if (!isset($_SESSION['failed_attempts'][$username_input])) {
            $_SESSION['failed_attempts'][$username_input] = 1;
        } else {
            $_SESSION['failed_attempts'][$username_input]++;
        }
        // Check if the user has reached the maximum number of failed attempts
        if ($_SESSION['failed_attempts'][$username_input] >= 3) {
            // Lock the user out for 5 minutes
            $_SESSION['lockout'] = time() + (5 * 60);
            echo "Too many failed login attempts. Account locked for 5 minutes.";
            echo '<a href="/StoutBank/portal.html" class="button">Return to Portal</a>';
            exit;
        }
        echo "Wrong username or password.";
    }
} else {
    // User not found
    echo "Wrong username or password.";
}

// Close connection
$stmt->close();
$conn->close();

?>
     <div class="button-container">
            <a href="portal.html" class="button">Back to Portal</a>
            </div>
    </body>
