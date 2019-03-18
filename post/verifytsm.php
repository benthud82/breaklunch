<?php

include_once '../../connections/conn_printvis.php';
$tsmnum = intval($_POST['tsmnum']);

$verify_sql = $conn1->prepare("SELECT tsm_name FROM printvis.tsm WHERE tsm_num = $tsmnum");
$verify_sql->execute();
$verify_array = $verify_sql->fetchAll(pdo::FETCH_ASSOC);


if (isset($verify_array[0]['tsm_name'])) {
    $tsm = $verify_array[0]['tsm_name'];
    $error = 0;
} else {
    $tsm = 'NO TSM FOUND';
    $error = 1;
}
$returndata = array();
$returndata[] = $tsm;
$returndata[] = $error;
echo json_encode($returndata);
