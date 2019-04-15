<?php

include_once '../connections/conn_printvis.php';

function _ftpupload($ftpfilename) {
    //* Transfer file to FTP server *//
    $server = "172.16.1.203";
    $ftp_user_name = "nextview";
    $ftp_user_pass = "NextView9";
    $dest = "$ftpfilename";
    $source = "./exports/$ftpfilename";
    $connection = ftp_connect($server);
    $login = ftp_login($connection, $ftp_user_name, $ftp_user_pass);
    if (!$connection || !$login) {
        die('Connection attempt failed!');
    }
    $upload = ftp_put($connection, $dest, $source, FTP_ASCII);
    if (!$upload) {
        echo 'FTP upload failed!';
    } else {
        echo'FTP Succeeded!';
    }
    print_r(error_get_last());
    ftp_close($connection);
}

$ftpdate = date('Y-m-d');
$sql_breaklunch = $conn1->prepare("SELECT 
                                                                        bl_tsm, bl_whse, bl_datetime, bl_type, nv_type
                                                                    FROM
                                                                        printvis.breaklunch
                                                                    WHERE
                                                                        DATE(bl_datetime) = CURDATE();");

$sql_breaklunch->execute();
$breaklunch_array = $sql_breaklunch->fetchAll(pdo::FETCH_ASSOC);
$numrows = count($breaklunch_array);
if ($numrows > 0) {
    $filename = "breaklunch" . "_" . $ftpdate . ".csv";
    $fp = fopen("./exports/$filename", "w"); //open for write
    $data = array();

    foreach ($breaklunch_array as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp, $breaklunch_array[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
    }

    fclose($fp); //close connection
    $sendftp = _ftpupload($filename); //upload to nextview
}
