
<?php

include_once '../connections/conn_printvis.php';
//include_once '../globalincludes/newcanada_asys.php';
//include_once '../globalincludes/voice_11.php';
include_once '../globalincludes/usa_asys.php';
include_once('Net/SFTP.php');


$today = date('Y-m-d');
$ftpdate = date('Y-m-d');

$today = '2024-08-21';
$ftpdate = '2024-08-21';

// Function to upload a file to the FTP server
function _ftpupload($filename)
{
    $dest = "$filename";
    $source = "./exports/$filename";
    

    $sftp = new Net_SFTP('sf.henryschein.com');

    if (!$sftp->login('Hsinextview', 'EballMM15!')) {

        exit('Login Failed');

    }

    //     $sftp->put('destfile', 'srcfile', NET_SFTP_LOCAL_FILE);

    $sftp->put($dest, $source, NET_SFTP_LOCAL_FILE);

    $sftp->disconnect();

}

// Array of warehouse IDs
$whsearray = array(2, 3, 6, 7, 9, 11, 16);
// Get the current date in 'Y-m-d' format for the FTP filename
$ftpdate = date('Y-m-d');

// Get today's date in '1YYMMDD' format
$current_date1YYMMDD = date('1ymd', strtotime('-1 day'));
// Get yesterday's date in 'Y-m-d' format
$yesterday = date('Y-m-d', strtotime('-1 day'));


