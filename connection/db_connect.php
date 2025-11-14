<?php
$servername = "localhost";
$username = "root";     // Change if you use a different MySQL user
$password = "munyoiks7";         // Add your password if set
$database = "school";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
