<?php
include 'dbh.php';
date_default_timezone_set('America/New_York');

$username = $_POST['uid'];
$pwd = password_hash($_POST['pwd'], PASSWORD_BCRYPT, array("cost" => 10));
$email = $_POST['email'];
$org = $_POST['org'];
$org = ucwords($org);
$org = str_replace(' ', '', $org);
$idFormat = $_POST['id-format'];
$school = $_POST['school'];
$school = ucwords($school);
$school = str_replace(' ', '', $school);
$date = date("m/d/Y @ g:ia");


$sql = "INSERT INTO user(org, uid, pwd, idFormat, email, school, lastLogin) VALUES ('$org', '$username', '$pwd', '$idFormat', '$email', '$school', '$date')";
$result = mysqli_query($conn, $sql);
if ($result) {
    $sql = "CREATE TABLE bannedList_{$org}_{$school} (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, uid VARCHAR(30) NOT NULL)";
    $result2 = mysqli_query($conn, $sql);
    if ($result2) {
        $sql = "CREATE TABLE partyList_{$org}_{$school} (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, eventTitle VARCHAR(255), count INT(11) NOT NULL, eventDate VARCHAR(30) NOT NULL)";
        $result3 = mysqli_query($conn, $sql);
        if ($result3) {
            header("Location: ../index.php");
        } else {
            echo 'Error in creating table 2/2. Please contact ' . "<a href='mailto:zcopland16@gmail.com'>Zach Copland</a>.";
        }
        
    } else {
        echo 'Error in creating table 1/2. Please contact ' . "<a href='mailto:zcopland16@gmail.com'>Zach Copland</a>.";
    }
    
} else {
    echo "Failed to process this request. Please go back and try to submit again.";
}




?>
