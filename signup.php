<?php 
include 'db/dbh.php';
?>

<!DOCTYPE html>
<html>
<head>
	<title>Signup</title>
	<!-- Start of Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <!-- End of Bootsrap -->
    <link rel= "stylesheet" type= "text/css" href= "style.css">
</head>
<body>
	<h1 class="text-center">Sign up</h1><br/><br/>
	<div class="container">
      <h2 class="text-center">Credentials</h2>
      <form id="signUp" method="POST" class="loginForm" action="db/create.php">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <input id="username" type="text" class="form-control" name="uid" placeholder="Username" autofocus="true" required="true">
            <input id="email" type="text" class="form-control" name="email" placeholder="Email" required="true">
            <input id="organization" type="text" class="form-control" name="org" placeholder="Organization name" required="true">
            <input id="school" type="text" class="form-control" name="school" placeholder="School Name" required="true">
        </div>
        <div id="username-short"><small>Username is too short!</small></div>
		<div id="username-taken"><small><img src="media/red-x.png" height="20" width="20" /> Username is taken!</small></div>
		<div id="username-allowed"><small><img src="media/green-check.png" height="20" width="20" /> Username is available!</small></div><br/>
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
            <input id="password1" type="password" class="form-control" name="pwd" placeholder="Password" required="true">
            <input id="password2" type="password" class="form-control" name="pwd2" placeholder="Retype password" required="true">
        </div>
        <div id="passwords-not">
            <div class="alert alert-warning alert-dismissable">
                <strong>Attention</strong> Passwords do not match!
            </div>
        </div><br/>
        <div class="input-group">
            <input id="id-format" type="number" class="form-control" name="id-format" placeholder="Student ID length" required="true">
            <small>Ex: 800-00-1234 would have a length of 11 (count non-numeric characters).</small>
        	<input id="verification" type="text" class="form-control" name="ver" placeholder="Verification code" required="true">
        </div>
        <div id="ver-not">
            <div class="alert alert-warning alert-dismissable">
                <strong>Attention</strong> Verification code is incorrect!
            </div>
        </div>
        <br/><br/>
        <button id="submitbtn" name="submits" value="submits" type="button" class="btn btn-primary btn-md">Sign up</button>
      </form>
    </div>
    <script>
        $(document).ready(function() {
            var us_taken = $('#username-taken');
        	var us_allowed = $('#username-allowed');
        	var us_short = $('#username-short');
        	var sbtn = $('#submitbtn');
        	var pass_match = false;
        	var password_alert = $('#passwords-not');
        	var ver_match = false;
        	var ver_alert = $('#ver-not');
        	var us_okay = false;
        	us_allowed.hide();
        	us_taken.hide();
        	us_short.hide();
        	sbtn.hide();
        	password_alert.hide();
        	ver_alert.hide();
        	$('#username').focusout(function(){
            	var username = document.getElementById('username').value;
            	if (username.length > 4) {
                	//username is at least 5 characters
                	//check the db to see if it is taken
                	us_allowed.hide();
                	us_taken.hide();
                	us_short.hide();
                	$.ajax({type: "POST", url: "db/check-username.php", data: {username: username}, success: function(result){
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
            	} else {
                	//username is less than 5 characters
                	//do nothing
                	us_allowed.hide();
                    us_taken.hide();
                    us_short.show();
                    sbtn.hide();
            	}
        	});
        	$('#password1 ,#password2').focusout(function(){
        	    var pw1 = $('#password1').val();
                var pw2 = $('#password2').val();
            	if (pw1 != pw2) {
                	password_alert.show();
                	pass_match = false;
            	} else {
                	password_alert.hide();
                	pass_match = true;
            	}
        	});
        	$('#verification').focusout(function(){
            	if ($(this).val() == '1481297313') {
                	ver_match = true;
                	ver_alert.hide();
            	} else {
                	ver_match = false;
                	ver_alert.show();
            	}
        	});
        	$('#submitbtn').click(function(){
            	var email = $('#email').val();
            	var id_format = $('#id-format').val();
            	var school = $('#school').val();
            	var org = $('#organization').val();
            	
            	if (pass_match == true && ver_match == true && us_okay == true && email.length > 8 && id_format.length > 0 && school.length > 2 && org.length > 3) {
                	$('#signUp').submit();
            	} else {
                	console.log("Field(s) have not been filled out.");
            	}
        	});
    	});
    </script>
</body>
</html>


