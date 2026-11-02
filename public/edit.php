
<?php
session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$produk = new Produk($database->getConnection());

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$data = $produk->readOne($id);

if (!$data) {
    $_SESSION['notifikasi'] = [
        'jenis' => 'error',
        'pesan' => 'Produk tidak ditemukan.'
    ];
    header('Location: index.php');
    exit;
}

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = (float)($_POST['harga'] ?? 0);

    if ($nama === '' || $harga <= 0) {
        $pesan = 'Nama dan harga harus diisi dengan benar.';
    } else {
        $foto = $data['foto'];

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

            if ($produk->update($id)) {
                $_SESSION['notifikasi'] = [
                    'jenis' => 'success',
                    'pesan' => 'Produk berhasil diperbarui!'
                ];

                header('Location: index.php');
                exit;
            } else {
                $pesan = 'Gagal memperbarui produk.';
            }
        }
    }

    $data['nama'] = $nama;
    $data['deskripsi'] = $deskripsi;
    $data['harga'] = $harga;
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
<div class="container form-container">
    <p class="eyebrow">PRODUCT MANAGEMENT</p>
    <h1>Edit Produk</h1>
    <p class="subtitle">Perbarui informasi produk yang sudah terdaftar.</p>

    <?php if ($pesan !== ''): ?>
        <div class="alert alert-error"><?= htmlspecialchars($pesan); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $id; ?>">

        <label>Nama Produk</label>
        <input type="text" name="nama"
               value="<?= htmlspecialchars($data['nama']); ?>" required>

        <label>Deskripsi</label>
        <textarea name="deskripsi"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>

        <label>Harga Produk (Rp)</label>
        <input type="number" name="harga" min="0.01" step="0.01"
               value="<?= htmlspecialchars((string)$data['harga']); ?>" required>

        <label>Foto Produk</label>
        <?php if (!empty($data['foto'])): ?>
            <p class="current-photo">Foto saat ini: <?= htmlspecialchars($data['foto']); ?></p>
        <?php endif; ?>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png,.gif">
        <small>Kosongkan jika tidak ingin mengganti foto. Maksimal 2 MB.</small>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
