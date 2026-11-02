<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$db = $database->getConnection();

$produk = new Produk($db);
$stmt = $produk->read();
?>
