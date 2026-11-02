<?php
session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$produk = new Produk($database->getConnection());

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = (float)($_POST['harga'] ?? 0);

    if ($nama === '' || $harga <= 0) {
        $pesan = 'Nama dan harga harus diisi dengan benar.';
    } else {
        $foto = '';

        if (isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {

            $hasilUpload = $produk->uploadFoto($_FILES['foto']);

            if ($hasilUpload === false) {
                $pesan = 'Foto gagal diunggah. Gunakan JPG, PNG, atau GIF maksimal 2 MB.';
            } else {
                $foto = $hasilUpload;
            }
        }

        if ($pesan === '') {
            $produk->setNama($nama);
            $produk->setDeskripsi($deskripsi);
            $produk->setHarga($harga);
            $produk->setFoto($foto);

            if ($produk->create()) {
                $_SESSION['notifikasi'] = [
                    'jenis' => 'success',
                    'pesan' => 'Produk berhasil ditambahkan!'
                ];

                header('Location: index.php');
                exit;
            } else {
                $pesan = 'Gagal menambahkan produk.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Produk</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container form-container">
    <p class="eyebrow">PRODUCT MANAGEMENT</p>
    <h1>Tambah Produk</h1>
    <p class="subtitle">Masukkan informasi produk baru.</p>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-error"><?= htmlspecialchars($pesan); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Produk</label>
        <input type="text" name="nama" required>

        <label>Deskripsi</label>
        <textarea name="deskripsi"></textarea>

        <label>Harga Produk (Rp)</label>
        <input type="number" name="harga" min="0.01" step="0.01" required>

        <label>Foto Produk</label>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif">
        <small>Format JPG, PNG, atau GIF. Maksimal 2 MB.</small>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
