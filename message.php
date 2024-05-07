
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
            <a class="bell" href="/StoutBank/message.php">
                <img src="img/bell.png" alt="bell" />
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
            <li><a href="transfers.php">Transfers</a></li>
            <li><a href="services.php">Services</a></li>
        </ul>
    </header>

    <?php
    // Include the configuration file
    require_once(__DIR__ . '/config/config.php');

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the routing number from session
    $routing_number = $_SESSION['routing_number'];

    $sql = "SELECT n.message, un.seen, un.notification_id, DATE_FORMAT(un.date_earned, '%Y-%m-%d %H:%i:%s') AS formatted_date
    FROM user_notifications un
    JOIN notifications n ON un.notification_id = n.notification_id
    WHERE un.routing_number = ?
    ORDER BY un.date_earned DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $routing_number);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <main>
        <?php
        $totalNotifications = 9; // Total number of notifications
        $seenCount = 0; // Counter for seen notifications

        $sql = "SELECT n.message, un.seen, un.notification_id, DATE_FORMAT(un.date_earned, '%Y-%m-%d %H:%i:%s') AS formatted_date
            FROM user_notifications un
            JOIN notifications n ON un.notification_id = n.notification_id
            WHERE un.routing_number = ?
            ORDER BY un.date_earned DESC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $routing_number);
        $stmt->execute();
        $result = $stmt->get_result();

        $notificationIds = []; // Array to hold fetched notification IDs
        while ($row = $result->fetch_assoc()) {
            $notificationIds[] = $row['notification_id']; // Collect all notification IDs
            if ($row['seen']) {
                $seenCount++;
            }
        }

        // Check if user has seen 8 notifications and if the 9th notification is not already delivered
        if ($seenCount == 8 && !in_array(9, $notificationIds)) {
            $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, 9, 0)";
            $insertStmt = $conn->prepare($insertSql);
            if (!$insertStmt) {
                die("Prepare failed: " . $conn->error);
            }
            $insertStmt->bind_param("i", $routing_number);
            $insertStmt->execute();
            $insertStmt->close();
            $seenCount++; // Increment seen count since the 9th is now added
        }

        // Display the progress bar
        $progressPercent = ($seenCount / $totalNotifications) * 100;
        echo "<div class='progress-bar-container' style='width: 100%; background-color: #ddd;'>";
        echo "<div class='progress-bar' style='width: $progressPercent%; background-color: #4CAF50; height: 20px;'></div>";
        echo "</div>";

        // Reset the result set to the beginning
        mysqli_data_seek($result, 0);

        while ($row = $result->fetch_assoc()) {
            echo "<div class='message-container'>";
            if (!$row['seen']) {
                echo "<img src='img/red.png' alt='Red Dot' class='red-dot' />";
                $updateSql = "UPDATE user_notifications SET seen = 1 WHERE notification_id = ? AND routing_number = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("ii", $row['notification_id'], $routing_number);
                $updateStmt->execute();
                $updateStmt->close();
            }
            echo "<p>" . htmlspecialchars($row['message']) . "</p><br>";
            if ($row['notification_id'] == 8) {
                echo "<br><a href='hidden.html'>Click here</a>";
            }
            if ($row['notification_id'] == 7) {
                echo "<br><a href='helppage.php?slide=4' class='button'>Learn More</a>";
            }
            if ($row['notification_id'] == 4) {
                echo "<br><a href='helppage.php?slide=3' class='button'>Learn More</a>";
            }
            if ($row['notification_id'] == 5) {
                echo "<br><a href='helppage.php?slide=5' class='button'>Learn More</a>";
            }
            if ($row['notification_id'] == 6) {
                echo "<br><a href='helppage.php?slide=6' class='button'>Learn More</a>";
            }
            if ($row['notification_id'] == 9) {
                echo "<br><a href='helppage.php' class='button'>Learn More</a>";
            }
            echo "</div>";
        }
        ?>
    </main>





    <?php
    // Close statement and connection
    $stmt->close();
    $conn->close();
    ?>

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