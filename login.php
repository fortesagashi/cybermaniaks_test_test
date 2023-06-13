<?php
session_start();

// including the db connection file
require_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // retrieving the typed username and password from user
    $username = $_POST["username"];
    $password = $_POST["password"];

    //checking the username and password in database
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        // user logged in succesfully if rows are returned
        echo "Login successful!";
        $_SESSION["username"] = $username;
        //redirecting to main page
        header("Location: main.php");
        exit();
    } else {
        //invalid credentials
        echo "Invalid username or password.";
    }
}
?>

<h2>Login</h2>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Login">
</form>
