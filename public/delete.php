<?php
require_once '../config/database.php';
require_once '../classes/Produk.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $database = new Database();
    $db = $database->getConnection();
    
    $produk = new Produk($db);

    $data = $produk->readOne($id);

    if ($produk->delete($id)) {

        if ($data && !empty($data['foto'])) {
            $file_path = '../uploads/' . $data['foto'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        header("Location: index.php");
        exit();

    } else {
        die("Gagal menghapus produk.");
    }

} else {
    header("Location: index.php");
    exit();
}
?>
