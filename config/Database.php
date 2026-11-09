<?php

class Database {
    // Alamat server database.
    private string $host = 'localhost';
    // Nama database yang digunakan oleh aplikasi SleepMate.
    private string $database = 'rehatsehat_db';
    // Username MySQL bawaan XAMPP biasanya root.
    private string $username = 'root';
    // Password MySQL bawaan XAMPP biasanya kosong.
    private string $password = '';
    // Menyimpan objek PDO agar koneksi tidak dibuat berulang kali.
    private ?PDO $connection = null;

    public function getConnection(): PDO {
        // Memeriksa apakah koneksi sudah pernah dibuat sebelumnya.
        if ($this->connection instanceof PDO) {
            return $this->connection;
        }

        $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";

        // Mengatur perilaku PDO ketika berinteraksi dengan database.
        $options = [
            // Kesalahan query akan menghasilkan PDOException, sehingga dapat ditangani menggunakan try-catch.
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            // Hasil fetch() dan fetchAll() dikembalikan sebagai array asosiatif.
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Menonaktifkan emulated prepares agar PDO menggunakan prepared statement native dari driver MySQL jika tersedia.
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $error) {
            // Pesan kesalahan yang aman ditampilkan di level aplikasi.
            throw new RuntimeException(
                'Koneksi database gagal. Periksa service MySQL dan konfigurasi Database.php.',
                0, $error );
        }
        return $this->connection;
    }
}