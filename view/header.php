<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Mengatur karakter agar teks dapat ditampilkan dengan benar. -->
    <meta charset="UTF-8">
    <!-- Membuat tampilan website menyesuaikan ukuran layar perangkat. -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Menentukan judul website yang tampil pada tab browser. -->
    <title>Rehat Sehat - Manajemen Pola Tidur</title>
    <!-- Memanggil file CSS untuk mengatur tampilan website. -->
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
<header class="topbar">
    <div class="container topbar-inner">
        <!-- Logo dan nama aplikasi RehatSehat. -->
        <a class="brand" href="index.php">
            <!-- Ikon bulan sebagai identitas aplikasi RehatSehat. -->
            <span class="brand-icon">☾</span>
            <!-- Menampilkan nama aplikasi dan deskripsi singkat. -->
            <span>
                Rehat Sehat
                <small>Catat tidur, bangun lebih teratur</small>
            </span>
        </a>

        <nav class="navigation">
            <!-- Navigasi menuju halaman dashboard. -->
            <a
                class="<?= $page === 'dashboard' ? 'active' : '' ?>"
                href="index.php?page=dashboard"
            >
                Dashboard
            </a>

            <!-- Navigasi menuju halaman data mahasiswa. -->
            <a
                class="<?= $page === 'mahasiswa' ? 'active' : '' ?>"
                href="index.php?page=mahasiswa"
            >
                Mahasiswa
            </a>

            <!-- Navigasi menuju halaman jadwal tidur. -->
            <a
                class="<?= $page === 'jadwal' ? 'active' : '' ?>"
                href="index.php?page=jadwal"
            >
                Jadwal Tidur
            </a>

            <!-- Navigasi menuju halaman catatan tidur. -->
            <a
                class="<?= $page === 'catatan' ? 'active' : '' ?>"
                href="index.php?page=catatan"
            >
                Catatan Tidur
            </a>
        </nav>
    </div>
</header>

<main class="container main-content">
    <?php if ($flash !== null): ?>
        <!--Menampilkan notifikasi dengan class CSS sesuai jenis pesan.-->
        <div
            class="alert alert-<?= e($flash['type']) ?>"
            role="alert"
        >
            <!-- Menampilkan isi pesan notifikasi. -->
            <?= e($flash['message']) ?>
        </div>
    <?php endif; ?>