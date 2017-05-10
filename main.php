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
    if (!empty($org)) {
        $temp = preg_replace('/(?<!^)([A-Z])/', ' \\1', $org);    
        echo "<h2 class='black-color center'>{$temp}</h2>";
    }
    if (!empty($school)) {
        $temp = preg_replace('/(?<!^)([A-Z])/', ' \\1', $school);
        echo "<h3 class='black-color center'>{$temp}</h3>";
    }
?>  
    <div class="j container">
        <div id="eventCounterDiv" class="center">
            <div class="row">
                <label for="eventTitle">Event title : </label>
            </div>
            <div class="row">
                <input id="eventTitle" name="evenTitle" placeholder="Spring Fling 2017" />
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
    <form action="db/logout.php">
        <button class="cornerBtn btn-styled" type="submit">Logout</button>
    </form>
    <script type='text/javascript'>
        /* variables */
        var idTextfield = document.getElementById('studentID');
        var studentID;
        var operation;
        var idFormat = $('#idFormat').val();
        var school = $('#school').val();
        var email = $('#email').val();
        var org = $('#org').val();
        var red = '#FF0000';
        var green = '#00FF00';
        var normal = '#CAEBF2';
        var paused = false;
        var eventStart = false;
        var counter = 0;
        
        $('#resumeEvent').hide();
        $('#stopEvent').hide();
        $('#pauseEvent').hide();
        $('#count-h4').hide();
        
        $('#users-list').hide();
        //toggling the employee list
        $('#showUsers').click(function() {
            $('#users-list').toggle(1000);
        });
        
        /* this will process form w/o reloading page */
        function process() {
            studentID = $('#studentID').val();
            operation = $('#operation').val();
            $('#bannedList').hide();
            $('#status').hide();
            switch (operation) {
                case 'Check':
                    if (checkLength()) {
                        /* use AJAX every time someone checks an ID */
                        $.ajax({type: "POST", url: "process-search.php", data: {
                            studentID: studentID,
                            operation: operation,
                            school: school,
                            org: org
                        }, success: function(result){
                            if (result) {
                                //banned
                                changeBGColor(red);
                                $('#status').empty()
                                $('#status').append('Student is BANNED.');
                                $('#status').show();
                            } else {
                                //not banned
                                changeBGColor(green);
                                $('#status').empty()
                                $('#status').append('Student is allowed.');
                                $('#status').show();
                                if (paused == false && eventStart == true) {
                                    var title = $('#eventTitle').val();
                                    $.ajax({type: "POST", url: "event-db/updateCounter.php", data: {
                                        title: title,
                                        school: school,
                                        org: org
                                    }, success: function(result){
                                        if ($.isNumeric(result)) {
                                            //update counter
                                            counter = result;
                                            $('#counter').html(counter);
                                        } else {
                                            $('#modal-text').html(result);
                                            $("#myModal").modal();
                                        }
                                    }});
                                }
                            }
                        }});
                    }
                    break;
                case 'Ban':
                    if (checkLength()) {
                        /* use AJAX every time someone checks an ID */
                        $.ajax({type: "POST", url: "process-search.php", data: {
                            studentID: studentID,
                            operation: operation,
                            school: school,
                            org: org
                        }, success: function(result){
                            if (result == true) {
                                //student has been banned
                                changeBGColor(normal);
                                $('#status').empty()
                                $('#status').append('Student has been banned.');
                                $('#status').show();
                            } else {
                                //student is already banned
                                changeBGColor(normal);
                                $('#status').empty()
                                $('#status').append('Student is already banned.');
                                $('#status').show();
                            }
                        }});
                    }
                    break;
                case 'UnBan':
                    if (checkLength()) {
                        /* use AJAX every time someone checks an ID */
                        $.ajax({type: "POST", url: "process-search.php", data: {
                            studentID: studentID,
                            operation: operation,
                            school: school,
                            org: org
                        }, success: function(result){
                            if (result == true) {
                                //student was removed from list
                                changeBGColor(normal);
                                $('#status').empty()
                                $('#status').append('Student has been removed from list.');
                                $('#status').show();
                            } else {
                                //student was not banned
                                changeBGColor(normal);
                                $('#status').empty()
                                $('#status').append('Student was not banned.');
                                $('#status').show();
                            }
                        }});
                    }
                    break;
                case 'Banned Students':
                    /* use AJAX every time someone checks an ID */
                        $.ajax({type: "POST", url: "process-search.php", data: {
                            studentID: studentID,
                            operation: operation,
                            school: school,
                            org: org
                        }, success: function(result){
                            changeBGColor(normal);
                            $('#bannedList').empty();
                            $('#bannedList').show();
                            $('#bannedList').append(result);
                        }});
                    break;
                default:
                    break;
            }
        }
        
        /* check the length of ID */
        function checkLength() {
            studentID = $('#studentID').val();
            len = studentID.length;
            if (len == idFormat) {
                return true;
            } else {
                changeBGColor(normal);
                $('#modal-text').html('Check length of ID!');
                $("#myModal").modal();
                return false;
            }
        }
        
        /* change background color */
        function changeBGColor(color) {
            document.body.style.background = color;
        }

        /* setting the textfield to be selected */
        idTextfield.select();
        
        /* create event button */
        $('#createEvent').click(function() {
            var title = $('#eventTitle').val();
            if (title.length >= 5) {
                $.ajax({type: "POST", url: "event-db/createEvent.php", data: {
                    title: title,
                    school: school,
                    org: org
                }, success: function(result){
                    if (result == true) {
                        $('#count-h4').show();
                        $('#createEvent').hide();
                        $('#stopEvent').show();
                        $('#pauseEvent').show();
                        $('#listEvents').hide();
                        $('#eventTitle').prop('disabled', true);
                        $('#counter').html(counter);
                        eventStart = true;
                    } else {
                        $('#modal-text').html(result);
                        $("#myModal").modal();
                        $('#eventTitle').prop('disabled', false); 
                    }
                }});
            } else {
                $('#modal-text').html('Event title is too short!');
                $("#myModal").modal();
            }
            console.log('event start = ' + eventStart);
        });
        /* Pause event button */
        $('#pauseEvent').click(function() {
            $('#resumeEvent').show();
            $(this).hide();
            paused = true;
        });
        /* Resume event button */
        $('#resumeEvent').click(function() {
            $('#pauseEvent').show();
            $(this).hide();
            paused = false;
        });
        /* Stop event button */
        $('#stopEvent').click(function() {
            var title = $('#eventTitle').val();
            $.ajax({type: "POST", url: "event-db/stopEvent.php", data: {
                title: title,
                school: school,
                org: org
            }, success: function(result){
                if (result == true) {
                    $('#count-h4').hide();
                    $('#createEvent').show();
                    $('#stopEvent').hide();
                    $('#eventTitle').prop('disabled', false);
                    $('#counter').html('');
                    $('#eventTitle').val('');
                    $('#pauseEvent').hide();
                    $('#listEvents').show();
                    eventStart = false;
                } else {
                    $('#modal-text').html(result);
                    $("#myModal").modal();
                    $('#eventTitle').prop('disabled', true); 
                }
            }});
        });
        $('#listEvents').click(function() {
            var title = $('#eventTitle').val();
            $.ajax({type: "POST", url: "event-db/listEvents.php", data: {
                school: school,
                org: org
            }, success: function(result){
                if (result != false) {
                    $('#modal-text').html(result);
                    $("#myModal").modal();
                } else {
                    $('#modal-text').html('Error listing results!');
                    $("#myModal").modal();
                }
            }});
        });

        /* detecting which option is selected */
        function getValue(sel) {
            idTextfield.select();
            if (sel.value == 'Banned Students') {
                $('#studentID').val('');
                $('#studentID').prop('disabled', true); 
            }
            else { $('#studentID').prop('disabled', false); }
        }
        
    </script>
</body>
</html>