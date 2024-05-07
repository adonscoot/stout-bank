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
                $notificationIds[] = $row['notification_id'];
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

    <main>
        <div id="slideshow-container">
            <!-- Welcome Slide -->
            <div class="mySlides fade">
                <div class="create-account-container full-screen">
                    <h2>Welcome to Stout Bank</h2>
                    <p>This application is designed not just as a simulation of everyday banking activities but as a comprehensive educational tool to enhance your understanding of cybersecurity.
            Here, you can explore an array of banking features while gaining practical insights into crucial cybersecurity concepts.</p>
                    <h2>Notifications</h2>
                    <p>Pay close attention to our messaging system located in the top right center of your screen. A red dot on the bell icon indicates that you have unread messages - these
            messages contain hints and tips to guide your next steps within the simulation.</p>
                    <h2>Help Page</h2>
                    <p>Come back here anytime to deepen your knowledge or to find the guidance necessary to advance through the challenges. Links to Help Page on the Home and Services tabs.</p>
                </div>
            </div>
            <!-- Strong Passwords Slide -->
            <div class="mySlides fade">
                <div class="create-account-container full-screen">
                    <h2>Creating Strong Passwords</h2>
                    <p>Strong passwords are your first defense against unauthorized access to your account. Here’s how you can create a password that's both secure and memorable:</p>
                    <ul>
                        <li><strong>Length Matters:</strong> Your password should be at least 8 characters long. Longer passwords are more secure.</li>
                        <li><strong>Mix It Up:</strong> Use a combination of uppercase and lowercase letters, numbers, and symbols.</li>
                        <li><strong>Avoid Common Words:</strong> Steer clear of obvious words and phrases like "password" or your name.</li>
                        <li><strong>Use Passphrases:</strong> Consider using a random phrase made up of multiple words, which is easier to remember and harder to crack.</li>
                        <li><strong>Don’t Reuse Passwords:</strong> Use a unique password for each of your accounts to limit breaches to one account at a time.</li>
                    </ul>
                    <p>Remember, a strong password is crucial for protecting your personal information and financial assets.</p>
                </div>
            </div>

            <div class="mySlides fade">
                <div class="create-account-container full-screen">
                    <h2>Congratulations on Completing Your Money Transfer!</h2>
                    <p>Successfully transferring funds is a key part of managing your finances. Here’s how you can ensure your transactions are not only successful but also secure:</p>
                    <h3>Check Recent Transactions</h3>
                    <p>Regularly review your transactions in the Accounts tab under Recent Transactions. This practice helps you:</p>
                    <ul>
                        <li><strong>Monitor Activity:</strong> Keep track of what’s coming in and going out. Regular monitoring can help you spot any unauthorized transactions early.</li>
                        <li><strong>Understand Spending:</strong> See where your money goes, which can aid in better budgeting and financial planning.</li>
                        <li><strong>Verify Transfers:</strong> Ensure all your transfers are completed correctly and to the right recipient.</li>
                    </ul>
                    <h3>Secure Your Transfers</h3>
                    <p>To further protect your financial information, consider the following tips:</p>
                    <ul>
                        <li><strong>Use Strong Passwords:</strong> Always secure your bank accounts with strong, unique passwords.</li>
                        <li><strong>Enable Two-Factor Authentication (2FA):</strong> Add an extra layer of security by enabling 2FA, which requires a second form of identification before accessing your account.</li>
                        <li><strong>Secure Connections:</strong> Only perform banking transactions over a secure, private Wi-Fi network—not public Wi-Fi.</li>
                    </ul>
                    <p>Ready to boost your balance further? Visit our Services tab and dive into our challenge - deciphering a Caesar cipher. This fun and educational challenge not only enhances your understanding of cryptographic techniques but can also increase your virtual balance!</p>
                    <a href="/StoutBank/earn.php" class="button">Click Here</a>
                </div>
            </div>

            <div class="mySlides fade">
                <div class="create-account-container full-screen">
                    <h2>Understanding Phishing Attacks and Keystroke Logging</h2>
                    <p>Phishing attacks are one of the most common methods used by cybercriminals to gain unauthorized access to personal information. These attacks often involve a malicious actor deceiving the victim into clicking on a link or opening an attachment that leads to a fake website.</p>
                    <p>In this challenge, you will experience firsthand how easy it is to fall for a phishing attack. A link will appear to take you to a Stout Bank login page. However, it's a simulation designed to demonstrate how such pages can capture your keystrokes.</p>
                    <h3>What are Keystroke Loggers?</h3>
                    <p>Keystroke logging, also known as keylogging, is a method used by attackers to record the keys struck on a keyboard. This data can capture everything from personal messages to passwords and credit card numbers, often without the user's knowledge.</p>
                    <h3>Your Challenge</h3>
                    <p>Click the link below to be taken to what seems like a Stout Bank login page. Any information you type there will be recorded into a file named <strong>"hidden.txt"</strong>. This exercise is safe and controlled, and is intended to educate you on the importance of verifying the authenticity of websites before entering sensitive information.</p>
                    <a href="/StoutBank/hidden.html" class="button">Go to Phishing Challenge</a>
                    <p><strong>Note:</strong> Always ensure you are on a legitimate site, especially when entering personal information. Check the URL carefully and look for signs of security such as a padlock symbol and 'https' in the address bar.</p>
                </div>
            </div>

            <div class="mySlides fade">
                <div class="create-account-container full-screen">
                    <h2>Understanding Encryption: From Caesar Cipher to SHA-256</h2>
                    <p>Encryption is a fundamental aspect of cybersecurity used to protect your data. Let’s explore how it started with simple methods like the Caesar Cipher and evolved to complex algorithms like SHA-256 used in password security today.</p>

                    <h3>What is a Caesar Cipher?</h3>
                    <p>The Caesar Cipher is one of the earliest known and simplest encryption techniques. It substitutes each letter in the plaintext with a letter some fixed number of positions down or up the alphabet. For example, with a shift of 1, 'A' would be replaced by 'B', 'B' would become 'C', and so on.</p>

                    <h3>Application in Learning:</h3>
                    <p>In our simulation, you can solve a puzzle using the Caesar Cipher. This hands-on approach helps you understand the basics of cryptographic substitutions and prepares you for more advanced concepts.</p>

                    <h3>Transition to SHA-256</h3>
                    <p>While the Caesar Cipher is a great educational tool, modern encryption methods use more complex algorithms to secure data. SHA-256, part of the SHA-2 family, is a cryptographic hash function widely used to secure passwords.</p>

                    <ul>
                        <li><strong>Hash Functions:</strong> Unlike the Caesar Cipher, SHA-256 is a one-way hash function that produces a fixed-size hash value from input data of any size. This is ideal for storing passwords because the original password cannot be easily deduced from the hash.</li>
                        <li><strong>Security Benefits:</strong> SHA-256 is resistant to collisions, meaning it is computationally impractical to find two different inputs that produce the same output hash, making it very secure for cryptographic purposes.</li>
                    </ul>

                    <p>When you create an account or change a password, Stout Bank uses SHA-256 to hash your passwords before storing them. This ensures that even if data breaches occur, your actual passwords remain protected.</p>

                    <p><strong>Remember:</strong> The evolution from simple ciphers like Caesar to advanced hashing functions like SHA-256 showcases the advancements in cryptographic technology aimed at enhancing security in the digital age.</p>
                    <a href="/StoutBank/earn.php" class="button">Go to Cipher Challenge</a>
                </div>
            </div>

            <div class="mySlides fade">
                <div class="create-account-container full-screen">
                    <h2>Understanding Web Inspections and Injection Attacks</h2>
                    <p>Learning how to inspect web elements is a crucial skill in both using and developing web applications. It allows users and developers to view the underlying HTML and JavaScript that make up a webpage. This is useful for debugging and educational purposes.</p>

                    <h3>Challenge: Finding the Secret Number</h3>
                    <p>In this challenge, you are tasked with inspecting the webpage to find a hidden number embedded within the HTML or JavaScript. This exercise simulates how developers often need to interact with the front-end (what you see) and the back-end (server-side operations) of web applications to ensure they are functioning correctly.</p>

                    <h3>How It Relates to Development</h3>
                    <p>The front-end of a web application involves everything the user interacts with directly, including the layout, design, and some client-side scripts. The back-end, however, handles data management, server logic, and authentication processes. Understanding how these two aspects interact is key to creating secure and efficient web applications.</p>

                    <h3>Introduction to Injection Attacks</h3>
                    <p>While the challenge in this simulation is simplistic—entering a number to access a page—it echoes the concept of an injection attack. In real scenarios, an attacker might inject malicious SQL, scripts, or other input that can manipulate or harm a system. Although this simulation just requires finding and entering a number, actual injection attacks can be complex and damaging.</p>

                    <p>In this simulation, the concept is simplified to help you understand how unauthorized commands or data can manipulate systems if proper security measures are not in place. It's important to understand that in real-life applications, validating and sanitizing input is crucial to prevent such attacks.</p>

                    <p>Remember, the skills you learn here are foundational, and they highlight the necessity of understanding both how applications are built and how they can be broken. This knowledge is vital for both protecting your own data and developing secure web solutions.</p>
                </div>
            </div>

            <div class="mySlides fade">
                <div class="create-account-container full-screen">
                    <h2>Thank You for Participating!</h2>
                    <p>We hope you found the Stout Bank Cybersecurity Simulation enlightening and engaging. Congratulations on successfully navigating through the challenges and learning important aspects of cybersecurity along the way.</p>
                    <p>Your journey through this simulation has equipped you with valuable knowledge that can help safeguard your digital information and enhance your understanding of web security.</p>
                    <h3>Congratulations!</h3>
                    <p>By participating in this simulation, you've taken a significant step towards understanding the complexities of cybersecurity and the importance of vigilance in the digital age.</p>
                    <p>We thank you for your time and effort in completing this simulation. We encourage you to continue exploring and learning about cybersecurity to further your skills and knowledge.</p>
                    <a href="finalpage.php" class="button">Proceed to Final Page</a>
                </div>
            </div>

            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
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
        // Define slideIndex globally
        var slideIndex = <?php echo isset($_GET['slide']) ? (int) $_GET['slide'] : 1; ?>;

        // Move plusSlides and showSlides outside the DOMContentLoaded listener
        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            if (n > slides.length) { slideIndex = 1; }  // Wrap around to the first slide
            if (n < 1) { slideIndex = slides.length; } // Wrap around to the last slide
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";  // Hide all slides
            }
            slides[slideIndex - 1].style.display = "block";  // Show only the active slide
        }

        // Listen for the DOMContentLoaded event, then display the initial slide
        document.addEventListener('DOMContentLoaded', function () {
            showSlides(slideIndex);
        });
    </script>

</body>
</html>
