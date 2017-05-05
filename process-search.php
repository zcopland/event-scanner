<?php
include 'db/dbh.php';

$operator = $_POST['operation'];
$student = $_POST['studentID'];
$student = stripslashes($student);
$school = $_POST['school'];
$org = $_POST['org'];


switch ($operator) {
    case 'Check':
        //echo "ID: {$student}.<br />";
        checkBanned($student);
        break;
    case 'Ban':
        //echo "ID: {$student}.<br />";
        addBan($student);
        break;
    case 'UnBan':
        //echo "ID: {$student}.<br />";
        unban($student);
        break;
    case 'Banned Students':
        listBanned();
        break;
    default:
        //echo 'Error';
        break;
}


function addBan($studentID) {
    global $org, $school, $conn;
    if (check($studentID)) {
        echo false; //student is already banned
    } else {
        $sql = "INSERT INTO bannedList_{$org}_{$school} (uid) VALUES ('{$studentID}')";
        $result = mysqli_query($conn, $sql);
        echo true; //student is now banned
    }

}
function listBanned() {
    global $conn, $school, $org;
    $query = "SELECT * FROM bannedList_{$org}_{$school};";
    $result = mysqli_query($conn, $query);
    $text = <<<TEXT
    <fieldset class="bannedList">
      <legend>Banned List</legend>
      <br/>
      <ul>
TEXT;
    while ($row = mysqli_fetch_assoc($result)) { 
        $text .= "<li>" . $row['uid'] . "</li>";
    }
    $text .= '</ul>';
    $text .= '</fieldset>';
    echo $text;
}
function checkBanned($studentID) {
    if (check($studentID)) {
        //student is banned
        echo true;
/*
        echo '<body style="background-color: #FF0000">';
        echo 'Student is BANNED.';
*/
    }
    //student is not in array
     else {
        //student is not banned
        echo false;
/*
        echo '<body style="background-color: #009900">';
        echo 'Student is allowed.';
*/
    }
}
function unban($studentID) {
    global $conn, $school, $org;
    if (check($studentID)) {
        $query = "DELETE FROM bannedList_{$org}_{$school} WHERE uid = '{$studentID}';";
        $result = mysqli_query($conn, $query);
        if ($result) {
            echo true;
            //echo 'Success. Student was removed from banned list.';
        } else {
            echo false;
            //echo 'Error removing student from the banned list.';
        }   
    }
    /* student was never banned */
    else {
        echo false;
/*
        echo '<body style="background-color: #009999">';
        echo "{$studentID} was never banned.";
*/
    }

}

function check($studentID) {
    global $org, $school, $conn;
    $query = "SELECT * FROM bannedList_{$org}_{$school} WHERE uid = '{$studentID}';";
    $result = mysqli_query($conn, $query);
    $rows = mysqli_num_rows($result);
    if ($rows > 0) {
        return true; //found them, they are banned
    } else {
        return false; //didn't find them
    }
}

?>