<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
    </head>
<?php
session_start();
include_once '../connections/conn_printvis.php';

$_SESSION['whse'] = 3;
$whse = 3;
include 'kiosk_signin.php';
