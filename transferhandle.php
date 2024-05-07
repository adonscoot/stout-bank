<?php
session_start();

$source_account = $_POST['source_account'];
$source_account_type = $_POST['source_account_type'];
$destination_account = $_POST['destination_account'];
$destination_account_type = $_POST['destination_account_type'];
$amount = $_POST ['amount'];
if (!isset($_SESSION['key1'])) {
    $_SESSION['key1'] = 0; // Initialize 'key1' if it's not already set
}

// Include the configuration file
require_once(__DIR__ . '/config/config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Stout Bank - Sign Up</title>
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

                if (($num_checkings > 0) && ($num_savings > 0)) {


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
                } else {
                    ?>
                    <a class="bell" href="/StoutBank/message.php">
                        <img src="img/bell.png" alt="bell" />
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
        <h1>Transfer Money</h1>
        <?php

        if ($amount == "05102024") {

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
                if ($row['notification_id'] == 6) {
                    $foundNotificationOne = true;
                }
                if ($row['seen'] == 0) {
                    $foundUnseen = true;
                    break;
                }
            }

            $stmt->close();

            if (!$foundNotificationOne) {

                $int = 6;
                $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, ?, 0)";
                $insertStmt = $conn->prepare($insertSql);
                if (!$insertStmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $insertStmt->bind_param("ii", $routing_number, $int);
                $insertStmt->execute();
                $insertStmt->close();
            }

            echo '<div class="injection-result">
            <h2>Unexpected Access Granted!</h2>
            <p>You have entered a <strong>secret number</strong> that triggered an unexpected response. Below is sensitive information that is usually restricted. This simulation demonstrates what might happen in a real SQL injection attack:</p>
            <table>
                <thead>
                    <tr>
                        <th>Account Number</th>
                        <th>Account Holder</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>123456789</td>
                        <td>John Doe</td>
                        <td>$15,000</td>
                    </tr>
                    <tr>
                        <td>987654321</td>
                        <td>Jane Smith</td>
                        <td>$22,050</td>
                    </tr>
                </tbody>
            </table>
            <p class="warning">This is a simulated result. In a real scenario, accessing such information without authorization could lead to serious legal consequences and breach of privacy.</p>
          </div>
          <a href="/StoutBank/transfers.php" class="button">Return to Transfers</a>';
            exit();
        }

        if ($source_account == $destination_account && $source_account_type == $destination_account_type) // Error handle: source and destination accounts are the same
        {
            echo '<p style="color: red;">Source and destination accounts cannot be the same.</p>';
            echo '<a href="/StoutBank/transfers.php" class="button">Return to Transfers</a>';
        } else {
            if ($source_account_type == 'saving') // if savings: get amount for source account
            {
                $sql = "SELECT amount FROM saving_accounts WHERE saving_account_number = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $source_account);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $source_amount = $row['amount'];
            } else // else: get amount for checking source account
            {
                $sql = "SELECT amount FROM checking_accounts WHERE checking_account_number = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $source_account);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $source_amount = $row['amount'];
            }
            if ($source_amount < $amount) // Error handle: Account ammount is less than amount requested
            {
                echo '<p style="color: red;">Source account does not have enough funds. </p>';
                echo '<a href="/StoutBank/transfers.php" class="button">Return to Transfers</a>';
            } else // Do Transfer
            {
                if ($destination_account_type == 'saving') // if saving: get amount for destination account
                {
                    $sql = "SELECT amount FROM saving_accounts WHERE saving_account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $destination_account);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $destination_amount = $row['amount'];
                } else // else: get amount for destination checking account
                {
                    $sql = "SELECT amount FROM checking_accounts WHERE checking_account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $destination_account);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $destination_amount = $row['amount'];
                }

                // adjust account amounts
                $source_amount = $source_amount - $amount;
                $destination_amount = $destination_amount + $amount;

                if ($source_account_type == 'saving') // if saving: update source account
                {
                    $sql = "UPDATE saving_accounts SET amount = ? WHERE saving_account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $source_amount, $source_account);
                    $stmt->execute();
                } else // else: update source checking account
                {
                    $sql = "UPDATE checking_accounts SET amount = ? WHERE checking_account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $source_amount, $source_account);
                    $stmt->execute();
                }

                if ($destination_account_type == 'saving') // if saving: update destination account
                {
                    $sql = "UPDATE saving_accounts SET amount = ? WHERE saving_account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $destination_amount, $destination_account);
                    $stmt->execute();
                } else // else: update destination checking account
                {
                    $sql = "UPDATE checking_accounts SET amount = ? WHERE checking_account_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $destination_amount, $destination_account);
                    $stmt->execute();
                }

                // Create a transaction ticket
                $transaction_type = 'Transfer';
                $timestamp = date('Y-m-d H:i:s');

                $sql = "INSERT INTO transactions (user_id, transaction_type, amount,transaction_date, source_account, source_account_type, destination_account, destination_account_type)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssssss", $routing_number, $transaction_type, $amount, $timestamp, $source_account, $source_account_type, $destination_account, $destination_account_type); // Assuming $u_id holds the routing number
                $stmt->execute();
                echo "Transfer complete.<br>";
                echo '<a href="/StoutBank/transfers.php" class="button">Return to Transfers</a>';

                $sql = "SELECT seen, notification_id FROM user_notifications WHERE routing_number = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("i", $routing_number);
                $stmt->execute();
                $result = $stmt->get_result();

                $foundNotification = false;

                while ($row = $result->fetch_assoc()) {
                    if ($row['notification_id'] == 4) {
                        $foundNotification = true;
                    }
                }

                $stmt->close();

                if (!$foundNotification) {
                    $int = 4;
                    $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, ?, 0)";
                    $insertStmt = $conn->prepare($insertSql);
                    if (!$insertStmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $insertStmt->bind_param("ii", $routing_number, $int);
                    $insertStmt->execute();
                    $insertStmt->close();

                    $conn->close();
                }
            }
        }
        ?>
    </main>




    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
    $(".task-bar ul li a").click(function (e) {
        e.preventDefault();

        $(".task-bar ul li a").removeClass("active");

        $(this).addClass("active");

        window.location.href = $(this).attr("href");
    });
});

    </script>



</body>
</html>
