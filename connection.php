<?php
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "cybermaniaks_test"; 

$mysqli = new mysqli($host, $username, $password, $database);

// checking if the connection to the database was successful
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

?>