// Loop through each warehouse ID
foreach ($whsearray as $whse) {

    // Determine the text identifier based on the warehouse ID
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
        case 16:
            $text = 'CALG';
            break;
    }

    // Prepare SQL query to fetch break/lunch data for the current warehouse from the previous day
    $sql_breaklunch = $conn1->prepare("SELECT 
                                                                        bl_tsm, bl_whse, bl_datetime, bl_type, nv_type
                                                                    FROM
                                                                        printvis.breaklunch
                                                                    WHERE
                                                                        DATE(bl_datetime) = CURDATE()-1
                                                                        and bl_whse = $whse;");

    $sql_breaklunch->execute(); // Execute the query
    $breaklunch_array = $sql_breaklunch->fetchAll(pdo::FETCH_ASSOC); // Fetch all results as an associative array
    $numrows = count($breaklunch_array); // Count the number of rows returned
    if ($numrows > 0) {
        // Create a filename for the CSV file
        $filename = $text . "_" . "breaklunch" . "_" . $whse . "_" . $ftpdate . ".csv";
        $fp = fopen("./exports/$filename", "w"); // Open the file for writing
        $data = array();

        // Write each row of data to the CSV file
        foreach ($breaklunch_array as $key => $value) {
            fputcsv($fp, $breaklunch_array[$key]);
        }
        fclose($fp); // Close the file
        $sendftp = _ftpupload($filename); // Upload the file to the FTP server
    }

    // Prepare SQL query to fetch end-of-day data for the current warehouse from the previous day
    $sql_eod = $conn1->prepare("SELECT 
                                                                        eod_tsm, eod_whse, eod_datetime, eod_type, nv_type
                                                                    FROM
                                                                        printvis.eod
                                                                    WHERE
                                                                        DATE(eod_datetime) = CURDATE()-1
                                                                        and eod_whse = $whse;");

    $sql_eod->execute(); // Execute the query
    $eod_array = $sql_eod->fetchAll(pdo::FETCH_ASSOC); // Fetch all results as an associative array
    $numrows2 = count($eod_array); // Count the number of rows returned
    if ($numrows2 > 0) {    
        // Create a filename for the CSV file
        $filename2 = $text . "_" . "eod" . "_" . $whse . "_" . $ftpdate . ".csv";
        $fp2 = fopen("./exports/$filename2", "w"); // Open the file for writing
        $data = array();

        // Write each row of data to the CSV file
        foreach ($eod_array as $key => $value) {
            fputcsv($fp2, $eod_array[$key]);
        }
        fclose($fp2); // Close the file
        $sendftp2 = _ftpupload($filename2); // Upload the file to the FTP server
    }


    // //zone 4 extract
    // $sql_zone4 = $as400_conn->prepare("SELECT
    //                                         MCWHSE,
    //                                         MCLIC7,
    //                                         MCCART,
    //                                         MCSHPC,
    //                                         MCSHPZ,
    //                                         MCITEM,
    //                                         SUBSTRING(MCTLOC, 1, 6) AS MCTLOC,
    //                                         MCPCKS,
    //                                         SUBSTRING(MCPLOC, 1, 6) AS MCPLOC,
    //                                         MCSTAT,
    //                                         MCRCDT,
    //                                         MCRCHM,
    //                                         MCSLDT,
    //                                         MCSLHM,
    //                                         MCSLEM,
    //                                         MCRLDT,
    //                                         MCRLHM,
    //                                         MCRLEM
    //                                     FROM
    //                                         HSIPCORDTA.NOTSMC01
    //                                     WHERE
    //                                         MCWHSE = $whse AND 
    //                                         MCCART <> 0 AND
    //                                         MCRCDT = $current_date1YYMMDD
	// 				ORDER BY MCCART ASC, SUBSTRING(MCTLOC, 1, 6) ASC");
					

    // $sql_zone4->execute(); // Execute the query
    // $zone4_array = $sql_zone4->fetchAll(pdo::FETCH_ASSOC); // Fetch all results as an associative array

    // $numrows3 = count($zone4_array); // Count the number of rows returned
    // if ($numrows3 > 0) {
    //     // Create a filename for the CSV file
    //     $filename3 = $text . "_" . "SlowMovingReplens" . "_" . $ftpdate . ".csv";
    //     $fp3 = fopen("./exports/$filename3", "w"); // Open the file for writing
    //     $data = array();

    //     // Write each row of data to the CSV file
    //     foreach ($zone4_array as $key => $value) {
    //         fputcsv($fp3, $zone4_array[$key]);
    //     }
    //     fclose($fp3); // Close the file
    //     $sendftp3 = _ftpupload($filename3); // Upload the file to the FTP server
    // }




    //Shorts extract
    // Get yesterday's date in 'Y-m-d' format
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    $sql_shorts = $as400_conn->prepare("SELECT
                                        WAREHOUSE   ,
                                        ITEM_NUMBER ,
                                        SUBSTRING(PICKUP_LOC, 1, 6) AS PICKUP_LOC,
                                        RUBBER_CART ,
                                        DROPZONE    ,
                                        TARGET_BATCH,
                                        TARGET_CART ,
                                        TARGET_TOTE ,
                                        SHORT_QTY   ,
                                        SHORT_REM_NO,
                                        ASSIGNED_TO1,
                                        ASSIGNED_TO2,
                                        BOX_LOCATION,
                                        FUTURE_USE_3,
                                        STATUS      ,
                                        TIMESTAMP_A ,
                                        TIMESTAMP_P ,
                                        TIMESTAMP_D ,
                                        TIMESTAMP_C
                                    FROM
                                        HSIPCORDTA.HWSTREM
                                    WHERE
                                        WAREHOUSE = $whse AND
                                        STATUS = 'C' AND
					ASSIGNED_TO1 <> 0 AND
                                        DATE(TIMESTAMP_C) = '$yesterday'");

    $sql_shorts->execute(); // Execute the query
    $shorts_array = $sql_shorts->fetchAll(pdo::FETCH_ASSOC); // Fetch all results as an associative array

    $numrows4 = count($shorts_array); // Count the number of rows returned
    if ($numrows4 > 0) {
        // Create a filename for the CSV file
        $filename4 = $text . "_" . "Shorts" . "_" . $ftpdate . ".csv";
        $fp4 = fopen("./exports/$filename4", "w"); // Open the file for writing
        $data = array();

        // Write each row of data to the CSV file
        foreach ($shorts_array as $key => $value) {
            fputcsv($fp4, $shorts_array[$key]);
        }
        fclose($fp4); // Close the file
        $sendftp4 = _ftpupload($filename4); // Upload the file to the FTP server
    }



}
