<?php

include_once '../connections/conn_printvis.php';
include_once '../globalincludes/newcanada_asys.php';
include_once '../globalincludes/voice_11.php';


$today = date('Y-m-d');
$ftpdate = date('Y-m-d');

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


$sql_notlpack = $dbh->prepare("SELECT Pack.Badge_Num, 
                                        Pack.Batch_Num, 
                                        Pack.Cart_Num, 
                                        Pack.CEErrors, 
                                        convert(varchar(25), Pack.DateCreated, 120) AS START, 
                                        convert(varchar(25), Pack.DateTimeComplete, 120) AS COMPLETE, 
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

// NOTL CasePicking Data for PM
$sql_NOTLcase = $aseriesconn_can->prepare("SELECT A.PBWHSE AS WHSE, 
                                        A.PBCART AS BATCH, 
                                        A.PBBIN# AS TOTENUMBER,
                                        A.PBBXSZ AS BOXSIZE,
                                        B.PDITEM AS ITEM,
                                        B.PDPKGU AS PKGU,
                                        B.PDPCKQ AS QTY,
                                        A.PBLOC# AS LOCATION,                                        
                                        A.PBBOX# AS BOXNUMBER,
                                        A.PBLP9D AS LICENSE,
                                        A.PBSHPC AS TYPE,
                                        A.PBWCS# AS WCSNUMBER,
                                        A.PBWKNO AS WORKORDERNUMBER,
                                        CHAR(DATE('20'||DIGITS(A.PBPTJD))) AS PRINTDATE
                                                                                
                                        FROM ARCPCORDTA.NOTWPB A
                                        JOIN ARCPCORDTA.NOTWPD B on B.PDWCS# = A.PBWCS# and B.PDWKNO = A.PBWKNO and A.PBBOX# = B.PDBOX# 
                                        WHERE A.PBWHSE = 11
                                        and A.PBBXSZ = 'CSE'
                                        and A.PBCART > 0
                                        and CHAR(DATE('20'||DIGITS(A.PBPTJD))) >='$today'");



$sql_NOTLcase->execute();
$array_NOTLcase = $sql_NOTLcase->fetchAll(pdo::FETCH_ASSOC);


$numrows8 = count($array_NOTLcase);
if ($numrows8 > 0) {
    $filename8 = "NOTLCase_" . $ftpdate . ".csv";
    $fp8 = fopen("./exports/$filename8", "w"); //open for write
    $data = array();

    foreach ($array_NOTLcase as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp8, $array_NOTLcase[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
     
    fclose($fp8); //close connection
    $sendftp8 = _ftpupload($filename8); //upload to nextview 

    }

    
    // NOTL Parts / nsi Data for PM
$sql_NOTLparts = $aseriesconn_can->prepare("SELECT A.PBWHSE AS WHSE, 
                                        A.PBCART AS BATCH, 
                                        A.PBBIN# AS TOTENUMBER,
                                        A.PBBXSZ AS BOXSIZE,
                                        B.PDITEM AS ITEM,
                                        B.PDPKGU AS PKGU,
                                        B.PDPCKQ AS QTY,
                                        A.PBLOC# AS LOCATION,                                        
                                        A.PBBOX# AS BOXNUMBER,
                                        A.PBLP9D AS LICENSE,
                                        A.PBSHPC AS TYPE,
                                        A.PBWCS# AS WCSNUMBER,
                                        A.PBWKNO AS WORKORDERNUMBER,
                                        CHAR(DATE('20'||DIGITS(A.PBPTJD))) AS PRINTDATE
                                                                                
                                        FROM ARCPCORDTA.NOTWPB A
                                        JOIN ARCPCORDTA.NOTWPD B on B.PDWCS# = A.PBWCS# and B.PDWKNO = A.PBWKNO and A.PBBOX# = B.PDBOX# 
                                        WHERE A.PBWHSE = 11
                                        and A.PBCART > 0
                                        and CHAR(DATE('20'||DIGITS(A.PBPTJD))) >='$today'");



$sql_NOTLparts->execute();
$array_NOTLparts = $sql_NOTLparts->fetchAll(pdo::FETCH_ASSOC);


$numrows9 = count($array_NOTLparts);
if ($numrows9 > 0) {
    $filename9 = "NOTLParts_" . $ftpdate . ".csv";
    $fp9 = fopen("./exports/$filename9", "w"); //open for write
    $data = array();

    foreach ($array_NOTLparts as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp9, $array_NOTLparts[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
     
    fclose($fp9); //close connection
    $sendftp9 = _ftpupload($filename9); //upload to nextview 

    }
    
    

$whsearray2 = array(12, 16);
$ftpdate1 = date('Y-m-d');

foreach ($whsearray2 as $whse2) {

    switch ($whse2) {
        case 12:
            $text = 'VANC';
            break;
        case 16:
            $text = 'CALG';
            break;
                    }
        // Calgary Packing Data for PM
$sql_calgpack = $aseriesconn_can->prepare("SELECT A.PBWHSE AS WHSE, 
                                        A.PBCART AS BATCH, 
                                        A.PBBIN# AS TOTENUMBER,
                                        A.PBBXSZ AS BOXSIZE,
                                        B.PDITEM AS ITEM,
                                        B.PDPKGU AS PKGU,
                                        B.PDPCKQ AS QTY,
                                        A.PBLOC# AS LOCATION,                                        
                                        A.PBBOX# AS BOXNUMBER,
                                        A.PBLP9D AS LICENSE,
                                        A.PBSHPC AS TYPE,
                                        A.PBWCS# AS WCSNUMBER,
                                        A.PBWKNO AS WORKORDERNUMBER,
                                        CHAR(DATE('20'||DIGITS(A.PBPTJD))) AS PRINTDATE, 
                                        'J-115' as JobType
                                        
                                        FROM ARCPCORDTA.NOTWPB A
                                        JOIN ARCPCORDTA.NOTWPD B on B.PDWCS# = A.PBWCS# and B.PDWKNO = A.PBWKNO and A.PBBOX# = B.PDBOX# 
                                        WHERE A.PBWHSE = $whse2
                                        and A.PBBXSZ <> 'CSE'
                                        and A.PBCART > 0
                                        and CHAR(DATE('20'||DIGITS(A.PBPTJD))) >='$today'");



$sql_calgpack->execute();
$array_calgpack = $sql_calgpack->fetchAll(pdo::FETCH_ASSOC);


$numrows5 = count($array_calgpack);
if ($numrows5 > 0) {
    $filename5 = $text . "_" . "Pack" . "_" . $whse2 . "_" . $ftpdate1 . ".csv";
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
$sql_calgpick = $aseriesconn_can->prepare("SELECT A.PBWHSE AS WHSE, 
                                        A.PBCART AS BATCH, 
                                        A.PBBIN# AS TOTENUMBER,
                                        A.PBBXSZ AS BOXSIZE,
                                        B.PDITEM AS ITEM,
                                        B.PDPKGU AS PKGU,
                                        B.PDPCKQ AS QTY,
                                        A.PBLOC# AS LOCATION,                                        
                                        A.PBBOX# AS BOXNUMBER,
                                        A.PBLP9D AS LICENSE,
                                        A.PBSHPC AS TYPE,
                                        A.PBWCS# AS WCSNUMBER,
                                        A.PBWKNO AS WORKORDERNUMBER,
                                        CHAR(DATE('20'||DIGITS(A.PBPTJD))) AS PRINTDATE, 
                                        'J-136' as JobType
                                        
                                        FROM ARCPCORDTA.NOTWPB A
                                        JOIN ARCPCORDTA.NOTWPD B on B.PDWCS# = A.PBWCS# and B.PDWKNO = A.PBWKNO and A.PBBOX# = B.PDBOX# 
                                        WHERE A.PBWHSE = $whse2
                                        and A.PBBXSZ <> 'CSE'
                                        and A.PBCART > 0
                                        and CHAR(DATE('20'||DIGITS(A.PBPTJD))) >='$today'");



$sql_calgpick->execute();
$array_calgpick = $sql_calgpick->fetchAll(pdo::FETCH_ASSOC);


$numrows6 = count($array_calgpick);
if ($numrows6 > 0) {
    $filename6 = $text . "_" . "Pick" . "_" . $whse2 . "_" . $ftpdate1 . ".csv";
    $fp6 = fopen("./exports/$filename6", "w"); //open for write
    $data = array();

    foreach ($array_calgpick as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp6, $array_calgpick[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
     
    fclose($fp6); //close connection
    $sendftp6 = _ftpupload($filename6); //upload to nextview 
}


// Calgary CasePicking Data for PM
$sql_calgcase = $aseriesconn_can->prepare("SELECT A.PBWHSE AS WHSE, 
                                        A.PBCART AS BATCH, 
                                        A.PBBIN# AS TOTENUMBER,
                                        A.PBBXSZ AS BOXSIZE,
                                        B.PDITEM AS ITEM,
                                        B.PDPKGU AS PKGU,
                                        B.PDPCKQ AS QTY,
                                        A.PBLOC# AS LOCATION,                                        
                                        A.PBBOX# AS BOXNUMBER,
                                        A.PBLP9D AS LICENSE,
                                        A.PBSHPC AS TYPE,
                                        A.PBWCS# AS WCSNUMBER,
                                        A.PBWKNO AS WORKORDERNUMBER,
                                        CHAR(DATE('20'||DIGITS(A.PBPTJD))) AS PRINTDATE
                                                                                
                                        FROM ARCPCORDTA.NOTWPB A
                                        JOIN ARCPCORDTA.NOTWPD B on B.PDWCS# = A.PBWCS# and B.PDWKNO = A.PBWKNO and A.PBBOX# = B.PDBOX# 
                                        WHERE A.PBWHSE = $whse2
                                        and A.PBBXSZ = 'CSE'
                                        and A.PBCART > 0
                                        and CHAR(DATE('20'||DIGITS(A.PBPTJD))) >='$today'");



$sql_calgcase->execute();
$array_calgcase = $sql_calgcase->fetchAll(pdo::FETCH_ASSOC);


$numrows7 = count($array_calgcase);
if ($numrows7 > 0) {
    $filename7 = $text . "_" . "Case" . "_" . $whse2 . "_" . $ftpdate1 . ".csv";
    $fp7 = fopen("./exports/$filename7", "w"); //open for write
    $data = array();

    foreach ($array_calgcase as $key => $value) {
        //$data[] = $breaklunch_array[$key];
        fputcsv($fp7, $array_calgcase[$key]);
        //$data[] = $picktimerow['bl_tsm'] . $picktimerow['bl_whse'] . $picktimerow['bl_datetime'] . $picktimerow['bl_type'] . $picktimerow['nv_type'] . "\r\n";
 }
     
    fclose($fp7); //close connection
    $sendftp7 = _ftpupload($filename7); //upload to nextview 

    }
}