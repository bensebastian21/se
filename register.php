<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hashing the password

    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Registration successful! <a href='login.php'>Login here</a></p>";
    } else {
        echo "<p class='error'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js" defer></script>
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
        <form action="register.php" method="POST" onsubmit="return validateRegister()">
            <input type="text" name="username" id="username" placeholder="Username" required><br>
            <input type="email" name="email" id="email" placeholder="Email" required><br>
            <input type="password" name="password" id="password" placeholder="Password" required><br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
