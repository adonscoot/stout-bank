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
            <li><a href="accounts.php" >Accounts</a></li>
            <li><a href="transfers.php">Transfers</a></li>
            <li><a href="services.php" class="active">Services</a></li>
        </ul>
    </header>

    <main>
        <div class="create-account-container">
            <h2>Earn Money with Stout</h2>
            <p>Solve puzzles for direct deposit into your checkings account!</p>
            <a href="/StoutBank/earn.php" class="button">Click Here</a>
        </div>
        <div class="create-account-container">
            <h2>Help Page</h2>
            <p>More information about concepts.</p>
            <a href="/StoutBank/helppage.php" class="button">Click Here</a>
        </div>
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
    <script>
        $(document).ready(function () {
            // Show the hidden link after a delay
            setTimeout(function () {
                $("#security-challenge-link").fadeIn(1000); // Example: fade in after 2 seconds
            }, 1000);

            // When the hidden link is clicked
            $("#security-challenge-link").click(function (e) {
                e.preventDefault(); // Prevent the default link behavior
                // Show the challenge content
            alert("\x4D\x61\x6B\x65\x20\x74\x77\x6F\x20\x74\x72\x61\x6E\x73\x66\x65\x72\x73\x20\x74\x6F\x20\x73\x65\x65\x20\x77\x68\x61\x74\x27\x73\x20\x6E\x65\x78\x74\x2C\x20\x61\x20\x63\x6C\x75\x65\x20\x61\x77\x61\x69\x74\x73\x20\x77\x68\x65\x72\x65\x20\x79\x6F\x75\x72\x20\x66\x75\x6E\x64\x73\x20\x69\x6E\x74\x65\x72\x73\x65\x63\x74\x2E");
            });
        });
    </script>
</body>
</html>
