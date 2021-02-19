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
    case 'BREAK':
        $nvtype = 'J-715';
        break;
    case 'LUNCH':
        $nvtype = 'J-730';
        break;
    case 'ENDOFDAY':
        $nvtype = 'J-725';
        break;
    

    default:
        break;
}

$datetime = date('Y-m-d H:i:s');

$sql = "INSERT INTO printvis.breaklunch (bl_tsm, bl_whse, bl_datetime, bl_type, nv_type) VALUES ($tsmnum, $whse, '$datetime', '$posttype', '$nvtype');";
$query = $conn1->prepare($sql);
$query->execute();

