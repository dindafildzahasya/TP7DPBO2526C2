# RehatSehat - Manajemen Pola Tidur Mahasiswa

RehatSehat adalah aplikasi web sederhana berbasis **PHP Native** untuk mengelola profil mahasiswa, target waktu tidur, dan catatan tidur harian. Project ini dibuat untuk tugas praktikum PHP OOP dan menggunakan MySQL sebagai database.

Saya Adinda Fildzah Hasya dengan NIM 2501218 mengerjakan Tugas Praktikum 6 pada Mata Kuliah Desain dan Pemrograman Berorientasi Objek (DPBO) untuk keberkahan-Nya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin


## 1. Fitur

Setiap entitas memiliki fitur Create, Read, Update, dan Delete (CRUD):

- **Mahasiswa**: menambah, melihat, mengubah, dan menghapus profil mahasiswa.
- **Jadwal Tidur**: mengelola tanggal serta target jam tidur dan jam bangun setiap mahasiswa.
- **Catatan Tidur**: menyimpan waktu tidur aktual, waktu bangun, kualitas tidur, dan catatan tambahan.
- Dashboard menampilkan jumlah mahasiswa, jadwal, dan catatan yang tersimpan.

> Catatan: aplikasi ini berfungsi sebagai pencatatan dan pengelolaan target tidur.

## 2. Teknologi

- PHP Native 8.1 atau lebih baru
- MySQL / MariaDB
- PDO untuk koneksi database
- HTML dan CSS
- XAMPP atau Laragon

Tidak menggunakan framework PHP maupun library CRUD eksternal.

## 3. Struktur Folder

```text
TP7DPBO2526CXX_SleepMate/
├── class/
│   ├── CatatanTidur.php
│   ├── JadwalTidur.php
│   └── Mahasiswa.php
├── config/
│   ├── Database.php
│   └── helpers.php
├── database/
│   └── rehatsehat_db.sql
├── style/
│   └── style.css
├── view/
│   ├── catatan.php
│   ├── dashboard.php
│   ├── database_error.php
│   ├── footer.php
│   ├── header.php
│   ├── jadwal.php
│   └── mahasiswa.php
├── .gitignore
├── index.php
```

### Fungsi setiap folder/file

- `class/`: berisi class model untuk masing-masing tabel. Setiap class memiliki fungsi CRUD dan menerima objek koneksi PDO melalui constructor.
- `config/Database.php`: mengatur koneksi PDO ke MySQL.
- `config/helpers.php`: berisi helper untuk escaping output, flash message, dan redirect.
- `database/`: menyimpan file SQL yang berisi struktur tabel, relasi, dan data awal.
- `view/`: berisi bagian tampilan HTML untuk dashboard dan halaman CRUD. Header dan footer dibuat modular agar dapat digunakan kembali.
- `style/`: menyimpan CSS.
- `index.php`: entry point aplikasi, mengatur halaman yang diminta, memproses aksi POST, memanggil class model, lalu mengirim data ke view.

## 4. Rancangan Database

Database: **`rehatsehat_db`**. Database terdiri dari tiga tabel.

### Tabel `mahasiswa`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_mahasiswa` | INT, PK, AI | Primary key mahasiswa |
| `nim` | VARCHAR(30), UNIQUE | Nomor induk mahasiswa |
| `nama` | VARCHAR(100) | Nama mahasiswa |
| `email` | VARCHAR(120) | Email mahasiswa |
| `created_at` | TIMESTAMP | Waktu data dibuat |

### Tabel `jadwal_tidur`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_jadwal` | INT, PK, AI | Primary key jadwal |
| `mahasiswa_id` | INT, FK | Relasi ke `mahasiswa.id_mahasiswa` |
| `tanggal` | DATE | Tanggal target tidur |
| `target_tidur` | TIME | Target mulai tidur |
| `target_bangun` | TIME | Target bangun |
| `catatan` | TEXT | Catatan opsional |
| `created_at` | TIMESTAMP | Waktu data dibuat |

