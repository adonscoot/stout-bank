<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stout Bank - Home</title>
    <link rel="stylesheet" href="styles.css">
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
                session_start();
                if (!isset($_SESSION['routing_number'])) {
                    header("Location: portal.html");
                    exit();
                }
                echo '<span class="routing-number">' . $_SESSION['routing_number'] . '</span>';
                ?>
            </span>
            <?php
            require_once(__DIR__ . '/config/config.php');
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $routing_number = $_SESSION['routing_number'];

            // Check and potentially insert for notification 1 aka Welcome notification
            $sql = "SELECT seen FROM user_notifications WHERE routing_number = ? AND notification_id = 1";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $routing_number);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, 1, 0)";
                $insertStmt = $conn->prepare($insertSql);
                if (!$insertStmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $insertStmt->bind_param("i", $routing_number);
                $insertStmt->execute();
                $insertStmt->close();
            }
            $stmt->close();


            if (isset($_GET['source'])) {
                // Check and potentially insert for notification 7 aka Phising Attack notification
                $sql = "SELECT seen FROM user_notifications WHERE routing_number = ? AND notification_id = 7";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("i", $routing_number);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows == 0) {
                    $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, 7, 0)";
                    $insertStmt = $conn->prepare($insertSql);
                    if (!$insertStmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $insertStmt->bind_param("i", $routing_number);
                    $insertStmt->execute();
                    $insertStmt->close();
                }
                $stmt->close();
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

            while ($row = $result->fetch_assoc()) {
                if ($row['seen'] == 0) {
                    $foundUnseen = true;
                    break;
                }
            }

            $stmt->close();
            // Display the notification bell
           
            ?>
            <a class="bell" href="/StoutBank/message.php">
                <img src="img/bell.png" alt="bell" />
                <?php
                 if($foundUnseen)
                 {
                     ?>
                     <img src="img/red.png" alt="Red Dot" class="notification-dot" />
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
            <li><a href="home.php" class="active">Home</a></li>
            <li><a href="accounts.php">Accounts</a></li>
            <li><a href="transfers.php">Transfers</a></li>
            <li><a href="services.php">Services</a></li>
        </ul>
    </header>

    <main>
        <div class="container-group">
            <div class="image-container">
                <h2 class="image-text">Building Trust, One Transaction at a Time: Welcome to Stout Bank</h2>
                <img src="img/banking-person.jfif" alt="Banking Person" class="banking-person-image" />
            </div>
            <div class="create-account-container">
                <h2>Help Page</h2>
                <p>More information and help.</p>
                <a href="/StoutBank/helppage.php" class="button">Click Here</a>
                <br />
                <h2>Start Here: Create an Account</h2>
                <p>Open a new checking or savings account today.</p>
                <a href="/StoutBank/createaccount.php" class="button">Click Here</a> 
                
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="logo-container">
                <img src="img/stout.png" alt="Stout Bank Logo" class="logo-small" />
            </div>
            <div class="text-container">
                <p>This is a simulated banking environment created for educational purposes only. No real transactions or financial services are conducted here.</p>
                <p>Have questions? Contact us at <a href="mailto:nathanadenscott@outlook.com">nathanadenscott@outlook.com</a>.</p>
            </div>
    </footer>

    <!--Java Script for Task Bar-->
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
