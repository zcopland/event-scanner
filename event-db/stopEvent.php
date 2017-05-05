<?php
include '../db/dbh.php';
date_default_timezone_set('America/New_York');

$title = $_POST['title'];
$org = $_POST['org'];
$org = ucwords($org);
$org = str_replace(' ', '', $org);
$school = $_POST['school'];
$school = ucwords($school);
$school = str_replace(' ', '', $school);
$date = date("m/d/Y");

$sql = "SELECT * FROM `partyList_{$org}_{$school}` WHERE inProgress=TRUE AND eventTitle='{$title}' AND eventDate='{$date}';";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if ($row > 0) {
    $id = $row['id'];
    $sql = "UPDATE partyList_{$org}_{$school} SET inProgress=FALSE WHERE id={$id};";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo true;
    } else {
        echo 'Error stoping event in DB!';
    }
} else if ($row <= 0) {
    echo 'Error finding row!';
}

?>