
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
        $nvtype = 'PCKCOR';
        break;
    case 'MEETING':
        $nvtype = 'PCKMET';
        break;
    case 'EOD':
        $nvtype = 'PCKEOD';
        break;
    case 'HOUSE':
        $nvtype = 'PCKHKP';
        break;
    case 'SPILL':
        $nvtype = 'PCKSPL';
        break;
    case 'TRAIN':
        $nvtype = 'PCKTRN';
        break;
    case 'DRUG':
        $nvtype = 'PCKDRG';
        break;

    default:
        break;
}
$datetime = date('Y-m-d H:i:s');

$sql = "INSERT INTO printvis.eod (eod_tsm, eod_whse, eod_datetime, eod_type, nv_type) VALUES ($tsmnum, $whse, '$datetime', '$posttype', '$nvtype');";
$query = $conn1->prepare($sql);
$query->execute();

