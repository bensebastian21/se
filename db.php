<?php
$servername = "localhost";
$username = "root";  // Adjust based on your MySQL configuration
$password = "";  // Adjust as necessary
$dbname = "notes_app";  // Change to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
