<?php

/* Class ini digunakan untuk mengelola data pada tabel jadwal_tidur. */
class JadwalTidur {
    // Koneksi ini digunakan bersama oleh seluruh method di dalam class.
    private PDO $db;

    /* Menerima koneksi PDO dari file utama (index.php), kemudian menyimpannya agar setiap method dapat menggunakan koneksi database yang sama. */
    public function __construct(PDO $db) {
        // Menyimpan koneksi database ke dalam properti class.
        $this->db = $db;
    }

    public function getAll(): array {
        // Alias j digunakan untuk tabel jadwal_tidur dan alias m untuk mahasiswa.
        $sql = 'SELECT j.id_jadwal, j.mahasiswa_id, j.tanggal, 
                    j.target_tidur, j.target_bangun, j.catatan,
                    m.nama AS nama_mahasiswa, m.nim
                FROM jadwal_tidur AS j
                INNER JOIN mahasiswa AS m ON m.id_mahasiswa = j.mahasiswa_id
                ORDER BY j.id_jadwal ASC';

        // prepare() menyiapkan perintah SQL sebelum dieksekusi.
        $stmt = $this->db->prepare($sql);

        // Menjalankan query SELECT. Tidak ada parameter tambahan pada query ini.
        $stmt->execute();

        // fetchAll() mengambil seluruh baris hasil query dalam bentuk array.
        return $stmt->fetchAll();
    }

    /* Method ini digunakan untuk mengambil data lama ketika pengguna memilih tombol Edit, sehingga data dapat ditampilkan kembali pada form. */
    public function find(int $id): ?array {
        // id digunakan sebagai pengganti nilai ID pada query.
        $sql = 'SELECT id_jadwal, mahasiswa_id, tanggal,
                    target_tidur, target_bangun, catatan
                FROM jadwal_tidur
                WHERE id_jadwal = :id';

        // Menyiapkan query agar nilai input tidak digabung langsung ke SQL.
        $stmt = $this->db->prepare($sql);

        // Mengirimkan nilai ID untuk mengisi placeholder :id.
        $stmt->execute(['id' => $id]);

        // fetch() mengambil satu baris data dari hasil query.
        $row = $stmt->fetch();

        // PDO menghasilkan false jika data tidak ditemukan.
        // Nilai tersebut diubah menjadi null agar hasil method konsisten.
        return $row === false ? null : $row;
    }

    /* CREATE: Menambahkan jadwal tidur baru ke database. */
    public function create(array $data): bool {
        // Memastikan mahasiswa, tanggal, dan target waktu sudah diisi.
        $this->validate($data);

        // INSERT digunakan untuk menambahkan satu baris data jadwal baru.
        $sql = 'INSERT INTO jadwal_tidur (mahasiswa_id, tanggal, target_tidur, target_bangun, catatan)
                VALUES (:mahasiswa_id, :tanggal, :target_tidur, :target_bangun, :catatan)';

        // Menyiapkan query INSERT menggunakan PDO Prepared Statement.
        $stmt = $this->db->prepare($sql);

        // Mengisi setiap placeholder dengan data dari form.
        return $stmt->execute([
            // mahasiswa_id menjadi foreign key yang menghubungkan jadwal dengan mahasiswa.
            'mahasiswa_id' => (int) $data['mahasiswa_id'],

            // Tanggal dan target waktu disimpan sesuai nilai dari input form.
            'tanggal' => trim($data['tanggal']),
            'target_tidur' => trim($data['target_tidur']),
            'target_bangun' => trim($data['target_bangun']),

            // Catatan bersifat opsional, sehingga dapat dikirim sebagai string kosong.
            'catatan' => trim($data['catatan'] ?? ''),
        ]);
    }

    /* UPDATE: Memperbarui jadwal tidur yang sudah tersimpan. */
    public function update(int $id, array $data): bool {
        // Melakukan validasi yang sama seperti saat menambahkan jadwal baru.
        $this->validate($data);

        // UPDATE mengubah nilai kolom pada jadwal dengan ID yang ditentukan.
        $sql = 'UPDATE jadwal_tidur
                SET mahasiswa_id = :mahasiswa_id,
                    tanggal = :tanggal,
                    target_tidur = :target_tidur,
                    target_bangun = :target_bangun,
                    catatan = :catatan
                WHERE id_jadwal = :id';

        // Menyiapkan perintah UPDATE sebelum dieksekusi.
        $stmt = $this->db->prepare($sql);

        // Mengirimkan data baru dan ID jadwal melalui placeholder.
        return $stmt->execute([
            'mahasiswa_id' => (int) $data['mahasiswa_id'],
            'tanggal' => trim($data['tanggal']),
            'target_tidur' => trim($data['target_tidur']),
            'target_bangun' => trim($data['target_bangun']),
            'catatan' => trim($data['catatan'] ?? ''),

            // ID menentukan baris jadwal yang akan diperbarui.
            'id' => $id,
        ]);
    }

    /* DELETE: Menghapus satu jadwal tidur berdasarkan ID. */
    public function delete(int $id): bool {
        // DELETE menghapus baris jadwal yang memiliki id_jadwal sesuai parameter.
        $sql = 'DELETE FROM jadwal_tidur WHERE id_jadwal = :id';

        // Menyiapkan query penghapusan menggunakan prepared statement.
        $stmt = $this->db->prepare($sql);

        // Mengisi placeholder :id dan menjalankan query.
        return $stmt->execute(['id' => $id]);
    }

    /* VALIDASI: Memeriksa data sebelum proses Create atau Update. */
    private function validate(array $data): void {
        // Memastikan pengguna sudah memilih mahasiswa pada dropdown form.
        if ((int) ($data['mahasiswa_id'] ?? 0) < 1) {
            throw new InvalidArgumentException('Pilih mahasiswa terlebih dahulu.');
        }

        // Mengambil nilai tanggal dan target waktu, sekaligus menghapus spasi.
        $tanggal = trim($data['tanggal'] ?? '');
        $targetTidur = trim($data['target_tidur'] ?? '');
        $targetBangun = trim($data['target_bangun'] ?? '');

        // Ketiga data tersebut wajib diisi agar jadwal dapat disimpan.
        if ($tanggal === '' || $targetTidur === '' || $targetBangun === '') {
            throw new InvalidArgumentException('Tanggal, target tidur, dan target bangun wajib diisi.');
        }
    }
}