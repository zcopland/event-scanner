<?php
session_start();
date_default_timezone_set('America/New_York');
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
$FORMAT = '';
$SCHOOL = '';
$EMAIL = '';
$ORG = '';
$isAdmin = false;
$date = date('n/j/y');
$month = date('M');
$data = 'data';
$labels = 'labels';

$id = $_SESSION['id'];
$sql = "SELECT * FROM user WHERE id='$id'";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $FORMAT = $row['idFormat'];
    $SCHOOL = $row['school'];
    $ORG = $row['org'];
    $EMAIL = $row['email'];
}

if ($ORG != 'Admin' && $ORG != 'admin') {
    $query = "SELECT * FROM bannedList_{$ORG}_{$SCHOOL}";
    //create select query
    $shouts = mysqli_query($conn, $query);
    //listing it
    while ($row = mysqli_fetch_assoc($shouts)) { 
        $bannedListArray[$indexPath] = $row['uid'];
        $indexPath++;
    }
} else if ($ORG == 'Admin' || $ORG == 'admin') {
    $isAdmin = true;
}


?>

<!-- Start of HTML -->
<!DOCTYPE html>
<html>
<head>
    <?php if (isset($_SESSION['org'])) { ?>
    <title>Event Scanner - <?php echo $_SESSION['org']; ?></title>
    <?php } ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <link rel= "stylesheet" type= "text/css" href= "style.css">
</head>
<body>
    <div class="searchForm">
    <h1 class="appName black-color">Event ID Checker</h1>
<?php 
    if (!empty($ORG)) {
        $temp = preg_replace('/(?<!^)([A-Z])/', ' \\1', $ORG);    
        echo "<h2 class='black-color center'>{$temp}</h2>";
    }
    if (!empty($SCHOOL)) {
        $temp = preg_replace('/(?<!^)([A-Z])/', ' \\1', $SCHOOL);
        echo "<h3 class='black-color center'>{$temp}</h3>";
    }
?>  
    <div class="j container">
        <div id="eventCounterDiv" class="center">
            <div class="row">
                <label for="eventTitle">Event title : </label>
            </div>
            <div class="row">
                <?php echo "<input id='eventTitle' name='evenTitle' placeholder='{$month} House Party' />"; ?>
                <button id="createEvent" class="btn btn-sm btn-success" name="createEvent">Create</button>
            </div><br/>
            <div class="row">
                <div class="col-sm-8">
                    <button id="pauseEvent" class="btn btn-sm btn-warning" name="pauseEvent">Pause</button>
                    <button id="resumeEvent" class="btn btn-sm btn-info" name="resumeEvent">Resume</button>
                </div>
                <div class="col-sm-4">
                    <button id="stopEvent" class="btn btn-sm btn-danger" name="stopEvent">Stop</button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 right-align">
                    <h4 id="count-h4">Count: </h4>
                </div>
                <div class="col-sm-6 left-align">
                    <h4 id="counter"></h4>
                </div>
            </div>
            <div class="row">
                <div class="center">
                    <button id="listEvents" class="btn btn-sm btn-default" name="listEvents">List Events</button>
                </div>
            </div>
        </div>
        <!-- Modal -->
      <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Attention</h4>
            </div>
            <div class="modal-body">
              <p id="modal-text">TEXT</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Dismiss</button>
            </div>
          </div>
          
        </div>
      </div>
      <!-- /Modal-->
        <form method="POST">
            <label class='black-color' for="studentID">Student ID : </label>
            <input type="text" name="studentID" id="studentID" placeholder="800-XX-XXXX" autofocus="true">
            <select name="operation" id="operation" onchange="getValue(this)">
                <option>Check</option>
                <option>Ban</option>
                <option>UnBan</option>
                <option id="listBanned">Banned Students</option>
            </select>
            <button name="submit" class="btn-styled" value="submit" type="button" onclick="process()">GO</button>
        </form>
        <div id="status" class="center black-color"></div>
        <div id="bannedList" class="center black-color"></div>
<!-- PHP FOR ADMIN STUFF -->
<?php
if ($isAdmin) {
	echo <<<HTML
<br/>
<div class="contianer">
<button class="btn vermillion-bg" type="submit" id="showUsers" name="showUsers">Show Users</button>
<button class="btn vermillion-bg" id="showCodes" name="showCodes">Verification Codes</button>
</div>
<br/><br/>
HTML;

	$query = "SELECT * FROM user";
	$result = mysqli_query($conn, $query);
	echo "<div id=\"users-list\" class=\"container table-responsive\"><table class='table table-hover'><tr><th>Username</th><th>Email</th><th>School</th><th>Org</th><th>Last Login</th></tr>";
	while ($row = mysqli_fetch_assoc($result)) { 
		echo <<<TEXT
<tr>
  <td>{$row['uid']}</td>
  <td>{$row['email']}</td>
  <td>{$row['school']}</td>
  <td>{$row['org']}</td>
  <td>{$row['lastLogin']}</td>
TEXT;
    }
    echo "</tr></table></div>";

$sql = "SELECT * FROM ver_codes";
$result = mysqli_query($conn, $sql);
echo <<<TEXT
<input id='labels' type='hidden' value='{$labels}' />
<input id='data' type='hidden' value='{$data}' />
TEXT;
echo "<div id=\"codes-list\" class=\"container table-responsive\"><table class='table table-hover'><tr><th>Code</th><th>Used?</th><th>usedBy</th></tr>";
while ($row = mysqli_fetch_assoc($result)) { 
	echo <<<TEXT
<tr>
<td>{$row['code']}</td>
<td>{$row['used']}</td>
<td>{$row['usedBy']}</td>
TEXT;
}
echo "</tr></table></div>";

} 
?>
<!-- END OF PHP FOR ADMIN STUFF -->
    </div>
    </div>
    <?php echo <<<HTML
    <input type="hidden" id="idFormat" value="$FORMAT" />
    <input type="hidden" id="email" value="$EMAIL" />
    <input type="hidden" id="org" value="$ORG" />  
    <input type="hidden" id="school" value="$SCHOOL" />   
HTML;
    ?>
    <form action="db/logout.php">
        <button class="cornerBtn btn-styled" type="submit">Logout</button>
    </form>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script>
    <script type="text/javascript" src="chart.js"></script>
    <script type="text/javascript" src="script.js"></script>
</body>
</html>