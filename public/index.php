```php
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$db = $database->getConnection();

$produk = new Produk($db);
$stmt = $produk->read();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Daftar Produk</h1>

        <a href="create.php" class="btn">Tambah Produk</a>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php
                $no = 1;

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr>
                    <td><?= $no++; ?></td>

                    <td>
                        <?= htmlspecialchars($row['nama']); ?>
                    </td>

                    <td>
                        Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                    </td>

                    <td>
                        <a href="edit.php?id=<?= $row['id']; ?>">
                            Edit
                        </a>

                        <a href="delete.php?id=<?= $row['id']; ?>"
                           onclick="return confirm('Yakin ingin menghapus produk ini?')">
                            Hapus
                        </a>
                    </td>
                </tr>

                <?php endwhile; ?>

                <?php if ($stmt->rowCount() === 0): ?>
                <tr>
                    <td colspan="4">Belum ada data produk.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
```
