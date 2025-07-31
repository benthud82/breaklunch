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
if ($posttype == 'BREAK') {
    $nvtype = 'PCKBRK';
} elseif ($posttype == 'SHUTTLE') {
    $nvtype = 'PCKSHU';
} else {
    $nvtype = 'PCKLUN';
}

$datetime = date('Y-m-d H:i:s');

$sql = "INSERT INTO printvis.breaklunch (bl_tsm, bl_whse, bl_datetime, bl_type, nv_type) VALUES ($tsmnum, $whse, '$datetime', '$posttype', '$nvtype');";
$query = $conn1->prepare($sql);
$query->execute();

