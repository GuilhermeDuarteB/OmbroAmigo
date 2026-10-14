<?php
$serverName = "";
$database = "";
$username = "";
$password = "";

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $conn->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_SYSTEM);
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}
