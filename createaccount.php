<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Stout Bank - Create Accounts</title>
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

            // Set routing number
            $routing_number = $_SESSION['routing_number'];

            // Query for notification_id and seen attributes
            $sql = "SELECT seen, notification_id FROM user_notifications WHERE routing_number = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $routing_number);
            $stmt->execute();
            $result = $stmt->get_result();

            // Make variables for check
            $foundUnseen = false;
            $foundNotification = false;

            // checking for Unseen messages and Specific Message
            while ($row = $result->fetch_assoc()) {
                if ($row['notification_id'] == 1) {
                    $foundNotification = true;
                }
                if ($row['seen'] == 0) {
                    $foundUnseen = true;
                    break;
                }
            }

            $stmt->close();

            if (!$foundNotification) {
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

    <main>
        <div class="create-account-container">
            <h2>Get $300 on us when you open a new checking account!</h2>
            <form action="accounthandle.php" method="post">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" required /><br />

                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" required /><br />

                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required /><br />

                <label for="accountType">Account Type:</label>
                <select id="accountType" name="accountType" required onchange="setInitialDeposit()">
                    <option value="checking">Checking</option>
                    <option value="savings">Savings</option>
                </select><br />

                <label for="initialDeposit">Initial Deposit:</label>
                <input type="text" id="initialDeposit" name="initialDeposit" readonly /><br />

                <input type="submit" value="Create Account" class="button" />
            </form>
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

    <script>
        function setInitialDeposit() {
            var accountType = document.getElementById("accountType").value;
            var initialDeposit = document.getElementById("initialDeposit");

            // Set initial deposit based on account type
            if (accountType === "checking") {
                initialDeposit.value = "300";
            } else if (accountType === "savings") {
                initialDeposit.value = "0";
            }
        }
        setInitialDeposit();
    </script>
</body>
</html>
