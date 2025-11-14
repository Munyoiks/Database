<?php
include 'connection/db_connect.php';

$first = $_POST['first_name'];
$last = $_POST['last_name'];
$dob = $_POST['date_of_birth'];
$gender = $_POST['gender'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$course = $_POST['course_id'];
$parent = $_POST['parent_name'];
$parent_phone = $_POST['parent_phone'];
$status = $_POST['status_of_student'];

$sql = "INSERT INTO students 
(first_name, last_name, date_of_birth, gender, phone, email, course_id, parent_name, parent_phone, status_of_student)
VALUES 
('$first', '$last', '$dob', '$gender', '$phone', '$email', '$course', '$parent', '$parent_phone', '$status')";

if ($conn->query($sql) === TRUE) {
    header("Location: students.php?success=1");
} else {
    echo "Error: " . $conn->error;
}
