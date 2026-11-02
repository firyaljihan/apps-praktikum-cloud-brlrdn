<?php
require_once __DIR__ . '/Database.php';

class Produk {
    private $db;
    private $conn;
    private $nama;
    private $deskripsi;
    private $harga;
    private $foto;

    public function __construct($conn = null) {
        $this->db = new Database();

        $this->conn = $conn instanceof PDO
            ? $conn
            : $this->db->getConnection();
    }

    public function setNama($nama) { $this->nama = $nama; }
    public function setDeskripsi($deskripsi) { $this->deskripsi = $deskripsi; }
    public function setHarga($harga) { $this->harga = $harga; }
    public function setFoto($foto) { $this->foto = $foto; }

    public function create() {
        $sql = "INSERT INTO produk (nama, deskripsi, harga, foto)
                VALUES (:nama, :deskripsi, :harga, :foto)";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nama' => $this->nama,
            ':deskripsi' => $this->deskripsi,
            ':harga' => $this->harga,
            ':foto' => $this->foto
        ]);
    }

    public function readAll() {
        return $this->conn->query(
            "SELECT * FROM produk ORDER BY created_at DESC"
        );
    }

    // Alias agar pemanggilan read() di kode lama tetap berfungsi.
    public function read() {
        return $this->readAll();
    }

    public function readOne($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM produk WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id) {
        $sql = "UPDATE produk
                SET nama=:nama, deskripsi=:deskripsi,
                    harga=:harga, foto=:foto
                WHERE id=:id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':nama' => $this->nama,
            ':deskripsi' => $this->deskripsi,
            ':harga' => $this->harga,
            ':foto' => $this->foto,
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $data = $this->readOne($id);

        if (!$data) {
            return false;
        }

        if (!empty($data['foto']) &&
            file_exists(__DIR__ . '/../public/uploads/' . $data['foto'])) {
            unlink(__DIR__ . '/../public/uploads/' . $data['foto']);
        }

        $stmt = $this->conn->prepare(
            "DELETE FROM produk WHERE id = :id"
        );

        return $stmt->execute([':id' => $id]);
    }

    public function uploadFoto($file) {
        $targetDir = __DIR__ . '/../public/uploads/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extension, $allowed) ||
            $file['size'] > 2000000) {
            return false;
        }

        $filename = uniqid() . '.' . $extension;

        if (move_uploaded_file(
            $file['tmp_name'],
            $targetDir . $filename
        )) {
            return $filename;
        }

        return false;
    }
}
?>
