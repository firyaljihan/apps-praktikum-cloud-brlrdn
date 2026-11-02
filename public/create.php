```php
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$db = $database->getConnection();
$produk = new Produk($db);

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produk->setNama(trim($_POST['nama'] ?? ''));
    $produk->setDeskripsi(trim($_POST['deskripsi'] ?? ''));
    $produk->setHarga((float) ($_POST['harga'] ?? 0));

    $foto = '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $hasilUpload = $produk->uploadFoto($_FILES['foto']);

        if ($hasilUpload === false) {
            $pesan = 'Upload foto gagal. Gunakan JPG, PNG, atau GIF maksimal 2 MB.';
        } else {
            $foto = $hasilUpload;
        }
    }

    if ($pesan === '') {
        $produk->setFoto($foto);

        if ($produk->create()) {
            header('Location: index.php');
            exit;
        } else {
            $pesan = 'Gagal menambahkan produk.';
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
<div class="container">
    <h1>Tambah Produk</h1>

    <?php if ($pesan !== ''): ?>
        <p><?= htmlspecialchars($pesan); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Nama Produk</label>
        <input type="text" name="nama" required>

        <label>Deskripsi</label>
        <textarea name="deskripsi"></textarea>

        <label>Harga</label>
        <input type="number" name="harga" min="0" step="0.01" required>

        <label>Foto Produk</label>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif">

        <button type="submit" class="btn">Simpan Produk</button>
    </form>

    <br>
    <a href="index.php">Kembali</a>
</div>
</body>
</html>
```
