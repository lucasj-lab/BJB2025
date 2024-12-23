<?php
// db_connect.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'bjb_inc_db');

// Establish the database connection
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create the database if it does not exist
$db_creation_query = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($connection, $db_creation_query)) {
    mysqli_select_db($connection, DB_NAME);
} else {
    die("Error creating database: " . mysqli_error($connection));
}

?>