<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Memanggil file helpers.php yang berisi fungsi bantuan
require_once __DIR__ . '/config/helpers.php';
// Memanggil class Database untuk mengatur koneksi ke MySQL.
require_once __DIR__ . '/config/Database.php';
// Memanggil ketiga class model yang digunakan untuk mengelola data.
require_once __DIR__ . '/class/Mahasiswa.php';
require_once __DIR__ . '/class/JadwalTidur.php';
require_once __DIR__ . '/class/CatatanTidur.php';

// Jika token belum tersedia di session, aplikasi membuat token baru.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Jika parameter page tidak diberikan, halaman yang dibuka adalah dashboard.
$allowedPages = ['dashboard', 'mahasiswa', 'jadwal', 'catatan'];
$page = $_GET['page'] ?? 'dashboard';

// Jika halaman tidak tersedia, pengguna diarahkan ke dashboard.
if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

// Variabel untuk menyimpan pesan kesalahan koneksi database.
$databaseError = null;
try {
    // Membuat objek dari class Database.
    $connection = (new Database())->getConnection();
    // Membuat objek dari ketiga class model dengan memberikan koneksi PDO melalui constructor masing-masing class.
    $mahasiswaModel = new Mahasiswa($connection);
    $jadwalModel = new JadwalTidur($connection);
    $catatanModel = new CatatanTidur($connection);
} catch (Throwable $exception) {

    // Jika koneksi database gagal, pesan kesalahan disimpan ke dalam variabel $databaseError.
    $databaseError = 'Koneksi database gagal. Pastikan MySQL aktif dan database sleepmate_db sudah diimpor. Periksa juga konfigurasi di config/Database.php.';
}

// Memeriksa apakah pengguna mengirimkan data menggunakan metode POST.
if ($databaseError === null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil token CSRF yang dikirim melalui form.
    $token = $_POST['csrf_token'] ?? '';

    // Membandingkan token dari form dengan token yang tersimpan
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        // Jika token tidak sesuai, proses dihentikan dan pesan kesalahan disimpan ke session.
        setFlash('error', 'Permintaan tidak valid. Muat ulang halaman lalu coba kembali.');
        // Mengarahkan pengguna kembali ke halaman sebelumnya.
        redirectTo($page);
    }

    // Mengambil nilai action dari form untuk menentukan operasi CRUD dan tabel yang akan diproses.
    $action = $_POST['action'] ?? '';
    // FILTER_VALIDATE_INT digunakan untuk memastikan ID berupa integer.
    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
    // Variabel untuk menentukan halaman tujuan setelah proses CRUD.
    $targetPage = $page;

    try {
        // switch digunakan untuk menentukan operasi yang harus dijalankan berdasarkan nilai action dari form.
        switch ($action) {
            case 'mahasiswa_create':
                // CREATE:
                // Memanggil method create() pada class Mahasiswa
                $mahasiswaModel->create($_POST);
                // Menentukan halaman tujuan setelah proses selesai.
                $targetPage = 'mahasiswa';
                // Menyimpan pesan keberhasilan ke dalam session.
                setFlash('success', 'Data mahasiswa berhasil ditambahkan.');
                break;
            case 'mahasiswa_update':
                // UPDATE:
                // Memeriksa ID mahasiswa yang akan diperbarui.
                if (!$id || $id < 1) {
                    throw new InvalidArgumentException('ID mahasiswa tidak valid.');
                }
                // Memanggil method update() pada class Mahasiswa..
                $mahasiswaModel->update($id, $_POST);
                // Mengarahkan pengguna kembali ke halaman mahasiswa.
                $targetPage = 'mahasiswa';
                setFlash('success', 'Data mahasiswa berhasil diperbarui.');
                break;
            case 'mahasiswa_delete':
                // DELETE:
                // Memeriksa ID mahasiswa yang akan dihapus.
                if (!$id || $id < 1) {
                    throw new InvalidArgumentException('ID mahasiswa tidak valid.');
                }
                // Memanggil method delete() pada class Mahasiswa untuk menghapus data berdasarkan ID.
                $mahasiswaModel->delete($id);
                $targetPage = 'mahasiswa';
                // Karena relasi database menggunakan ON DELETE CASCADE,
                // jadwal dan catatan milik mahasiswa tersebut juga akan terhapus.
                setFlash('success', 'Data mahasiswa berhasil dihapus. Jadwal dan catatan miliknya ikut terhapus.');
                break;
            case 'jadwal_create':
                // CREATE:
                // Memanggil method create() pada class JadwalTidur untuk menyimpan jadwal tidur baru.
                $jadwalModel->create($_POST);
                $targetPage = 'jadwal';
                setFlash('success', 'Jadwal tidur berhasil ditambahkan.');
                break;
            case 'jadwal_update':
                // UPDATE:
                // Memeriksa ID jadwal yang akan diperbarui.
                if (!$id || $id < 1) {
                    throw new InvalidArgumentException('ID jadwal tidak valid.');
                }
                // Memanggil method update() pada class JadwalTidur untuk memperbarui data berdasarkan ID.
                $jadwalModel->update($id, $_POST);
                $targetPage = 'jadwal';
                setFlash('success', 'Jadwal tidur berhasil diperbarui.');
                break;
            case 'jadwal_delete':
                // DELETE:
                // Memeriksa ID jadwal yang akan dihapus.
                if (!$id || $id < 1) {
                    throw new InvalidArgumentException('ID jadwal tidak valid.');
                }
                // Memanggil method delete() pada class JadwalTidur.
                $jadwalModel->delete($id);
                $targetPage = 'jadwal';
                setFlash('success', 'Jadwal tidur berhasil dihapus.');
                break;
            case 'catatan_create':
                // CREATE:
                // Menambahkan riwayat tidur aktual mahasiswa
                $catatanModel->create($_POST);
                $targetPage = 'catatan';
                setFlash('success', 'Catatan tidur berhasil ditambahkan.');
                break;
            case 'catatan_update':
                // UPDATE:
                // Memeriksa ID catatan tidur yang akan diperbarui.
                if (!$id || $id < 1) {
                    throw new InvalidArgumentException('ID catatan tidak valid.');
                }
                // Memanggil method update() pada class CatatanTidur.
                $catatanModel->update($id, $_POST);
                $targetPage = 'catatan';
                setFlash('success', 'Catatan tidur berhasil diperbarui.');
                break;
            case 'catatan_delete':
                // Memeriksa ID catatan tidur yang akan dihapus.
                if (!$id || $id < 1) {
                    throw new InvalidArgumentException('ID catatan tidak valid.');
                }
                // Memanggil method delete() pada class CatatanTidur.
                $catatanModel->delete($id);
                $targetPage = 'catatan';
                setFlash('success', 'Catatan tidur berhasil dihapus.');
                break;
            default:
                // Jika nilai action tidak sesuai dengan daftar
                throw new InvalidArgumentException('Aksi tidak dikenali.');
        }
    } catch (InvalidArgumentException $exception) {
        // Menangani kesalahan validasi data.
        setFlash('error', $exception->getMessage());
    } catch (PDOException $exception) {
        // Menangani kesalahan yang terjadi ketika berinteraksi dengan database MySQL.
        if ($exception->getCode() === '23000') {
            setFlash('error', 'Data gagal disimpan karena NIM sudah digunakan atau data masih memiliki relasi yang tidak sesuai.');
        } else {
            // Jika kesalahan database bukan kode 23000, aplikasi menampilkan pesan kesalahan umum.
            setFlash('error', 'Terjadi kesalahan saat mengakses database. Periksa kembali data dan konfigurasi database.');
        }
    } catch (Throwable $exception) {
        // Menangani kesalahan lain yang tidak termasuk dalam dua jenis exception sebelumnya.
        setFlash('error', 'Terjadi kesalahan pada aplikasi. Silakan coba kembali.');
    }
    // Setelah proses CRUD selesai, pengguna diarahkan ke halaman sesuai tabel yang diproses.
    redirectTo($targetPage);
}

