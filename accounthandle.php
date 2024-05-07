<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Stout Bank - Accounts</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header class="banner">
        <div class="banner-container">
            <div class="logo-container">
                <img src="img/stout.png" alt="Stout Bank Logo" class="logo-small" />
            </div>
            <div class="text-container">
                <h1>Welcome to Stout Bank</h1>
                <p class="slogan">Bank on Stout: Where Stability Meets Service</p>
            </div>
        </div>
        <div class="banner-info-container">
            <a class="stout-phone-number" href="tel:1-800-235-5261">1-800-STOUTB1 (235-5261)</a>
            <span class="routing-number">
                Routing #
                <?php
                session_start(); // Start the session
                // Check if the session variable is set
                if (isset($_SESSION['routing_number'])) {
                    $routing_number = $_SESSION['routing_number'];
                    echo '<span class="routing-number">' . $routing_number . '</span>';
                } else {
                    // Redirect the user to the login page if they are not logged in
                    header("Location: portal.html");
                    exit(); // Stop further execution
                }
                ?>
            </span>
            <?php
            // Include the configuration file
            require_once(__DIR__ . '/config/config.php');

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Get the username from session
            $username = $_SESSION['username'];

            $routing_number = $_SESSION['routing_number'];

            $sql = "SELECT seen, notification_id FROM user_notifications WHERE routing_number = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $routing_number);
            $stmt->execute();
            $result = $stmt->get_result();

            $foundUnseen = false;
            $foundNotificationOne = false;

            while ($row = $result->fetch_assoc()) {
                if ($row['notification_id'] == 1) {
                    $foundNotificationOne = true;
                }
                if ($row['seen'] == 0) {
                    $foundUnseen = true;
                    break;
                }
            }

            $stmt->close();

            if (!$foundNotificationOne) {
                $int = 1;
                $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, ?, 0)";
                $insertStmt = $conn->prepare($insertSql);
                if (!$insertStmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $insertStmt->bind_param("ii", $routing_number, $int);
                $insertStmt->execute();
                $insertStmt->close();

                // Display bell icon with red dot
                ?>
                <a class="bell" href="/StoutBank/message.php">
                    <img src="img/bell.png" alt="bell" />
                    <img src="img/red.png" alt="Red Dot" class="notification-dot" />
                </a>
                <?php
            } else if (!$foundUnseen) {
                ?>
                    <a class="bell" href="/StoutBank/message.php">
                        <img src="img/bell.png" alt="bell" />
                    </a>
                <?php
            } else {
                ?>
                    <a class="bell" href="/StoutBank/message.php">
                        <img src="img/bell.png" alt="bell" />
                        <img src="img/red.png" alt="Red Dot" class="notification-dot" />
                    </a>
                <?php
            }
            ?>
            <a class="logout-link" href="/StoutBank/logout.php">
                Logout
                <img src="img/powersign.jpg" alt="Power Sign" />
            </a>

        </div>
    </header>

    <header class="task-bar">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="accounts.php" class="active">Accounts</a></li>
            <li><a href="transfers.php">Transfers</a></li>
            <li><a href="services.php">Services</a></li>
        </ul>
    </header>

    <?php

    // Get the username from session
    $username = $_SESSION['username'];

    $routing_number = $_SESSION['routing_number'];

    // Check if the user already has a checking account
    $sql = "SELECT * FROM checking_accounts WHERE u_id = (SELECT routing_number FROM users WHERE username = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0 && $_POST['accountType'] === 'checking') {
        // User already has a checking account and trying to create another
        echo "Sorry, you can only have one checking account per user.<br>";
        echo '<a href="/StoutBank/accounts.php" class="button">Return to Accounts</a>';
        exit;
    }

    // Check if the user already has 3 saving accounts
    $sql = "SELECT * FROM saving_accounts WHERE u_id = (SELECT routing_number FROM users WHERE username = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 2 ) {
        // User already has a checking account and trying to create another
        echo "Sorry, you can only have 3 saving accounts per user.<br>";
        echo '<a href="/StoutBank/accounts.php" class="button">Return to Accounts</a>';
        exit;
    }

    // Get form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $accountType = $_POST['accountType'];
    $initialDeposit = ($accountType === 'checking') ? 300.00 : 0.00; // Set initial deposit based on account type

    // Prepare SQL statement to retrieve the routing number based on the username
    $sql = "SELECT routing_number FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the routing number
    $row = $result->fetch_assoc();
    $u_id = $row['routing_number'];

    // Construct SQL query based on account type
    if ($accountType === 'checking') {
        $tableName = 'checking_accounts';
        $columnPrefix = 'checking_account';
    } else {
        $tableName = 'saving_accounts';
        $columnPrefix = 'saving_account';
    }

    // Insert account data into the appropriate table
    $sql = "INSERT INTO $tableName (amount, u_id, {$columnPrefix}_first_name, {$columnPrefix}_last_name, {$columnPrefix}_email)
VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("disss", $initialDeposit, $u_id, $firstName, $lastName, $email); // Assuming $u_id holds the routing number
    $stmt->execute();

    // Creating Account
    if ($stmt->affected_rows > 0) {
        echo "Account created successfully!<br>";
        echo '<a href="/StoutBank/accounts.php" class="button">Return to Accounts</a>';

        $sql = "SELECT notification_id FROM user_notifications WHERE routing_number = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $routing_number);
        $stmt->execute();
        $result = $stmt->get_result();

        $foundNotificationTwo = false;

        while ($row = $result->fetch_assoc()) {
            if ($row['notification_id'] == 2) {
                $foundNotificationTwo = true;
            }
        }

        $stmt->close();
        if (!$foundNotificationTwo) {
            $int = 2;
            $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, ?, 0)";
            $insertStmt = $conn->prepare($insertSql);
            if (!$insertStmt) {
                die("Prepare failed: " . $conn->error);
            }
            $insertStmt->bind_param("ii", $routing_number, $int);
            $insertStmt->execute();
            $insertStmt->close();
        }
    } else {
        echo "Error creating account: " . $conn->error;
    }

    $conn->close();
    ?>

</body>
</html>
