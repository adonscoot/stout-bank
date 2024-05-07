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

            $sql = "SELECT seen FROM user_notifications WHERE routing_number = ? AND notification_id = 8";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $routing_number);
            $stmt->execute();
            $result = $stmt->get_result();
            $foundNotificationOne = $result->num_rows > 0;

            if (!$foundNotificationOne) {
                $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, 8, 0)";
                $insertStmt = $conn->prepare($insertSql);
                if (!$insertStmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $insertStmt->bind_param("i", $routing_number);
                $insertStmt->execute();
                $insertStmt->close();
            }

            $conn->commit();
            $stmt->close();

            // Check for unseen notifications
            $sql = "SELECT seen FROM user_notifications WHERE routing_number = ? AND seen = 0";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $routing_number);
            $stmt->execute();
            $result = $stmt->get_result();
            $foundUnseen = $result->num_rows > 0;

            ?>
            <a class="bell" href="/StoutBank/message.php">
                <img src="img/bell.png" alt="bell" />
                <?php if ($foundUnseen): ?>
                    <img src="img/red.png" alt="Red Dot" class="notification-dot" />
                <?php endif; ?>
            </a>
            <a class="logout-link" href="/StoutBank/logout.php">
                Logout
                <img src="img/powersign.jpg" alt="Power Sign" />
            </a>

        </div>
    </header>
    <header class="task-bar">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="accounts.php">Accounts</a></li>
            <li><a href="transfers.php" class="active">Transfers</a></li>
            <li><a href="services.php">Services</a></li>
        </ul>
    </header>
    <main>
        <div class="create-account-container">
            <h1>Transfer Money</h1>
            <form method="post" action="/StoutBank/transferhandle.php">
                <label for="source_account">Source Account:</label>
                <select id="source_account" name="source_account" onchange="updateSourceAccountType()" required>
                    <option value="" selected disabled>Select Account</option>
                    <?php

                $sql = "SELECT checking_account_number FROM checking_accounts WHERE u_id = (SELECT routing_number FROM users WHERE username = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $checking_account_number = $row['checking_account_number'];
                    echo "<option value='$checking_account_number' source_account_type='checking'>Checking Account #$checking_account_number</option>";
                }

                $stmt->close();

                // Prepare SQL statement to retrieve user's savings accounts
                $sql = "SELECT saving_account_number FROM saving_accounts WHERE u_id = (SELECT routing_number FROM users WHERE username = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                // Display all possible savings accounts as options
                while ($row = $result->fetch_assoc()) {
                    $saving_account_number = $row['saving_account_number'];
                    echo "<option value='$saving_account_number' source_account_type='saving'>Saving Account #$saving_account_number</option>";
                }


                    ?>
                </select><br />

                <label for="destination_account">Destination Account:</label>

                <select id="destination_account" name="destination_account" onchange="updateDestinationAccountType()" required>
                    <option value="" selected disabled>Select Account</option>
                    <?php

                $sql = "SELECT checking_account_number FROM checking_accounts WHERE u_id = (SELECT routing_number FROM users WHERE username = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $checking_account_number = $row['checking_account_number'];
                    echo "<option value='$checking_account_number' destination_account_type='checking'>Checking Account #$checking_account_number</option>";
                }

                $stmt->close();

                $sql = "SELECT saving_account_number FROM saving_accounts WHERE u_id = (SELECT routing_number FROM users WHERE username = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                // Display all possible savings accounts as options
                while ($row = $result->fetch_assoc()) {
                    $saving_account_number = $row['saving_account_number'];
                    echo "<option value='$saving_account_number' destination_account_type='saving'>Saving Account #$saving_account_number</option>";
                }

                $stmt->close();
                $conn->close();
                    ?>
                </select><br />

                <input type="hidden" id="source_account_type" name="source_account_type" value="" />
                <input type="hidden" id="destination_account_type" name="destination_account_type" value="" />

                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required min="0" step="1" /><br />

                <input type="submit" value="Transfer" class="button" />
            </form>
            </div>
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

    <script>
        function updateSourceAccountType() {
            // Get the selected option
            var select = document.getElementById("source_account");
            var selectedOption = select.options[select.selectedIndex];

            // Get the source account type attribute value
            var sourceAccountType = selectedOption.getAttribute("source_account_type");

            // Set the value of the hidden input field
            document.getElementById("source_account_type").value = sourceAccountType;
        }
    </script>

    <script>
    function updateDestinationAccountType() {
        // Get the selected option
        var select = document.getElementById("destination_account");
        var selectedOption = select.options[select.selectedIndex];

        // Get the destination account type attribute value
        var destinationAccountType = selectedOption.getAttribute("destination_account_type");

        // Set the value of the hidden input field
        document.getElementById("destination_account_type").value = destinationAccountType;
    }
    </script>

    <script>
    // 05102024
    </script>



</body>
</html>
