<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="stylesheet.css">
    <title>Registration Page</title>
</head>

<body>
    <div>
        <form action="" method="post" id="login">
            <div class="login">
                <h3>Register</h3>
                <div class="header">
                    <img src="user-icon.png" alt="user icon">
                </div>

                <label for="name">First Name:</label>
                <br>
                <input type="text" id="name" name="name" minlength="3" maxlength="25" required>

                <label for="address">Address:</label>
                <br>
                <input type="text" id="address" name="address" minlength="3" maxlength="50" required>

                <label for="phonenumber">Phone Number:</label>
                <br>
                <input type="number" id="phonenumber" name="phonenumber" length="10" required>

                <label for="email">email:</label>
                <br>
                <input type="text" id="email" name="email" length="10" required>

                <label for="username">Username:</label>
                <br>
                <input type="text" id="username" name="username" minlength="3" maxlength="30" required>

                <label for="password">Password:</label>
                <br>
                <input type="password" id="password" name="password" minlength="3" maxlength="30" required>

                <label for="confirm">Confirm Password:</label>
                <br>
                <input type="password" id="confirm" name="confirm" minlength="3" maxlength="30" required>

                <button type="submit">Create Account</button>
                <a href="login.html">Already have an account? Login</a>
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
        } else {
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = htmlspecialchars($_POST["name"]);
                $address = htmlspecialchars($_POST["address"]);
                $phonenumber = htmlspecialchars($_POST["phonenumber"]);
                $username = htmlspecialchars($_POST["username"]);
                $email = htmlspecialchars($_POST["email"]);
                $password = htmlspecialchars($_POST["password"]);
                $confirm = htmlspecialchars($_POST["confirm"]);

                //form validations: 
                if (empty($name) || empty($address) || empty($phonenumber) || empty($username) || empty($email) || empty($password) || empty($confirm)) {
                    echo "<p class='red'> All fields are required.";
                } elseif ($password !== $confirm) {
                    echo "<p class='red'> Password and Confirm Password do not match.";
                } else {
                    $checkUser = "SELECT username FROM user WHERE username = '$username'";
                    $checkEmail = "SELECT email  FROM user WHERE email = '$email'";
                    $resultUser = $conn->query($checkUser);
                    $resultEmail = $conn->query($checkEmail);

                    if ($resultUser->num_rows > 0) {
                        echo "<p class='red'> Username already taken, Please choose another.";
                    } elseif ($resultEmail->num_rows > 0) {
                        echo "<p class='red'> Email is already registered. Please use another email";
                    } else {
                        $insertUser = "INSERT INTO user (name,u_address, phonenumber,username, email, user_password) VALUES ('$name', '$address', '$phonenumber', '$username', '$email', '$password')";
                        if ($conn->query($insertUser) === TRUE) {
                            $_SESSION['user_id'] = $conn->insert_id;
                            $_SESSION['username'] = $username;
                            echo "<p class='green'> Registration successful!";
                            header("refresh:3;url=login.php");
                        } else {
                            echo "Error: " . $insertUser . $conn->error;
                        }
                    }
                }
            }
        }
        $conn->close();
        ?>
    </div>
</body>

</html>