<?php
require_once 'config/database.php';
require_once 'classes/Produk.php';

$message = '';
$error = '';
$produk_data = null;
$produk = new Produk();

// 1. Logika untuk memproses form update (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    // Set properti produk dari form (TANPA setId)
    // Baris 15 yang error sudah saya hapus
    $produk->setNama($_POST['nama']);
    $produk->setDeskripsi($_POST['deskripsi']);
    $produk->setHarga($_POST['harga']);

    // Default foto adalah foto lama
    $foto_name = $_POST['foto_lama'];

    // 2. Cek apakah ada foto baru yang di-upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $new_foto_name = $produk->uploadFoto($_FILES['foto']);
        if ($new_foto_name) {
            $foto_name = $new_foto_name; // Gunakan foto baru
            // Hapus foto lama jika ada
            if (!empty($_POST['foto_lama']) && file_exists('uploads/' . $_POST['foto_lama'])) {
                unlink('uploads/' . $_POST['foto_lama']);
            }
        } else {
            $error = "Gagal upload foto baru. Pastikan file JPG, PNG, atau GIF dan ukuran < 2MB.";
        }
    }
    
    $produk->setFoto($foto_name);

    // 3. Update database jika tidak ada error upload
    if (empty($error)) {
        // ==== INI PERBAIKANNYA ====
        // Kita masukkan $id sebagai argumen ke dalam method update()
        if ($produk->update($id)) {
            $message = "✅ Produk berhasil diperbarui!";
            header("refresh:2;url=detail.php?id=" . $id);
        } else {
            $error = "Gagal memperbarui produk!";
        }
    }
    
    // Ambil data terbaru untuk ditampilkan di form (jika terjadi error)
    $produk_data = $produk->readOne($id);

} else {
    // 4. Logika untuk menampilkan data di form (GET)
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id == 0) {
        header("Location: index.php");
        exit();
    }
    
    $produk_data = $produk->readOne($id);

    if (!$produk_data) {
        header("Location: index.php");
        exit();
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
    <h1>✏️ Edit Produk</h1>

    <?php if ($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

    <?php if ($produk_data): ?>
    <form method="POST" enctype="multipart/form-data" class="form">
        <input type="hidden" name="id" value="<?php echo $produk_data['id']; ?>">
        <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($produk_data['foto']); ?>">

        <div class="form-group">
            <label for="nama">Nama Produk:</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($produk_data['nama']); ?>" required>
        </div>
        <div class="form-group">
            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" rows="5"><?php echo htmlspecialchars($produk_data['deskripsi']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="harga">Harga (Rp):</label>
            <input type="number" id="harga" name="harga" step="0.01" value="<?php echo htmlspecialchars($produk_data['harga']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Foto Saat Ini:</label>
            <?php if ($produk_data['foto']): ?>
                <img src="uploads/<?php echo htmlspecialchars($produk_data['foto']); ?>" alt="Foto saat ini" style="width: 150px; height: auto; border-radius: 5px; display: block; margin-bottom: 10px;">
            <?php else: ?>
                <p>Tidak ada foto</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="foto">Ganti Foto Produk (Opsional):</label>
            <input type="file" id="foto" name="foto" accept="image/*">
            <small>Format: JPG, PNG, GIF. Maksimal 2MB. Biarkan kosong jika tidak ingin ganti.</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">💾 Update</button>
            <a href="detail.php?id=<?php echo $produk_data['id']; ?>" class="btn btn-secondary">❌ Batal</a>
        </div>
    </form>
    <?php else: ?>
        <p>Produk tidak ditemukan.</p>
    <?php endif; ?>
</div>
</body>
</html>

