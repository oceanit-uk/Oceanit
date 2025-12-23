<?php
// CORRECT MAMP PDO Connection
$host = 'localhost';
$dbname = 'oceanit_db';  // Change to your DB name
$username = 'root';
$password = 'root';
$port = 8889;  // MAMP MySQL port

try {
    // OPTION 1: Using port (Recommended)
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    
    // OPTION 2: Using socket (Alternative)
    // $pdo = new PDO("mysql:host=$host;unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock;dbname=$dbname", $username, $password);
    
    // Set PDO error mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>