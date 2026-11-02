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

    public function setNama($nama) {
        $this->nama = $nama;
    }

    public function setDeskripsi($deskripsi) {
        $this->deskripsi = $deskripsi;
    }

    public function setHarga($harga) {
        $this->harga = $harga;
    }

    public function setFoto($foto) {
        $this->foto = $foto;
    }

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
            "SELECT * FROM produk ORDER BY id DESC"
        );
    }

    public function read() {
        return $this->readAll();
    }

    public function readOne($id) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM produk WHERE id = :id"
        );

        $stmt->execute([':id' => (int)$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id) {
        $id = (int)$id;

        if ($id <= 0 || !$this->readOne($id)) {
            return false;
        }

        $sql = "UPDATE produk
                SET nama = :nama,
                    deskripsi = :deskripsi,
                    harga = :harga,
                    foto = :foto
                WHERE id = :id";

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

        $stmt = $this->conn->prepare(
            "DELETE FROM produk WHERE id = :id"
        );

        $hasil = $stmt->execute([':id' => (int)$id]);

        if ($hasil && !empty($data['foto'])) {
            $path = __DIR__ . '/../public/uploads/' . basename($data['foto']);

            if (is_file($path)) {
                unlink($path);
            }
        }

        return $hasil;
    }

    public function uploadFoto($file) {
        if (
            !isset($file['error']) ||
            $file['error'] !== UPLOAD_ERR_OK ||
            $file['size'] > 2000000
        ) {
            return false;
        }

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($extension, $allowed, true)) {
            return false;
        }

        $targetDir = __DIR__ . '/../public/uploads/';

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $filename = uniqid('produk_', true) . '.' . $extension;

        if (move_uploaded_file(
            $file['tmp_name'],
            $targetDir . $filename
        )) {
            return $filename;
        }

        return false;
    }
}
