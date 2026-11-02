<?php
session_start();

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$produk = new Produk($database->getConnection());

$daftarProduk = $produk->read()->fetchAll(PDO::FETCH_ASSOC);

$notifikasi = $_SESSION['notifikasi'] ?? null;
unset($_SESSION['notifikasi']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Produk</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php if ($notifikasi): ?>
<div class="notification <?= htmlspecialchars($notifikasi['jenis']); ?>" id="notification">
    <?= htmlspecialchars($notifikasi['pesan']); ?>
</div>
<?php endif; ?>

<div class="container">
    <header class="dashboard-header">
        <div>
            <p class="eyebrow">PRODUCT MANAGEMENT</p>
            <h1>Dashboard Produk</h1>
            <p class="subtitle">Kelola dan pantau seluruh produkmu.</p>
        </div>
        <a href="create.php" class="btn btn-primary">+ Tambah Produk</a>
    </header>

    <div class="stats-card">
        <div>
            <span>Total Produk</span>
            <h2><?= count($daftarProduk); ?></h2>
            <small>Produk terdaftar dalam database</small>
        </div>
        <div class="stats-icon">📦</div>
    </div>

    <section class="content-card">
        <div class="section-heading">
            <div>
                <h2>Daftar Produk</h2>
                <p>Informasi produk yang tersimpan.</p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($daftarProduk): ?>
                    <?php foreach ($daftarProduk as $no => $row): ?>
                    <tr>
                        <td><?= $no + 1; ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['nama']); ?></strong>
                            <small class="product-description">
                                <?= htmlspecialchars($row['deskripsi'] ?? 'Tidak ada deskripsi'); ?>
                            </small>
                        </td>
                        <td class="price">
                            Rp <?= number_format((float)$row['harga'], 0, ',', '.'); ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a class="btn btn-edit" href="edit.php?id=<?= (int)$row['id']; ?>">Edit</a>
                                <a class="btn btn-delete"
                                   href="delete.php?id=<?= (int)$row['id']; ?>"
                                   onclick="return confirm('Yakin ingin menghapus produk ini?');">
                                   Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="no-data">
                            Belum ada produk. Tambahkan produk pertamamu!
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <footer class="footer">Product Management App · Cloud Computing</footer>
</div>

<script>
const notification = document.getElementById('notification');

if (notification) {
    setTimeout(() => {
        notification.classList.add('hide');
        setTimeout(() => notification.remove(), 400);
    }, 3500);
}
</script>
</body>
</html>
