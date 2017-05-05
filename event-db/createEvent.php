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

$sql = "UPDATE partyList_{$org}_{$school} SET inProgress=FALSE WHERE inProgress=TRUE;";
$result = mysqli_query($conn, $sql);

if ($result) {
    $sql = "INSERT INTO `partyList_{$org}_{$school}`(`eventTitle`, `count`, `eventDate`, `inProgress`) VALUES ('{$title}', 0, '{$date}', TRUE);";
    $result2 = mysqli_query($conn, $sql);
    if ($result2) {
        echo true;
    } else {
        echo 'Error creating event!';
    }
} else {
    echo 'Error setting all events to false!';
}





?>