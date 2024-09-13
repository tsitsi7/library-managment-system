<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="stylesheet.css">
    <title>Login Page</title>
</head>

<body>
    <div>
        <form action="" method="post" id="login">
            <div class="login">
                <h3>Welcome back!</h3>
                <div class="header">
                    <img src="user-icon.png" alt="user icon">
                </div>
                <label for="username">Username:</label>
                <br>
                <input type="text" id="username" name="username" minlength="3" maxlength="30" required>

                <label for="password">Password:</label>
                <br>
                <input type="password" id="password" name="password" minlength="3" maxlength="30" required>

                <button type="submit">Login</button>
                <a href="register.php">Don't have an account? Register</a>
            </div>
        </form>
        <?php
        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbname = "librarydb";

        $conn = new mysqli($host, $user, $pass, $dbname);

        // Check connection
        if ($conn->connect_error) {
            echo "Could not connect to server\n";
            die("Connection failed: " . $conn->connect_error);
        }
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = htmlspecialchars($_POST["username"]);
            $password = htmlspecialchars($_POST["password"]);

            //form validations: 
            if (empty($username) || empty($password)) {
                echo "<p> Both username and password are required.";
            } else {
                $checkUser = "SELECT user_id, username, user_password FROM user WHERE username = '$username'";
                $resultUser = $conn->query($checkUser);
                //verification
                if ($resultUser->num_rows > 0) {
                    $row = $resultUser->fetch_assoc();
                    $user_password = $row["user_password"];

                    if ($password === $user_password) {
                        $_SESSION['user_id'] = $row["user_id"];
                        $_SESSION['username'] = $row["username"];
                        echo "<p class='green'> Login successful!";
                        header("refresh:3;url=dashboard.php"); //url=dashboard.php
                    } else {
                        echo "<p class='red'> Invalid password.";
                    }
                } else {
                    echo "<p class='red'> User not found. Please check your username.";
                }
            }
        }
        ?>
    </div>
</body>

</html>