### Tabel `catatan_tidur`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id_catatan` | INT, PK, AI | Primary key catatan |
| `mahasiswa_id` | INT, FK | Relasi ke `mahasiswa.id_mahasiswa` |
| `tanggal` | DATE | Tanggal tidur |
| `jam_tidur` | TIME | Waktu mulai tidur aktual |
| `jam_bangun` | TIME | Waktu bangun aktual |
| `kualitas` | ENUM | `Baik`, `Cukup`, atau `Kurang` |
| `catatan` | TEXT | Catatan opsional |
| `created_at` | TIMESTAMP | Waktu data dibuat |

### Relasi

```text
mahasiswa (1) ──────── (N) jadwal_tidur
    │
    └───────────────── (N) catatan_tidur
```

Satu mahasiswa dapat memiliki banyak jadwal dan banyak catatan tidur. Kolom `mahasiswa_id` pada tabel `jadwal_tidur` dan `catatan_tidur` merupakan foreign key ke `mahasiswa.id_mahasiswa`. Kedua foreign key memakai `ON DELETE CASCADE`, sehingga jadwal dan catatan milik mahasiswa ikut dihapus ketika data mahasiswa tersebut dihapus.

## 5. Alur Program

Alur aplikasi mengikuti pola request-response dan pemisahan tampilan dengan logika program:

1. Pengguna mengakses website melalui browser.
2. Browser mengirim HTTP request ke `index.php`.
3. `index.php` memuat helper, konfigurasi database, dan class model menggunakan `require_once`.
4. `Database` membuat koneksi PDO ke database MySQL.
5. Berdasarkan parameter `page`, `index.php` memanggil class model untuk mengambil data yang diperlukan.
6. File pada folder `view/` menampilkan form dan tabel data kepada pengguna.
7. Ketika pengguna mengirim form, browser mengirim request POST ke `index.php` dengan nilai `action`.
8. `index.php` memeriksa CSRF token, menentukan aksi CRUD, lalu memanggil method pada class yang sesuai.
9. Class model menjalankan SQL melalui PDO prepared statement (`prepare()` dan `execute()`).
10. Setelah proses selesai, aplikasi menyimpan flash message dan melakukan redirect kembali ke halaman terkait.
11. View mengambil data terbaru dan menampilkannya kepada pengguna.

### Contoh alur tambah jadwal

```text
Browser
  │ POST: action=jadwal_create + data form
  ▼
index.php
  │ validasi CSRF dan action
  ▼
JadwalTidur::create($_POST)
  │ prepare(INSERT ... VALUES (:mahasiswa_id, ...))
  │ execute([...parameter...])
  ▼
MySQL: tabel jadwal_tidur
  │ hasil proses
  ▼
index.php melakukan redirect
  ▼
view/jadwal.php menampilkan daftar jadwal terbaru
```

## 6. Penerapan OOP dan Prepared Statement

- Setiap tabel memiliki class model tersendiri: `Mahasiswa`, `JadwalTidur`, dan `CatatanTidur`.
- Objek PDO diberikan ke masing-masing class melalui constructor (*dependency injection* sederhana).
- Method model memisahkan operasi database dari kode HTML.
- Semua perintah `SELECT`, `INSERT`, `UPDATE`, dan `DELETE` pada kode PHP menggunakan `$this->db->prepare(...)` lalu `$stmt->execute(...)`.
- Tidak ada pemanggilan `mysqli_query()` atau `$db->query()` pada project.
- Output dari database ditampilkan menggunakan helper `e()` yang menjalankan `htmlspecialchars()`.
- Form POST dilengkapi token CSRF dan setelah pemrosesan menggunakan pola Post/Redirect/Get.

## 7. Pengujian CRUD 

Lakukan uji **Create, Read, Update, Delete** untuk masing-masing tabel:

1. Mahasiswa: tambah mahasiswa baru, pastikan muncul pada tabel, edit nama/email, lalu hapus.
2. Jadwal Tidur: pilih mahasiswa, tambah target tidur, pastikan nama mahasiswa tampil, edit target, lalu hapus.
3. Catatan Tidur: pilih mahasiswa, tambah catatan aktual, pastikan data tampil, edit kualitas/catatan, lalu hapus.
4. Uji relasi: buat satu jadwal dan satu catatan untuk seorang mahasiswa, kemudian hapus mahasiswa tersebut. Pastikan data turunannya ikut hilang karena `ON DELETE CASCADE`.


## 8. Dokumentasi

https://youtu.be/MPJnQo_EGiY

