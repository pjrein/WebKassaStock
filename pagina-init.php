<?php
// Start the session
session_start();
include 'includes/biblio.php';
//include 'index.php';
if (!@$_REQUEST['submit']) {
    selectie_form();
} elseif ($_REQUEST['submit'] == "selectie") {
    selectie_form();#keuze formulier
    $kassa_data = kassalijst();#haal de data op uit de database (kassa)
    vergelijk($kassa_data);
} elseif ($_REQUEST['submit'] == "nietInKassa") {
    $datum = $_SESSION["datum"];
    $scan_data = $_SESSION["scannen"];
    $naam = "nietInKassa" . $datum . ".csv";
    $header = array_keys($scan_data[0]);
    convert_to_csv($_SESSION["nietInKassa"], $header,$naam, ',');
} elseif ($_REQUEST['submit'] == "nietInScan") {
    $datum = $_SESSION["datum"];
    $scan_data = $_SESSION["scannen"];
    $naam = "nietInScan" . $datum . ".csv";
    $header = array_keys($scan_data[0]);
    convert_to_csv($_SESSION["nietInScan"], $header ,$naam, ',');
} elseif ($_REQUEST['submit'] == "merge") {
    $datum = $_SESSION["datum"];
    $merge = $_SESSION["merge"];
    $naam = "merge" . $datum . ".csv";  //naar dropbox
    $header = array_keys($merge[0]);
    convert_to_csv($_SESSION["merge"],$header ,$naam, ',');
 } elseif ($_REQUEST['submit'] == "update") {
    update();
    include 'index.php';
}   elseif ($_REQUEST['submit'] == "init") {
    init();
    include 'index.php';
}
?>
