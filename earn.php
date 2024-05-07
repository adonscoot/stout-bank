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
            <li><a href="accounts.php">Accounts</a></li>
            <li><a href="transfers.php">Transfers</a></li>
            <li><a href="services.php" class="active">Services</a></li>
        </ul>
    </header>

    <?php
    $_SESSION['earn'] = 0;
    function caesarCipher($str, $shift)
    {
        $result = '';
        $length = strlen($str);
        for ($i = 0; $i < $length; $i++) {
            $char = $str[$i];
            if (ctype_lower($char)) {
                $result .= chr(((ord($char) - 97 + $shift) % 26) + 97);
            } else {
                $result .= $char;
            }
        }
        return $result;
    }

    $quotes = array(
        "apple",
        "happy",
        "house",
        "water",
        "earth",
        "money",
        "party",
        "light",
        "heart",
        "music",
        "pizza",
        "river",
        "world",
        "table",
        "chair",
        "truck",
        "beach",
        "paint",
        "night",
        "field"
    );
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if the challenge form is submitted
        if (isset($_POST['challenge_submit'])) {
            // Get the user's input and the original quote
            $userQuote = $_POST['userQuote'];
            $originalQuote = $_POST['originalQuote'];

            // Check if the user's input matches the original quote
            if ($userQuote == $originalQuote) {
                $_SESSION['reward_amount'] = $_POST['reward'];
                echo "<h2 style='color: green;'>Congratulations! You got $" . $_SESSION['reward_amount'] . "!</h2>";
                // Award $10 to the user's account
                $sql = "SELECT amount FROM checking_accounts WHERE u_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $routing_number);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $amount = $row['amount'];

                $amount = $amount + $_SESSION['reward_amount'];

                $sql = "UPDATE checking_accounts SET amount = ? WHERE u_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $amount, $routing_number);
                $stmt->execute();
                $stmt->close();

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
                    if ($row['notification_id'] == 5) {
                        $foundNotification = true;
                    }
                }

                $stmt->close();

                if (!$foundNotification) {
                    $int = 5;
                    $insertSql = "INSERT INTO user_notifications (routing_number, notification_id, seen) VALUES (?, ?, 0)";
                    $insertStmt = $conn->prepare($insertSql);
                    if (!$insertStmt) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $insertStmt->bind_param("ii", $routing_number, $int);
                    $insertStmt->execute();
                    $insertStmt->close();

                    $conn->close();
                    header("Refresh:3; url=accounts.php"); // Redirect to services.php after 5 seconds
                    exit(); // Stop further execution
                } else {
                    echo "<h2 style='color: red;'>Sorry, that's not correct. Please try again.</h2>";
                }
            }
        }
    }

    // Display the form to select the reward amount
    ?>

    <div class="create-account-container">
        <h2>Choose Reward Amount</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <button type="submit" name="reward" value="10" class="button">$10</button>
            <button type="submit" name="reward" value="20" class="button">$20</button>
            <button type="submit" name="reward" value="30" class="button">$30</button>
        </form>
    </div>

    <?php
    // Display the challenge if the reward amount is set and valid
   
    if (isset($_POST['reward']) && in_array($_POST['reward'], ['10', '20', '30'])) {
        $rewardAmount = $_POST['reward'];
        // Process the selected reward amount
        switch ($rewardAmount) {
            case '10':
                $challenge = $quotes[array_rand($quotes)]; // Get a random quote
                $cipheredChallenge = caesarCipher($challenge, 1); // Encrypt by shifting 1
                break;
            case '20':
                $challenge = $quotes[array_rand($quotes)];
                $cipheredChallenge = caesarCipher($challenge, 2); // Encrypt by shifting 2
                break;
            case '30':
                $challenge = $quotes[array_rand($quotes)];
                $cipheredChallenge = caesarCipher($challenge, 3); // Encrypt by shifting 3
                break;
        }
        ?>
          
       <div class="create-account-container">
          <h3>Challenge:</h3>
           <p><strong>Ciphered Quote:</strong> <?php echo $cipheredChallenge; ?></p>
           <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
               <label for="userQuote">Enter the Original Quote:</label>
              <input type="text" id="userQuote" name="userQuote">
              <input type="hidden" name="originalQuote" value="<?php echo $challenge; ?>">
             <input type="hidden" name="reward" value="<?php echo $rewardAmount; ?>"> <!-- Add reward amount as a hidden input field -->
         <button type="submit" name="challenge_submit" class="button">Submit</button>
               <p>Need help? <a href="helppage.php?slide=5" class="help-link">Click here</a> for more information and tips on how to solve the cipher challenge.</p>
        </form>
        </div>


    <?php
    }
   
    

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
