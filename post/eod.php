
<?php

include_once '../../connections/conn_printvis.php';
$tsmnum = intval($_POST['tsmnum']);
$whse = intval($_POST['whse']);

if ($whse == 3) {
    date_default_timezone_set('America/Los_Angeles');
} elseif ($whse == 7) {
    date_default_timezone_set('America/Chicago');
} else {
    date_default_timezone_set('America/New_York');
}

$posttype = ($_POST['posttype']);

switch ($posttype) {
    case 'COORD':
        $nvtype = 'J-705';
        break;
    case 'MEETING':
        $nvtype = 'J-720';
        break;
    case 'EOD':
        $nvtype = 'J-725';
        break;
    case 'HOUSE':
        $nvtype = 'J-735';
        break;
    case 'SPILL':
        $nvtype = 'J-700';
        break;
    case 'TRAIN':
        $nvtype = 'J-710';
        break;

    default:
        break;
}

$datetime = date('Y-m-d H:i:s');

$sql = "INSERT INTO printvis.eod (eod_tsm, eod_whse, eod_datetime, eod_type, nv_type) VALUES ($tsmnum, $whse, '$datetime', '$posttype', '$nvtype');";
$query = $conn1->prepare($sql);
$query->execute();

