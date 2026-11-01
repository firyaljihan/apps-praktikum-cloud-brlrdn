<?php
require_once '../classes/Database.php';
require_once '../classes/Produk.php';
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

$produk = new Produk($db);
$produk->id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing ID.');
$produk->readOne();

if($_POST){
    $produk->nama = $_POST['nama'];
    $produk->harga = $_POST['harga'];
    
    if($produk->update()){
        header("Location: index.php");
    }
}
?>
<!-- Form Edit Produk -->
