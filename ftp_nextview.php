
<?php

include_once '../connections/conn_printvis.php';
include_once '../globalincludes/newcanada_asys.php';
include_once '../globalincludes/voice_11.php';


$today = date('Y-m-d');

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

    ftp_close($connection);
}

$whsearray = array(2, 3, 6, 7, 9, 11);
$ftpdate = date('Y-m-d');

foreach ($whsearray as $whse) {

    switch ($whse) {
        case 2:
            $text = 'ININ';
            break;
        case 3:
            $text = 'NVSP';
            break;
        case 6:
            $text = 'PADE';
            break;
        case 7:
            $text = 'TXGP';
            break;
        case 9:
            $text = 'FLJA';
            break;
        case 11:
            $text = 'NOTL';
            break;
    }


    $sql_breaklunch = $conn1->prepare("SELECT 
                                                                        bl_tsm, bl_whse, bl_datetime, bl_type, nv_type
                                                                    FROM
                                                                        printvis.breaklunch
                                                                    WHERE
                                                                        DATE(bl_datetime) = CURDATE()
                                                                        and bl_whse = $whse;");

    $sql_breaklunch->execute();
    $breaklunch_array = $sql_breaklunch->fetchAll(pdo::FETCH_ASSOC);
    $numrows = count($breaklunch_array);
    if ($numrows > 0) {
        $filename = $text . "_" . "breaklunch" . "_" . $whse . "_" . $ftpdate . ".csv";
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
    $sql_eod = $conn1->prepare("SELECT 
                                                                        eod_tsm, eod_whse, eod_datetime, eod_type, nv_type
                                                                    FROM
                                                                        printvis.eod
                                                                    WHERE
                                                                        DATE(eod_datetime) = CURDATE()
                                                                        and eod_whse = $whse;");

    $sql_eod->execute();
    $eod_array = $sql_eod->fetchAll(pdo::FETCH_ASSOC);
    $numrows2 = count($eod_array);
    if ($numrows2 > 0) {
        $filename2 = $text . "_" . "eod" . "_" . $whse . "_" . $ftpdate . ".csv";
        $fp2 = fopen("./exports/$filename2", "w"); //open for write
        $data = array();

        foreach ($eod_array as $key => $value) {
            //$data[] = $breaklunch_array[$key];
            fputcsv($fp2, $eod_array[$key]);
            //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
        }
        fclose($fp2); //close connection
        $sendftp2 = _ftpupload($filename2); //upload to nextview
    }
}


$sql_notlpack = $dbh->prepare("SELECT Pack.Badge_Num, 
                                        Pack.Batch_Num, 
                                        Pack.Cart_Num, 
                                        Pack.CEErrors, 
                                        convert(varchar(25), Pack.DateCreated, 120), 
                                        convert(varchar(25), Pack.DateTimeComplete, 120), 
                                        Pack.HelpPack, 
                                        Pack.NPErrors, 
                                        Pack.Pack_ID, 
                                        Pack.WIErrors, 
                                        Pack.WTErrors, 
                                        Tote.ToteLocation, 
                                        Tote.WCS_Num, 
                                        Tote.WorkOrder_Num, 
                                        Tote.Box_Num,
                                        'J-115' as JobType
                                        FROM HenrySchein.dbo.Pack Pack, HenrySchein.dbo.Tote Tote
                                        WHERE Pack.Batch_Num = Tote.Batch_Num and Pack.DateCreated >= '$today' and DateTimeComplete <> ' '");



$sql_notlpack->execute();
$array_notlpack = $sql_notlpack->fetchAll(pdo::FETCH_ASSOC);


$numrows3 = count($array_notlpack);
if ($numrows3 > 0) {
    $filename3 = "NOTLPack_" . $ftpdate . ".csv";
    $fp3 = fopen("./exports/$filename3", "w"); //open for write
    $data = array();

    foreach ($array_notlpack as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp3, $array_notlpack[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
    fclose($fp3); //close connection
    $sendftp3 = _ftpupload($filename3); //upload to nextview
}


        // Calgary Packing Data for PM
$sql_calgpack = $dbh->prepare("SELECT A.PBWHSE AS 'WHSE', 
                                        A.PBCART AS 'BATCH', 
                                        A.PBBIN AS 'TOTENUMBER'
                                        A.PBBXSZ AS 'BOXSIZE',
                                        B.PDITEM AS 'ITEM',
                                        B.PDPKGU AS 'PKGU',
                                        B.PDPCKQ AS 'QTY',
                                        A.PBLOC# AS 'LOCATION',                                        
                                        A.PBBOX# AS 'BOXNUMBER',
                                        A.PBLP9D AS 'LICENSE',
                                        A.PBSHPC AS 'TYPE',
                                        A.PBWCS# AS 'WCSNUMBER',
                                        A.PBWKNO AS 'WORKORDERNUMBER',
                                        convert(varchar(25), A.PBPTJD, 120) AS 'PRINTDATE', 
                                        'J-115' as JobType
                                        
                                        FROM ARCPCORDTA.NOTWPB
                                        JOIN ARCPCORDTA.NOTWPD on PDWCS# = PBWCS# and PDWKNO = PBWKNO and PBBOX# = PDBOX# 
                                        WHERE A.PBWHSE = 16
                                        and A.PBBXSZ <> 'CSE'
                                        and A.PBCART > 0
                                        and convert(varchar(25), A.PBPTJD, 120) >='$today");



$sql_calgpack->execute();
$array_calgpack = $sql_calgpack->fetchAll(pdo::FETCH_ASSOC);


$numrows5 = count($array_calgpack);
if ($numrows5 > 0) {
    $filename5 = "CALGPack_" . $ftpdate . ".csv";
    $fp5 = fopen("./exports/$filename5", "w"); //open for write
    $data = array();

    foreach ($array_calgpack as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp5, $array_calgpack[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
    fclose($fp5); //close connection
    $sendftp5 = _ftpupload($filename5); //upload to nextview
}


// Calgary Picking Data for PM
$sql_calgpick = $dbh->prepare("SELECT A.PBWHSE AS 'WHSE', 
                                        A.PBCART AS 'BATCH', 
                                        A.PBBIN AS 'TOTENUMBER'
                                        A.PBBXSZ AS 'BOXSIZE',
                                        B.PDITEM AS 'ITEM',
                                        B.PDPKGU AS 'PKGU',
                                        B.PDPCKQ AS 'QTY',
                                        A.PBLOC# AS 'LOCATION',                                        
                                        A.PBBOX# AS 'BOXNUMBER',
                                        A.PBLP9D AS 'LICENSE',
                                        A.PBSHPC AS 'TYPE',
                                        A.PBWCS# AS 'WCSNUMBER',
                                        A.PBWKNO AS 'WORKORDERNUMBER',
                                        convert(varchar(25), A.PBPTJD, 120) AS 'PRINTDATE', 
                                        'J-136' as JobType
                                        
                                        FROM ARCPCORDTA.NOTWPB
                                        JOIN ARCPCORDTA.NOTWPD on PDWCS# = PBWCS# and PDWKNO = PBWKNO and PBBOX# = PDBOX# 
                                        WHERE A.PBWHSE = 16
                                        and A.PBBXSZ <> 'CSE'
                                        and A.PBCART > 0
                                        and convert(varchar(25), A.PBPTJD, 120) >='$today");



$sql_calgpick->execute();
$array_calgpick = $sql_calgpick->fetchAll(pdo::FETCH_ASSOC);


$numrows6 = count($array_calgpick);
if ($numrows6 > 0) {
    $filenamel = "CALGPick_" . $ftpdate . ".csv";
    $fp6 = fopen("./exports/$filename6", "w"); //open for write
    $data = array();

    foreach ($array_calgpick as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp5, $array_calgpick[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
    fclose($fp6); //close connection
    $sendftp6 = _ftpupload($filename6); //upload to nextview
}

            //Vancouver Packing Data for PM
$sql_vancpack = $dbh->prepare("SELECT A.PBWHSE AS 'WHSE', 
                                        A.PBCART AS 'BATCH', 
                                        A.PBBIN AS 'TOTENUMBER'
                                        A.PBBXSZ AS 'BOXSIZE',
                                        B.PDITEM AS 'ITEM',
                                        B.PDPKGU AS 'PKGU',
                                        B.PDPCKQ AS 'QTY',
                                        A.PBLOC# AS 'LOCATION',                                        
                                        A.PBBOX# AS 'BOXNUMBER',
                                        A.PBLP9D AS 'LICENSE',
                                        A.PBSHPC AS 'TYPE',
                                        A.PBWCS# AS 'WCSNUMBER',
                                        A.PBWKNO AS 'WORKORDERNUMBER',
                                        convert(varchar(25), A.PBPTJD, 120) AS 'PRINTDATE', 
                                        'J-115' as JobType
                                        
                                        FROM ARCPCORDTA.NOTWPB
                                        JOIN ARCPCORDTA.NOTWPD on PDWCS# = PBWCS# and PDWKNO = PBWKNO and PBBOX# = PDBOX# 
                                        WHERE A.PBWHSE = 12
                                        and A.PBBXSZ <> 'CSE'
                                        and A.PBCART > 0
                                        and convert(varchar(25), A.PBPTJD, 120) >='$today");



$sql_vancpack->execute();
$array_vancpack = $sql_vancpack->fetchAll(pdo::FETCH_ASSOC);


$numrows4 = count($array_vancpack);
if ($numrows4 > 0) {
    $filename4 = "VANCPack_" . $ftpdate . ".csv";
    $fp4 = fopen("./exports/$filename4", "w"); //open for write
    $data = array();

    foreach ($array_vancpack as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp4, $array_vancpack[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
    fclose($fp4); //close connection
    $sendftp4 = _ftpupload($filename4); //upload to nextview
}


//Vancouver Picking Data for PM
$sql_vancpick = $dbh->prepare("SELECT A.PBWHSE AS 'WHSE', 
                                        A.PBCART AS 'BATCH', 
                                        A.PBBIN AS 'TOTENUMBER'
                                        A.PBBXSZ AS 'BOXSIZE',
                                        B.PDITEM AS 'ITEM',
                                        B.PDPKGU AS 'PKGU',
                                        B.PDPCKQ AS 'QTY',
                                        A.PBLOC# AS 'LOCATION',                                        
                                        A.PBBOX# AS 'BOXNUMBER',
                                        A.PBLP9D AS 'LICENSE',
                                        A.PBSHPC AS 'TYPE',
                                        A.PBWCS# AS 'WCSNUMBER',
                                        A.PBWKNO AS 'WORKORDERNUMBER',
                                        convert(varchar(25), A.PBPTJD, 120) AS 'PRINTDATE', 
                                        'J-136' as JobType
                                        
                                        FROM ARCPCORDTA.NOTWPB
                                        JOIN ARCPCORDTA.NOTWPD on PDWCS# = PBWCS# and PDWKNO = PBWKNO and PBBOX# = PDBOX# 
                                        WHERE A.PBWHSE = 12
                                        and A.PBBXSZ <> 'CSE'
                                        and A.PBCART > 0
                                        and convert(varchar(25), A.PBPTJD, 120) >='$today");



$sql_vancpick->execute();
$array_vancpick = $sql_vancpick->fetchAll(pdo::FETCH_ASSOC);


$numrows7 = count($array_vancpick);
if ($numrows7 > 0) {
    $filename7 = "VANCPick_" . $ftpdate . ".csv";
    $fp7 = fopen("./exports/$filename7", "w"); //open for write
    $data = array();

    foreach ($array_vancpick as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp7, $array_vancpick[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
    fclose($fp7); //close connection
    $sendftp7 = _ftpupload($filename7); //upload to nextview
}
