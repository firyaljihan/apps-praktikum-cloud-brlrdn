```php
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Produk.php';

$database = new Database();
$db = $database->getConnection();
$produk = new Produk($db);

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$data = $produk->readOne($id);

if (!$data) {
    header('Location: index.php');
    exit;
}

if ($produk->delete($id)) {
    header('Location: index.php');
    exit;
}

die('Gagal menghapus produk.');
?>
```
