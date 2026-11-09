<?php

class Mahasiswa{
    // Properti private berarti hanya dapat diakses dari dalam class.
    private PDO $db;

    /* Digunakan untuk menerima koneksi database dari luar class.*/

    public function __construct(PDO $db){
        // Menyimpan koneksi PDO ke dalam properti class.
        $this->db = $db;
    }

    /* Data mahasiswa diurutkan berdasarkan id mahasiswa secara ascending.*/
    public function getAll(): array{
        // Query untuk mengambil seluruh kolom yang diperlukan.
        $sql = 'SELECT id_mahasiswa, nim, nama, email, created_at
                FROM mahasiswa
                ORDER BY id_mahasiswa ASC';

        // Mempersiapkan query menggunakan PDO Prepared Statement.
        $stmt = $this->db->prepare($sql);
        // Menjalankan query yang sudah dipersiapkan.
        $stmt->execute();
        // Mengambil seluruh hasil query dalam bentuk array.
        return $stmt->fetchAll();
    }

    /* memilih tombol Edit pada data mahasiswa.*/
    public function find(int $id): ?array {
        // Query untuk mengambil data mahasiswa berdasarkan primary key.
        $sql = 'SELECT id_mahasiswa, nim, nama, email
                FROM mahasiswa
                WHERE id_mahasiswa = :id';

        // Menyiapkan query sebelum dijalankan.
        $stmt = $this->db->prepare($sql);

        // Mengirimkan nilai ID ke placeholder :id.
        $stmt->execute(['id' => $id]);

        // Mengambil satu baris data mahasiswa.
        $row = $stmt->fetch();

        // Jika data tidak ditemukan, PDO mengembalikan false.
        // False diubah menjadi null agar hasil method lebih konsisten.
        return $row === false ? null : $row;
    }

    /* Sebelum data disimpan, dilakukan validasi untuk memastikan seluruh input sudah sesuai. */
    public function create(array $data): bool{
        // Memastikan data mahasiswa valid sebelum disimpan.
        $this->validate($data);

        // Query INSERT untuk menambahkan data baru.
        $sql = 'INSERT INTO mahasiswa (nim, nama, email) VALUES (:nim, :nama, :email)';

        // Menyiapkan query menggunakan prepared statement.
        $stmt = $this->db->prepare($sql);

        // Menjalankan query dengan mengirimkan nilai setiap parameter.
        // trim() digunakan untuk menghilangkan spasi di awal dan akhir.
        return $stmt->execute([
            'nim' => trim($data['nim']),
            'nama' => trim($data['nama']),
            'email' => trim($data['email']),]);
    }

    /*Data yang diperbarui ditentukan berdasarkan ID mahasiswa.*/
    public function update(int $id, array $data): bool{
        // Memvalidasi data sebelum melakukan pembaruan.
        $this->validate($data);

        // Query UPDATE untuk mengubah data mahasiswa.
        $sql = 'UPDATE mahasiswa
                SET nim = :nim, nama = :nama, email = :email
                WHERE id_mahasiswa = :id';

        // Menyiapkan query sebelum dieksekusi.
        $stmt = $this->db->prepare($sql);

        // Mengirimkan data baru beserta ID mahasiswa.
        return $stmt->execute([
            'nim' => trim($data['nim']),
            'nama' => trim($data['nama']),
            'email' => trim($data['email']),
            'id' => $id,]);
    }

    /*Penghapusan dilakukan berdasarkan ID mahasiswa.*/
    public function delete(int $id): bool{
        // Query DELETE berdasarkan primary key.
        $sql = 'DELETE FROM mahasiswa WHERE id_mahasiswa = :id';

        // Menyiapkan query menggunakan prepared statement.
        $stmt = $this->db->prepare($sql);

        // Menjalankan query dengan parameter ID mahasiswa.
        return $stmt->execute(['id' => $id]);
    }

    /* Method ini bersifat private karena hanya digunakan oleh method create() dan update() di dalam class.*/
    private function validate(array $data): void{
        // Mengambil nilai input dan menghilangkan spasi.
        $nim = trim($data['nim'] ?? '');
        $nama = trim($data['nama'] ?? '');
        $email = trim($data['email'] ?? '');

        // Memastikan seluruh kolom wajib sudah diisi.
        if ($nim === '' || $nama === '' || $email === '') {
            throw new InvalidArgumentException('NIM, nama, dan email wajib diisi.');
        }

        // Memeriksa apakah email memiliki format yang valid.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Format email tidak valid.');
        }
    }
}