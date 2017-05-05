<?php
session_start(); //starting session
//setting variable to false until they log in
$_SESSION['id'] = '';
$_SESSION['username'] = '';
//used in debugging
//ini_set('display_errors', 'On');
//error_reporting(E_ALL | E_STRICT);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Log in</title>
    <!-- Start of Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- End of Bootsrap -->
    <link rel= "stylesheet" type= "text/css" href= "style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>
<body>
    </br></br>
    <div class="container">
    <h1 class="text-center">Please Log in</h1><br/>
    <div id="loginDiv" class="center">
      <h2 class="text-center">Credentials</h2><br/>
      <form method="POST" class="loginForm" action="db/login.php">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input id="username" type="text" class="form-control" name="uid" placeholder="Username" autofocus="true">
        </div><br/>
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input id="password" type="password" class="form-control" name="pwd" placeholder="Password">
        </div><br/><br/>
        <button name="submit" value="submit" type="submit" class="btn btn-primary btn-md">Log in</button>
      </form>
<?php
    if(isset($_SESSION['incorrect']) && $_SESSION['incorrect'] == true) {
        echo <<<HTML
<div class="alert alert-warning row text-center">
    <strong>Error!</strong> Username or password is incorrect!
</div>
HTML;
    }
?>
</div></div>
<button class="cornerBtn btn btn-md" onclick="infoPage();">Info</button>
<script>
    function infoPage() {
        document.location.href = 'info.html';
    }
    $(document).ready(function() {
        $('.signupFieldset').hide();
    });
    $('.loginLabel').click(function() {
        $('.signupFieldset').slideToggle(1000);
    $('.loginFieldset').slideToggle(1000);
    });
</script>
</body>
</html>
