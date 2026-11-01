<?php
require_once '../classes/Database.php';
require_once '../classes/Produk.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$produk = new Produk($db);
$stmt = $produk->read();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Daftar Produk</h1>
        <a href="create.php" class="btn">Tambah Produk</a>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Masukkan kode tabel data produkmu di sini -->
            </tbody>
        </table>
    </div>
</body>
</html>
