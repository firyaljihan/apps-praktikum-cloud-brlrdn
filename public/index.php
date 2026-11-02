<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$db = $database->getConnection();

$produk = new Produk($db);
$stmt = $produk->read();
$daftarProduk = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil notifikasi dari proses sebelumnya
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

    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .dashboard-header h1 {
            text-align: left;
            margin: 0 0 6px;
        }

        .subtitle {
            color: #64748b;
            font-size: 14px;
        }

        .stats-card {
            padding: 22px;
            background: #eef2ff;
            border-radius: 14px;
            margin-bottom: 25px;
        }

        .stats-card small {
            color: #64748b;
            font-weight: 600;
        }

        .stats-card h2 {
            margin-top: 8px;
            color: #4338ca;
            font-size: 28px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-buttons a {
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            text-decoration: none;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .btn-delete {
            background: #fee2e2;
            color: #b91c1c;
        }

        .notification {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 9999;
            max-width: 350px;
            padding: 16px 22px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 8px 30px rgba(0,0,0,.15);
            font-size: 14px;
            font-weight: 600;
            animation: slideIn .3s ease;
        }

        .notification.success {
            border-left: 5px solid #16a34a;
            color: #166534;
        }

        .notification.error {
            border-left: 5px solid #dc2626;
            color: #991b1b;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media(max-width: 600px) {
            .dashboard-header {
                align-items: stretch;
                flex-direction: column;
            }

            .notification {
                left: 15px;
                right: 15px;
                top: 15px;
                max-width: none;
            }
        }
    </style>
</head>

<body>

<?php if ($notifikasi): ?>
    <div class="notification <?= htmlspecialchars($notifikasi['jenis']); ?>"
         id="notification">
        <?= htmlspecialchars($notifikasi['pesan']); ?>
    </div>
<?php endif; ?>

<div class="container">

    <div class="dashboard-header">
        <div>
            <h1>Dashboard Produk</h1>
            <p class="subtitle">Kelola data produk dengan mudah.</p>
        </div>

        <a href="create.php" class="btn btn-primary">
            + Tambah Produk
        </a>
    </div>

    <div class="stats-card">
        <small>Total Produk Terdaftar</small>
        <h2><?= count($daftarProduk); ?> Produk</h2>
    </div>

    <h3>Daftar Produk</h3>

    <div class="table-wrapper">
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
                <?php if (count($daftarProduk) > 0): ?>

                    <?php foreach ($daftarProduk as $no => $row): ?>
                        <tr>
                            <td><?= $no + 1; ?></td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($row['nama']); ?>
                                </strong>
                            </td>

                            <td>
                                Rp <?= number_format(
                                    $row['harga'], 0, ',', '.'
                                ); ?>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <a class="btn-edit"
                                       href="edit.php?id=<?= (int)$row['id']; ?>">
                                        Edit
                                    </a>

                                    <a class="btn-delete"
                                       href="delete.php?id=<?= (int)$row['id']; ?>"
                                       onclick="return confirm('Yakin ingin menghapus produk ini? Data yang dihapus tidak dapat dikembalikan.')">
                                        Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4" class="no-data">
                            Belum ada produk. Yuk, tambahkan produk pertama!
                        </td>
                    </tr>

                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
    const notification = document.getElementById('notification');

    if (notification) {
        setTimeout(() => {
            notification.style.transition = 'opacity .4s';
            notification.style.opacity = '0';

            setTimeout(() => notification.remove(), 400);
        }, 3500);
    }
</script>

</body>
</html>
