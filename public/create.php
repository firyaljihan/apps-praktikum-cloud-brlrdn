<?php
require_once '../classes/Database.php';
require_once '../classes/Produk.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$produk = new Produk($db);

if($_POST){
    $produk->nama = $_POST['nama'];
    $produk->harga = $_POST['harga'];
    
    if($produk->create()){
        header("Location: index.php");
    }
}
?>
<!-- Form Tambah Produk -->
