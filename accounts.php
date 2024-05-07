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
            // Retrieve checking account information
            $sql = "SELECT checking_account_number, amount FROM checking_accounts WHERE u_id = (SELECT routing_number FROM users WHERE username = ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $num_checkings = $result->num_rows;
            $checking_accounts = array();
            while ($row = $result->fetch_assoc()) {
                $checking_accounts[] = $row;
            }
            // Retrieve savings account information
            $sql = "SELECT saving_account_number, amount FROM saving_accounts WHERE u_id = (SELECT routing_number FROM users WHERE username = ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $num_savings = $result->num_rows;
            $saving_accounts = array();
            while ($row = $result->fetch_assoc()) {
                $saving_accounts[] = $row;
            }
            // Retrieve transaction info
            $sql = "SELECT * FROM transactions WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $routing_number);
            $stmt->execute();
            $result = $stmt->get_result();
            $transactions = array();
            while ($row = $result->fetch_assoc()) {
                $transactions[] = $row;
            }

            // Check database for notification_id 3
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
                if ($row['notification_id'] == 3) {
                    $foundNotificationOne = true;
                }
                if ($row['seen'] == 0) {
                    $foundUnseen = true;
                    break;
                }
            }
           $stmt->close();
       
            if (!$foundNotificationOne) { 
                if (($num_checkings > 0) && ($num_savings > 0)) { // If the notification was not found and user made both types then give user notification
                    $int = 3;
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
                }
                else{
            ?>
                      <a class="bell" href="/StoutBank/message.php">
                        <img src="img/bell.png" alt="bell" />
                       <img src="img/red.png" alt="Red Dot" class="notification-dot" />
                    </a>
            <?php
                }
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

    <main>
        <div class="container-group">
            <?php
            // Display checking accounts
            foreach ($checking_accounts as $checking_account) {
                echo '<div class="checking-container">';
                echo '<h2>Checking</h2>';
                echo '<p>Account Number: ' . $checking_account['checking_account_number'] . '</p>';
                echo '<p>Total Amount: $' . $checking_account['amount'] . '</p>';
                echo '</div>';
            }
            // Display savings accounts
            echo '<div class="savings-container">';
            echo '<h2>Savings</h2>';
            foreach ($saving_accounts as $saving_account) {
                echo '<p>Account Number: ' . $saving_account['saving_account_number'] . '</p>';
                echo '<p>Total Amount: $' . $saving_account['amount'] . '</p>';
            }
            echo '</div>';
            // Sort transactions to display the most recent first
            usort($transactions, function ($a, $b) {
                return $b['transaction_id'] - $a['transaction_id']; // Descending order
            });
            echo '<div class="recent-transactions-container">';
            echo '<h2>Recent Transactions</h2>';
            foreach ($transactions as $transaction) {
                echo '<h3>Transaction Number: ' . $transaction['transaction_id'] . '</h3>';
                echo '<p>Total Amount: $' . $transaction['amount'] . '</p>';
                echo '<p>Source Account: ' . $transaction['source_account_type'] . ' ' . $transaction['source_account'] . '</p>';
                echo '<p>Destination Account: ' . $transaction['destination_account_type'] . ' ' . $transaction['destination_account'] . '</p>';
            }
            echo '</div>';
            ?>
            <div class="create-account-container">
                <h2>Create an Account</h2>
                <p>Open a new checking or savings account today.</p>
                <a href="/StoutBank/createaccount.php" class="button">Click Here</a>
            </div>
    </main>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
    $(".task-bar ul li a").click(function (e) {
        // Prevent default link behavior
        e.preventDefault();

        // Remove active class from all links
        $(".task-bar ul li a").removeClass("active");

        // Add active class to the clicked link
        $(this).addClass("active");

        // Redirect to the corresponding PHP page based on the link
        window.location.href = $(this).attr("href");
    });
});

    </script>
</body>
</html>
