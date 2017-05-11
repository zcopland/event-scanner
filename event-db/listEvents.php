<?php
include '../db/dbh.php';

$org = $_POST['org'];
$org = ucwords($org);
$org = str_replace(' ', '', $org);
$school = $_POST['school'];
$school = ucwords($school);
$school = str_replace(' ', '', $school);

$opening = "<div id=\"events-list\" class=\"table-responsive\"><table class='table table-hover'><tr><th>Event title</th><th>Date</th><th>Guests</th></tr>";
$body = '';
$closing = "</tr></table></div>";


$sql = "SELECT * FROM `partyList_{$org}_{$school}`;";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $body .= <<<TEXT
<tr>
  <td>{$row['eventTitle']}</td>
  <td>{$row['eventDate']}</td>
  <td>{$row['count']}</td>
</tr>
TEXT;
}

if (!empty($body)) {
    $body = $opening . $body . $closing;
    echo $body;
} else {
    echo false;
}


?>