$flash = $_SESSION['flash'] ?? null;
// Menghapus pesan flash dari session setelah diambil.
unset($_SESSION['flash']);

if ($databaseError === null) {
    $mahasiswaRows = [];
    $jadwalRows = [];
    $catatanRows = [];
    // Mengambil seluruh data mahasiswa.
    $mahasiswaOptions = $mahasiswaModel->getAll();
    $editData = null;

    if ($page === 'dashboard') {
        // Mengambil data mahasiswa.
        $mahasiswaRows = $mahasiswaOptions;
        // Mengambil seluruh data jadwal tidur.
        $jadwalRows = $jadwalModel->getAll();
        // Mengambil seluruh data catatan tidur.
        $catatanRows = $catatanModel->getAll();
    } elseif ($page === 'mahasiswa') {
        // Mengambil data mahasiswa untuk ditampilkan di halaman mahasiswa.
        $mahasiswaRows = $mahasiswaOptions;
        // Memeriksa apakah pengguna sedang membuka form edit.
        if (isset($_GET['edit'])) {
            // find() mencari satu data mahasiswa berdasarkan ID.
            $editData = $mahasiswaModel->find((int) $_GET['edit']);
        }
    } elseif ($page === 'jadwal') {
        // Mengambil seluruh data jadwal tidur untuk ditampilkan.
        $jadwalRows = $jadwalModel->getAll();
        // Jika parameter edit tersedia, cari jadwal berdasarkan ID.
        if (isset($_GET['edit'])) {
            $editData = $jadwalModel->find((int) $_GET['edit']);
        }
    } elseif ($page === 'catatan') {
        // Mengambil seluruh data catatan tidur.
        $catatanRows = $catatanModel->getAll();
        // Jika parameter edit tersedia, cari catatan berdasarkan ID.
        if (isset($_GET['edit'])) {
            $editData = $catatanModel->find((int) $_GET['edit']);
        }
    }
}

// Memanggil header.php yang berisi bagian awal tampilan website,
require __DIR__ . '/view/header.php';

// Memeriksa apakah koneksi database berhasil.
if ($databaseError !== null) {
    // Menampilkan halaman khusus yang menjelaskan bahwa koneksi database mengalami masalah.
    require __DIR__ . '/view/database_error.php';
} else {
    // Membentuk lokasi file view berdasarkan halaman yang dipilih.
    $viewFile = __DIR__ . '/view/' . $page . '.php';
    require $viewFile;
}

require __DIR__ . '/view/footer.php';

?>