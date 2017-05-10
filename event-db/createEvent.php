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
    $sql = "SELECT * FROM `partyList_{$org}_{$school}` WHERE `eventTitle`='{$title}' AND `eventDate`='{$date}';";
    $result2 = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result2);
    if ($row > 0) {
        /* eventTitle & eventDate exist, do not insert into table */
        echo "Error. Event title: '{$title}' on today's date ({$date}) already exists!";
    } else if ($row <= 0) {
        /* eventTitle & eventDate do not exist, insert into table */
        $sql = "INSERT INTO `partyList_{$org}_{$school}`(`eventTitle`, `count`, `eventDate`, `inProgress`) VALUES ('{$title}', 0, '{$date}', TRUE);";
        $result3 = mysqli_query($conn, $sql);
        if ($result3) {
            echo true;
        } else {
            echo 'Error creating event!';
        }
    }
} else {
    echo 'Error setting all events to false!';
}





?>