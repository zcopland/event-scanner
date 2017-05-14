<?php
include '../db/dbh.php';

$org = $_POST['org'];
$org_orig = $org;
$org = ucwords($org);
$org = str_replace(' ', '', $org);
$school = $_POST['school'];
$school = ucwords($school);
$school = str_replace(' ', '', $school);
$count = 0;
$labels = '';
$data = '';

$opening = <<<TEXT
<div class="btn-group">
    <button type="button" id="chart-btn" class="btn btn-primary" onclick="chartTable('chart')">Chart</button>
    <button type="button" id="table-btn" class="btn btn-primary" disabled="true" onclick="chartTable('table')">Table</button>
</div>
<input id='ORG' type='hidden' value='{$org_orig}' />
<div id='chartDiv' style="width:400px; height:400px;">
    <canvas id="chart" width="400px" height="400px"></canvas>
</div>
<div id="events-list" class="table-responsive"><table class='table table-hover'><tr><th>Event title</th><th>Date</th><th>Guests</th></tr>
TEXT;
$body = '';
$closing = "</tr></table></div>";


$sql = "SELECT * FROM `partyList_{$org}_{$school}`;";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    if ($count == 0) {
        $labels .= $row['eventTitle'];
        $data .= $row['count'];
    } else {
        $labels .= ",{$row['eventTitle']}";
        $data .= ",{$row['count']}";
    }
    $count++;
    $body .= <<<TEXT
<tr>
  <td>{$row['eventTitle']}</td>
  <td>{$row['eventDate']}</td>
  <td>{$row['count']}</td>
</tr>
TEXT;
}

$closing .= <<<TEXT
<input id='labels' type='hidden' value='{$labels}' />
<input id='data' type='hidden' value='{$data}' />
TEXT;

if (!empty($body)) {
    $body = $opening . $body . $closing;
    echo $body;
} else {
    echo false;
}


?>