CREATE DATABASE IF NOT EXISTS rehatsehat_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE rehatsehat_db;

CREATE TABLE IF NOT EXISTS mahasiswa (
    id_mahasiswa INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jadwal_tidur (
    id_jadwal INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    tanggal DATE NOT NULL,
    target_tidur TIME NOT NULL,
    target_bangun TIME NOT NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_jadwal_mahasiswa
        FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id_mahasiswa)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_jadwal_tanggal (tanggal)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS catatan_tidur (
    id_catatan INT AUTO_INCREMENT PRIMARY KEY,
    mahasiswa_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_tidur TIME NOT NULL,
    jam_bangun TIME NOT NULL,
    kualitas ENUM('Baik', 'Cukup', 'Kurang') NOT NULL DEFAULT 'Baik',
    catatan TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_catatan_mahasiswa
        FOREIGN KEY (mahasiswa_id) REFERENCES mahasiswa(id_mahasiswa)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX idx_catatan_tanggal (tanggal)
) ENGINE=InnoDB;

-- Data contoh agar halaman tidak kosong saat pertama kali dijalankan.
INSERT INTO mahasiswa (nim, nama, email) VALUES
('2304145', 'Alya Putri', 'alyaputri@upi.com'),
('2403499', 'Raka Pratama', 'rakapratama@upi.com'),
('2597611', 'Nadia Safitri', 'nadiasafitri@upi.com');

INSERT INTO jadwal_tidur (mahasiswa_id, tanggal, target_tidur, target_bangun, catatan) VALUES
(1, '2026-11-04', '22:00:00', '06:00:00', 'Mengurangi penggunaan ponsel sebelum tidur.'),
(2, '2026-11-04', '22:30:00', '06:30:00', 'Menyelesaikan tugas lebih awal.'),
(3, '2026-11-05', '21:30:00', '05:30:00', 'Menjaga jadwal tidur tetap konsisten.');

INSERT INTO catatan_tidur (mahasiswa_id, tanggal, jam_tidur, jam_bangun, kualitas, catatan) VALUES
(1, '2026-11-03', '22:15:00', '06:10:00', 'Baik', 'Bangun terasa cukup segar.'),
(2, '2026-11-03', '23:45:00', '06:20:00', 'Cukup', 'Tidur agak larut karena tugas.'),
(3, '2026-11-03', '21:50:00', '05:40:00', 'Baik', 'Tidak terbangun pada malam hari.');
rehatsehat_dbcatatan_tidurjadwal_tidurmahasiswa