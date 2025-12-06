<?php

//pdo connection (declare location name (localhost, username, password))
$host = "localhost";
$dbname = "snackshopdb";  
$dbuser = "root";
$dbpass = "";
$dsn = "mysql:host=$host;dbname=$dbname";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]; //untuk elak error 

try {
    $pdo = new PDO (dsn: $dsn, username: $dbuser, password: $dbpass, options: $options);
} catch (PDOException $e) {
    exit('Database connection failed' . $e->getMessage());
}

?>