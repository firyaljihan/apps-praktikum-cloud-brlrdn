<?php
require_once 'config/database.php';
require_once 'classes/Produk.php';

// 1. Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $produk = new Produk();

    // 2. Ambil data produk untuk dapat nama filenya
    $data = $produk->readOne($id);

    // 3. Panggil fungsi delete
    if ($produk->delete($id)) {
        
        // 4. Jika delete di DB berhasil, hapus file fotonya dari server
        if ($data && !empty($data['foto'])) {
            $file_path = 'uploads/' . $data['foto'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // 5. Redirect kembali ke halaman utama
        header("Location: index.php");
        exit();

    } else {
        die("Gagal menghapus produk.");
    }

} else {
    // Jika tidak ada ID, redirect ke halaman utama
    header("Location: index.php");
    exit();
}
?>

