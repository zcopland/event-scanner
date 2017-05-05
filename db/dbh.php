<?php
$conn = mysqli_connect("localhost", "root", "", "event-scanner");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>