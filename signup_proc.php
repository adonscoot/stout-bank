<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Stout Bank - Sign Up</title>
    <link rel="stylesheet" href="styles.css" />
</head>

<body>

    <header class="banner-small">
        <div class="container">
            <div class="logo-container">
                <img src="img/stout.png" alt="Stout Bank Logo" class="logo-small" />
            </div>
            <div class="text-container">
                <h1>Welcome to Stout Bank</h1>
                <p class="slogan">Bank on Stout: Where Stability Meets Service</p>
            </div>
        </div>
    </header>

        <?php
    // Include the configuration file
    require_once(__DIR__ . '/config/config.php');

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthdate = $_POST['birthdate'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input fields
    if (empty($first_name) || empty($last_name) || empty($birthdate) || empty($username) || empty($password)) {
        // Handle missing required fields error
        echo "All fields are required";
        exit;
    }

    // Validate date format (assuming YYYY-MM-DD format)
    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $birthdate)) {
        // Handle invalid date format error
        ?>
        <div class="button-container">
            <h3>Invalid date format!</h3>
            <a href="signup.html" class="button">Back to signup</a>
        </div>
        <?php
        exit;
    }

    // Calculate age based on birthdate
    $age = floor((time() - strtotime($birthdate)) / 31556926); // seconds in a year

    // Validate age requirement (18 years or older)
    if ($age < 18) {
        // Handle age requirement error
        ?>
        <div class="button-container">
            <h3>You must be at least 18 years old to sign up</h3>
            <a href="portal.html" class="button">Back to Portal</a>
        </div>
        <?php
        exit;
    }

    // Validate password requirements
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        // Handle invalid password format error
        ?>
        <div class="button-container">
            <h3>Password must be at least 8 characters long and contain at least one uppercase letter and one special character.</h3>
            <a href="signup.html" class="button">Back to signup</a>
        </div>
        <?php
        exit;
    }

    // Sanitize input to prevent SQL injection
    $first_name = mysqli_real_escape_string($conn, $first_name);
    $last_name = mysqli_real_escape_string($conn, $last_name);
    $birthdate = mysqli_real_escape_string($conn, $birthdate);
    $username = mysqli_real_escape_string($conn, $username);

    // Hash the password using SHA256
    $password_hashed = hash('sha256', $password);

    // Prepare and bind SQL statement for INSERT query
    $stmtInsert = $conn->prepare("INSERT INTO users (first_name, last_name, birthdate, username, password) VALUES (?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("sssss", $first_name, $last_name, $birthdate, $username, $password_hashed);

    // Check if the username already exists
    $stmtCheck = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $stmtCheck->bind_param("s", $username);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        echo "Username already exists. Please choose a different username.<br>";
        echo '<a href="/StoutBank/signup.html" class="button">Return to Signup</a>';
    } else {
        // Execute INSERT SQL statement
        if ($stmtInsert->execute()) {
            echo "New account created successfully<br>";
            echo '<a href="/StoutBank/portal.html" class="button">Return to Accounts</a>';
        } else {
            echo "Error: " . $stmtInsert->error;
        }
    }
    $stmtCheck->close();
    $stmtInsert->close();
    $conn->close();
        ?>
    </body>

</html>
