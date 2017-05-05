<?php
session_start();
$_SESSION['loggedIn'] = false;
date_default_timezone_set('America/New_York');
include 'dbh.php';

$uid = $_POST['uid'];
$pwd = $_POST['pwd'];

$sql = "SELECT * FROM user WHERE uid='{$uid}'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if (password_verify($pwd, $row['pwd'])) {
    //correct
    $date = date("m/d/Y @ g:ia");
    $sql = "UPDATE user SET lastLogin='{$date}' WHERE uid='{$uid}'";
    $result = mysqli_query($conn, $sql);
    $_SESSION['loggedIn'] = true;
    $_SESSION['incorrect'] = false;
    $_SESSION['org'] = $row['org'];
    $_SESSION['id'] = $row['id'];
    header("Location: ../main.php");
} else {
    //incorrect
    $_SESSION['loggedIn'] = false;
    $_SESSION['incorrect'] = true;
    header("Location: ../index.php");
}
?>