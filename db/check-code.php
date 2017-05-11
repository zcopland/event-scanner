<?php
include 'dbh.php';

$username = $_POST['username'];
$code = $_POST['code'];

$sql = "SELECT * FROM `ver_codes` WHERE `code`={$code}";
$result = mysqli_query($conn, $sql);
$rows = mysqli_num_rows($result);

if ($rows > 0) {
    //code was found
    if ($rows['used'] == 0) {
        //code has not been used
        $sql = "UPDATE `ver_codes` SET `used`=1, `usedBy`='{$username}' WHERE `code`={$code};";
        $result2 = mysqli_query($conn, $sql);
        if ($result2) {
            echo true;
        } else {
            echo false;
        }
    } else {
        //code has been found but already used
        echo false;
    }
    
} else if ($rows <= 0) {
    //code was not found
    echo false;
}

?>