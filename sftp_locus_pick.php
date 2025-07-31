<?php

// Include the database connection file
include_once '../connections/conn_printvis.php';
include_once '../globalincludes/usa_asys.php';
//include_once '../globalincludes/voice_11.php';

// Get today's date in 'Y-m-d' format
$today = date('Y-m-d');
$ftpdate = date('Y-m-d');

// Function to upload a file to the FTP server
function _ftpupload($filename)
{
    //* Transfer file to FTP server *//		
    // $server = "172.16.1.203"; // FTP server address
    // $ftp_user_name = "nextview"; // FTP username
    // $ftp_user_pass = "NextView9"; // FTP password
    // $dest = "$ftpfilename"; // Destination filename on the FTP server
    // $source = "./exports/$ftpfilename"; // Source file path on the local server
    // $connection = ftp_connect($server); // Establish FTP connection
    // $login = ftp_login($connection, $ftp_user_name, $ftp_user_pass); // Login to FTP server
    // if (!$connection || !$login) {
    //     echo 'Connection attempt failed!'; // Output error message if connection or login fails
    // }
    // $upload = ftp_put($connection, $dest, $source, FTP_ASCII); // Upload the file to the FTP server

    // ftp_close($connection); // Close the FTP connection


        //New SFTP Process
        $dest = "$filename";
        $source = "./exports/$filename";
        include('Net/SFTP.php');
 
        $sftp = new Net_SFTP('sf.henryschein.com');

        if (!$sftp->login('Hsinextview', 'EballMM15!')) {

            exit('Login Failed');

        }

        //     $sftp->put('destfile', 'srcfile', NET_SFTP_LOCAL_FILE);

        $sftp->put($dest, $source, NET_SFTP_LOCAL_FILE);

        $sftp->disconnect();





}

// Array of warehouse IDs
$whsearray = array(6); //only denver currently has Locus pick data
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
    $sql_breaklunch = $aseriesconn->prepare("SELECT
                                                    *
                                            FROM
                                                    HSIPCORDTA.HWFPKC
                                            WHERE
                                                    PKPDTE LIKE ('$yesterday' || '%')");

    $sql_breaklunch->execute(); // Execute the query
    $breaklunch_array = $sql_breaklunch->fetchAll(pdo::FETCH_ASSOC); // Fetch all results as an associative array
    $numrows = count($breaklunch_array); // Count the number of rows returned
    if ($numrows > 0) {
        // Create a filename for the CSV file
        $filename = $text . "_" . "locus_pick" . "_" . $whse . "_" . $ftpdate . ".csv";
        $fp = fopen("./exports/$filename", "w"); // Open the file for writing

        // Write the column headers to the CSV file
        $headers = array_keys($breaklunch_array[0]); // Get the column headers from the first row
        fputcsv($fp, $headers);

        // Write each row of data to the CSV file
        foreach ($breaklunch_array as $key => $value) {
            fputcsv($fp, $breaklunch_array[$key]);
        }
        fclose($fp); // Close the file
        $sendftp = _ftpupload($filename); // Upload the file to the FTP server
    }
}