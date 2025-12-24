<?php
$host = '127.0.0.1';
$db   = 'oceanit_db';
$user = 'root';
$pass = ''; // WAMP default password is empty
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
     $pdo = new PDO($dsn, $user, $pass);
    //  echo "Connected successfully!"; // Remove debug output
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>