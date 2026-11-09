<?php

/* Class ini digunakan untuk mengelola data pada tabel catatan_tidur. */
class CatatanTidur {
    // Properti untuk menyimpan objek koneksi database PDO.
    private PDO $db;
    public function __construct(PDO $db) {
        // Menyimpan koneksi database ke dalam properti class.
        $this->db = $db;
    }

    /* READ: Mengambil seluruh data catatan tidur. */
    public function getAll(): array {
        // Alias c digunakan untuk tabel catatan_tidur dan alias m untuk mahasiswa.
        $sql = 'SELECT c.id_catatan, c.mahasiswa_id, c.tanggal,
                    c.jam_tidur, c.jam_bangun, c.kualitas, c.catatan,
                    m.nama AS nama_mahasiswa, m.nim
                FROM catatan_tidur AS c
                /* JOIN menghubungkan kedua tabel berdasarkan primary key dan foreign key. */
                INNER JOIN mahasiswa AS m ON m.id_mahasiswa = c.mahasiswa_id
                ORDER BY c.id_catatan ASC';

        // prepare() menyiapkan perintah SQL sebelum dijalankan.
        $stmt = $this->db->prepare($sql);

        // Menjalankan query SELECT karena tidak ada parameter yang perlu dikirim.
        $stmt->execute();

        // fetchAll() mengambil seluruh baris hasil query dalam bentuk array.
        return $stmt->fetchAll();
    }

    /* READ: Mencari satu catatan tidur berdasarkan ID. */
    public function find(int $id): ?array {
        // Placeholder :id digunakan untuk menampung ID catatan yang dicari.
        $sql = 'SELECT id_catatan, mahasiswa_id, tanggal, jam_tidur, jam_bangun, kualitas, catatan
                FROM catatan_tidur
                WHERE id_catatan = :id';

        // Menyiapkan query menggunakan PDO Prepared Statement.
        $stmt = $this->db->prepare($sql);

        // Mengirimkan nilai ID untuk mengisi placeholder :id.
        $stmt->execute(['id' => $id]);

        // fetch() mengambil satu baris data dari hasil query.
        $row = $stmt->fetch();

        // PDO menghasilkan false jika data tidak ditemukan.
        return $row === false ? null : $row;
    }

    /* CREATE: Menambahkan catatan tidur baru ke database. */
    public function create(array $data): bool {
        // Memeriksa data wajib dan pilihan kualitas tidur sebelum disimpan.
        $this->validate($data);

        // INSERT digunakan untuk menambahkan satu baris catatan tidur baru.
        $sql = 'INSERT INTO catatan_tidur (mahasiswa_id, tanggal, jam_tidur, jam_bangun, kualitas, catatan)
                VALUES (:mahasiswa_id, :tanggal, :jam_tidur, :jam_bangun, :kualitas, :catatan)';

        // Menyiapkan query INSERT sebelum dieksekusi.
        $stmt = $this->db->prepare($sql);

        // Mengisi setiap placeholder dengan data dari form.
        return $stmt->execute([
            // mahasiswa_id merupakan foreign key 
            'mahasiswa_id' => (int) $data['mahasiswa_id'],
            // Menyimpan tanggal dan waktu tidur/bangun sesuai input pengguna.
            'tanggal' => trim($data['tanggal']),
            'jam_tidur' => trim($data['jam_tidur']),
            'jam_bangun' => trim($data['jam_bangun']),
            // Kualitas harus salah satu dari: Baik, Cukup, atau Kurang.
            'kualitas' => trim($data['kualitas']),
            // Catatan bersifat opsional, sehingga string kosong diperbolehkan.
            'catatan' => trim($data['catatan'] ?? ''),]);
    }

    /* UPDATE: Memperbarui catatan tidur yang sudah tersimpan. */
    public function update(int $id, array $data): bool {
        // Validasi dilakukan kembali agar data hasil perubahan tetap sesuai.
        $this->validate($data);

        // UPDATE mengubah isi catatan berdasarkan id_catatan.
        $sql = 'UPDATE catatan_tidur
                SET mahasiswa_id = :mahasiswa_id, tanggal = :tanggal, jam_tidur = :jam_tidur,
                    jam_bangun = :jam_bangun, kualitas = :kualitas, catatan = :catatan
                WHERE id_catatan = :id';

        // Menyiapkan query UPDATE menggunakan prepared statement.
        $stmt = $this->db->prepare($sql);

        // Mengirimkan data baru beserta ID catatan yang akan diperbarui.
        return $stmt->execute([
            'mahasiswa_id' => (int) $data['mahasiswa_id'],
            'tanggal' => trim($data['tanggal']),
            'jam_tidur' => trim($data['jam_tidur']),
            'jam_bangun' => trim($data['jam_bangun']),
            'kualitas' => trim($data['kualitas']),
            'catatan' => trim($data['catatan'] ?? ''),
            // ID menentukan baris catatan yang akan diubah.
            'id' => $id, ]);
    }

    /* DELETE: Menghapus satu catatan tidur berdasarkan ID. */
    public function delete(int $id): bool {
        // DELETE menghapus baris dengan id_catatan sesuai nilai parameter.
        $sql = 'DELETE FROM catatan_tidur WHERE id_catatan = :id';

        // Menyiapkan query penghapusan menggunakan PDO Prepared Statement.
        $stmt = $this->db->prepare($sql);

        // Mengisi placeholder :id kemudian menjalankan query.
        return $stmt->execute(['id' => $id]);
    }

    /* VALIDASI: Memeriksa data sebelum proses Create atau Update. */
    private function validate(array $data): void {
        // Memastikan pengguna sudah memilih mahasiswa pada form.
        if ((int) ($data['mahasiswa_id'] ?? 0) < 1) {
            throw new InvalidArgumentException('Pilih mahasiswa terlebih dahulu.');
        }

        // Mengambil nilai input dan menghilangkan spasi di awal/akhir.
        $tanggal = trim($data['tanggal'] ?? '');
        $jamTidur = trim($data['jam_tidur'] ?? '');
        $jamBangun = trim($data['jam_bangun'] ?? '');
        $kualitas = trim($data['kualitas'] ?? '');

        // Tanggal, jam tidur, jam bangun, dan kualitas wajib diisi.
        if ($tanggal === '' || $jamTidur === '' || $jamBangun === '' || $kualitas === '') {
            throw new InvalidArgumentException('Tanggal, jam tidur, jam bangun, dan kualitas tidur wajib diisi.');
        }
        // Memastikan tanggal benar-benar memiliki format YYYY-MM-DD
        $tanggalObj = DateTime::createFromFormat('!Y-m-d', $tanggal);

        if ($tanggalObj === false || $tanggalObj->format('Y-m-d') !== $tanggal) {
            throw new InvalidArgumentException('Format tanggal tidak valid.');
        }
        // Input type="time" dari HTML biasanya menghasilkan HH:MM.
        $polaWaktu = '/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/';

        if (!preg_match($polaWaktu, $jamTidur) || !preg_match($polaWaktu, $jamBangun)) {
            throw new InvalidArgumentException('Format jam tidur atau jam bangun tidak valid.');
        }
        // Nilai kualitas harus sesuai dengan pilihan ENUM pada tabel catatan_tidur.
        $daftarKualitas = ['Baik', 'Cukup', 'Kurang'];

        if (!in_array($kualitas, $daftarKualitas, true)) {
            throw new InvalidArgumentException('Kualitas tidur harus Baik, Cukup, atau Kurang.');
        }
    }
}