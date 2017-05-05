<?php
session_start();
if (!isset($_SESSION['org'])  || empty($_SESSION['org'])) {
    header('Location: index.php');
}
//used in debugging
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);

include 'db/dbh.php';

/* vars */
$bannedListArray = [];
$indexPath = 0;
$student = '';
$operator = '';
$idFormat = '';
$school = '';
$email = '';
$org = '';
$isAdmin = false;

$id = $_SESSION['id'];
$sql = "SELECT * FROM user WHERE id='$id'";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $idFormat = $row['idFormat'];
    $school = $row['school'];
    $org = $row['org'];
    $email = $row['email'];
}

if ($org != 'Admin' && $org != 'admin') {
    $query = "SELECT * FROM bannedList_{$org}_{$school}";
    //create select query
    $shouts = mysqli_query($conn, $query);
    //listing it
    while ($row = mysqli_fetch_assoc($shouts)) { 
        $bannedListArray[$indexPath] = $row['uid'];
        $indexPath++;
    }
} else if ($org == 'Admin' || $org == 'admin') {
    $isAdmin = true;
/*
    //handling admin actions
    $FOLDERPATH = 'Lists/';
    $folder_contents = scandir($FOLDERPATH);
    $count = count($folder_contents);
    $i = 2;
    while ($i < $count) {
        echo 'File ' . ($i - 1) . ': ' . $folder_contents[$i] . "<br/>";
        $i++;
    }
*/
}
if (isset($_POST['studentID']) && isset($_POST['operation'])) {
    //getting values from textfield and selector
    $student = $_POST['studentID'];
    $student = stripslashes($student);
    $operator = $_POST['operation'];

    //Checking to see if ID is long enough
    if (strlen($student) >= $idFormat || strlen($student) == 0) {
        //long enough
        switch ($operator) {
            case 'Check':
                echo "ID: {$student}.<br />";
                checkBanned($student);
                break;
            case 'Ban':
                echo "ID: {$student}.<br />";
                addBan($student);
                break;
            case 'UnBan':
                echo "ID: {$student}.<br />";
                unban($student);
                break;
            case 'Banned Students':
                listBanned();
                break;
            default:
                echo 'Error';
                break;
        }
    }
    elseif ($student == '*') {
        //do nothing
        listBanned();
    }
    else {
        //not long enough
        echo 'Error. Student ID is invalid.';
    }

}
//no values to studentID or operator yet
else {
    //echo 'YIKES';
}

function addBan($studentID) {
    //getting global array
    global $bannedListArray;

    if (in_array($studentID, $bannedListArray)) {
        echo 'Error. Student is already banned.';
    }
    elseif ($studentID == '800-62-8606') {
        echo 'Error. Cannot ban creator.';
    }
    else {
        global $org, $conn;
        $sql = "INSERT INTO bannedList_" . $org . "(uid) VALUES ('$studentID')";
        $result = mysqli_query($conn, $sql);
    }

}
function listBanned() {
    global $indexPath, $query, $conn;
    $shouts = mysqli_query($conn, $query);
    $indexPath = 0;
    echo <<<TEXT
    <fieldset class="bannedList">
      <legend>Banned List</legend>
      <br/>
      <ul>
TEXT;
    while ($row = mysqli_fetch_assoc($shouts)) { 
        echo '<li>' . $row['uid'] . "</li>";
        $indexPath++;
    }
    echo '</ul>';
    echo '</fieldset>';
}
function checkBanned($studentID) {
    //declaring global
    global $bannedListArray;
    //checking to see if student is in the array
    if (in_array($studentID, $bannedListArray)) {
        echo '<body style="background-color: #FF0000">';
        echo 'Student is BANNED.';
    }
    //student is not in array
     else {
        echo '<body style="background-color: #009900">';
        echo 'Student is allowed.';
    }
}
function unban($studentID) {
    /* declaring globals */
    global $bannedListArray, $FILEPATH, $org, $conn;
    /* checking to see if $studentID is already banned */
    if (in_array($studentID, $bannedListArray)) {
        $sql = "DELETE FROM bannedList_" . $org . " WHERE uid='$studentID'";
        $result = mysqli_query($conn, $sql);
        echo 'Success. Student was removed from banned list.';
    }
    /* student was not banned */
    else {
        echo '<body style="background-color: #009999">';
        echo "{$studentID} was never banned.";
    }

}

?>
<!DOCTYPE html>
<html>
<head>
    <?php if (isset($_SESSION['org'])) { ?>
    <title>Event Scanner - <?php echo $_SESSION['org']; ?></title>
    <?php } ?>
    <link rel= "stylesheet" type= "text/css" href= "style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>
<body>
    <div class="searchForm">
    <h1 class="appName">Event ID Checker</h1>
    <div class="j">
    <form method="POST">
        <label for="studentID">Student ID : </label>
        <input type="text" name="studentID" id="studentID" placeholder="800-XX-XXXX" autofocus="true">
        <select name="operation" onchange="getValue(this)">
            <option>Check</option>
            <option>Ban</option>
            <option>UnBan</option>
            <option id="listBanned">Banned Students</option>
        </select>
        <button name="submit" value="submit" type="submit">GO</button>
    </form>
<!-- PHP FOR ADMIN STUFF -->
<?php
if ($isAdmin) {
	echo <<<HTML
<br/>
<div class="contianer">
<button class="btn vermillion-bg" type="submit" id="showUsers" name="showUsers">Show Users</button>
</div>
<br/><br/>
HTML;

	$query = "SELECT * FROM user";
	$result = mysqli_query($conn, $query);
	echo "<div id=\"users-list\" class=\"container table-responsive\"><table class='table table-hover'><tr><th>Username</th><th>Email</th><th>School</th><th>Org</th><th>Last Login</th></tr>";
	while ($row = mysqli_fetch_assoc($result)) { 
		echo <<<TEXT
<tr>
  <td>{$row["uid"]}</td>
  <td>{$row['email']}</td>
  <td>{$row['school']}</td>
  <td>{$row['org']}</td>
  <td>{$row['lastLogin']}</td>
TEXT;
    }
    echo "</tr></table></div>";

} 
?>
<!-- END OF PHP FOR ADMIN STUFF -->
    </div>
    </div>
    <?php echo <<<HTML
    <input type="hidden" id="idFormat" value="$idFormat" />
    <input type="hidden" id="email" value="$email" />
    <input type="hidden" id="org" value="$org" />  
    <input type="hidden" id="school" value="$school" />   
HTML;
    ?>
    <form action="logout.php">
        <button class="cornerBtn" type="submit">Logout</button>
    </form>
    <script type='text/javascript'>
        /* variables */
        var idTextfield = document.getElementById('studentID');
        var idFormat = $('#idFormat').val();
        var school = $('#school').val();
        var email = $('#email').val();
        var org = $('#org').val();
        
        $('#users-list').hide();
        //toggling the employee list
        $('#showUsers').click(function() {
            $('#users-list').toggle(1000);
        });

        /* setting the textfield to be selected */
        idTextfield.select();

        /* detecting which option is selected */
        function getValue(sel) {
            idTextfield.select();
            if (sel.value == 'Banned Students') {}
            else {}
        }
        /* use AJAX every time someone checks an ID */
        $.ajax({type: "POST", url: "find-id.php", data: {username: username}, success: function(result){
            if (result) {
                us_allowed.show();
                sbtn.show();
                us_okay = true;
            } else {
                us_taken.show();
                sbtn.hide();
                us_okay = false;
            }
        }});
    </script>
</body>
</html>