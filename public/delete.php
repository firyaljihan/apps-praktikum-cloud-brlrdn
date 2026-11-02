<?php
session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$produk = new Produk($database->getConnection());

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['notifikasi'] = [
        'jenis' => 'error',
        'pesan' => 'ID produk tidak valid.'
    ];
} elseif ($produk->delete($id)) {
    $_SESSION['notifikasi'] = [
        'jenis' => 'success',
        'pesan' => 'Produk berhasil dihapus!'
    ];
} else {
    $_SESSION['notifikasi'] = [
        'jenis' => 'error',
        'pesan' => 'Produk gagal dihapus atau tidak ditemukan.'
    ];
}

header('Location: index.php');
exit;
