<?php
$host = 'localhost';
$user = 'root';
$password = '';
try {
    $conn = new PDO("mysql:host=$host;port=;dbname=electro_hut", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo $e->getMessage();
}
