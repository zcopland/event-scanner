<?php
session_start();
$_SESSION['loggedIn'] = false;
$_SESSION['org'] = '';
$_SESSION['incorrect'] = false;
session_destroy();
header("Location: ../index.php");
?>