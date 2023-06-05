<?php

/*
Database Connect 1.0
Typically, this file would be stored off the web server for security reasons.  GITHUB version has
no database credentials entered for security concerns.  This script when called will initalise a
database connection.
*/

// Database credentials
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database_name";

// Create connection
$connect = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}
?>

