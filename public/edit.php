<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$db = $database->getConnection();
$produk = new Produk($db);

$id = (int) ($_GET['id'] ?? 0);
$data = $produk->readOne($id);

if (!$data) {
    die('Data produk tidak ditemukan.');
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = (float) ($_POST['harga'] ?? 0);

    if ($nama === '' || $harga <= 0) {
        $pesan = 'Nama dan harga produk harus diisi dengan benar.';
    } else {
        $foto = $data['foto'];

        if (isset($_FILES['foto']) &&
            $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {

            $hasilUpload = $produk->uploadFoto($_FILES['foto']);

            if ($hasilUpload === false) {
                $pesan = 'Upload foto gagal. Gunakan JPG, PNG, atau GIF maksimal 2 MB.';
            } else {
                $foto = $hasilUpload;
            }
        }

        if ($pesan === '') {
            $produk->setNama($nama);
            $produk->setDeskripsi($deskripsi);
            $produk->setHarga($harga);
            $produk->setFoto($foto);

            if ($produk->update($id)) {
                header('Location: index.php');
                exit;
            } else {
                $pesan = 'Gagal memperbarui produk.';
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
    <title>Edit Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Produk</h1>

    <?php if ($pesan !== ''): ?>
        <p><?= htmlspecialchars($pesan); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Produk</label>
        <input type="text" name="nama"
            value="<?= htmlspecialchars($data['nama']); ?>" required>

        <label>Deskripsi</label>
        <textarea name="deskripsi"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>

        <label>Harga</label>
        <input type="number" name="harga" min="0" step="0.01"
            value="<?= htmlspecialchars($data['harga']); ?>" required>

        <label>Foto Produk</label>
        <?php if (!empty($data['foto'])): ?>
            <p>Foto saat ini: <?= htmlspecialchars($data['foto']); ?></p>
        <?php endif; ?>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif">

        <button type="submit" class="btn">Update Produk</button>
    </form>

    <br>
    <a href="index.php">Kembali</a>
</div>
</body>
</html>
```
