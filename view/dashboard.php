<?php
// Menghitung jumlah mahasiswa berdasarkan data yang telah diambil
$totalMahasiswa = count($mahasiswaRows);
// Menghitung jumlah jadwal tidur yang tersimpan di database.
$totalJadwal = count($jadwalRows);
// Menghitung jumlah catatan tidur yang tersimpan di database.
$totalCatatan = count($catatanRows);
// Mengambil maksimal lima jadwal tidur pertama dari array.
$jadwalTerbaru = array_slice($jadwalRows, 0, 5);
// Mengambil maksimal lima catatan tidur pertama dari array.
$catatanTerbaru = array_slice($catatanRows, 0, 5);
?>

<section class="page-heading">
    <!-- Judul utama halaman dashboard. -->
    <div>
        <h2>Dashboard Rehat Sehat</h2>
        <!-- Penjelasan singkat mengenai halaman dashboard. -->
        <p>
            Pantau dan kelola pola tidur mahasiswa melalui satu halaman.
        </p>
    </div>
</section>

<section class="stats-grid">
    <div class="stat-card">
        <!-- Label informasi pada kartu. -->
        <p>Total Mahasiswa</p>
        <!-- Menampilkan jumlah mahasiswa yang terdaftar. -->
        <h3><?= e($totalMahasiswa) ?></h3>
        <!-- Tautan untuk membuka halaman mahasiswa. -->
        <a href="index.php?page=mahasiswa">
            Lihat Mahasiswa
        </a>
    </div>

    <div class="stat-card">
        <!-- Label informasi pada kartu. -->
        <p>Total Jadwal Tidur</p>
        <!-- Menampilkan jumlah jadwal tidur yang tersedia. -->
        <h3><?= e($totalJadwal) ?></h3>
        <!-- Tautan untuk membuka halaman jadwal tidur. -->
        <a href="index.php?page=jadwal">
            Lihat Jadwal
        </a>
    </div>

    <div class="stat-card">
        <!-- Label informasi pada kartu. -->
        <p>Total Catatan Tidur</p>
        <!-- Menampilkan jumlah catatan tidur yang tersedia. -->
        <h3><?= e($totalCatatan) ?></h3>
        <!-- Tautan untuk membuka halaman catatan tidur. -->
        <a href="index.php?page=catatan">
            Lihat Catatan
        </a>
    </div>
</section>

<section class="section-card">
    <!-- Bagian judul dan tautan menuju seluruh jadwal. -->
    <div class="section-header">
        <div>
            <h3>Jadwal Tidur Terbaru</h3>
            <p>
                Daftar target waktu tidur mahasiswa yang telah disimpan.
            </p>
        </div>
        <!-- Mengarahkan pengguna menuju halaman jadwal tidur. -->
        <a href="index.php?page=jadwal" class="btn btn-secondary">
            Lihat Semua
        </a>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <!-- Bagian kepala tabel. -->
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Mahasiswa</th>
                    <th>Tanggal</th>
                    <th>Target Tidur</th>
                    <th>Target Bangun</th>
                </tr>
            </thead>

            <!-- Bagian isi tabel. -->
            <tbody>
                <?php if (!empty($jadwalTerbaru)): ?>
                    <?php foreach ($jadwalTerbaru as $index => $row): ?>
                        <tr>
                            <!-- Nomor urut data. -->
                            <td>
                                <?= e($index + 1) ?>
                            </td>

                            <!-- Nama mahasiswa pemilik jadwal. -->
                            <td>
                                <?= e($row['nama_mahasiswa']) ?>
                            </td>

                            <!-- Tanggal jadwal tidur. -->
                            <td>
                                <?= e(date('d-m-Y', strtotime($row['tanggal']))) ?>
                            </td>

                            <!-- Target waktu mulai tidur. -->
                            <td>
                                <?= e(substr($row['target_tidur'], 0, 5)) ?>
                            </td>

                            <!-- Target waktu bangun. -->
                            <td>
                                <?= e(substr($row['target_bangun'], 0, 5)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-state">
                            Belum ada jadwal tidur yang tersimpan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="section-card">
    <!-- Bagian judul dan tautan menuju seluruh catatan. -->
    <div class="section-header">
        <div>
            <h3>Catatan Tidur Terbaru</h3>
            <p>
                Riwayat waktu tidur dan bangun mahasiswa.
            </p>
        </div>
        <!-- Mengarahkan pengguna menuju halaman catatan tidur. -->
        <a href="index.php?page=catatan" class="btn btn-secondary">
            Lihat Semua
        </a>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <!-- Bagian kepala tabel. -->
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Mahasiswa</th>
                    <th>Tanggal</th>
                    <th>Jam Tidur</th>
                    <th>Jam Bangun</th>
                    <th>Kualitas Tidur</th>
                </tr>
            </thead>

            <!-- Bagian isi tabel. -->
            <tbody>
                <?php if (!empty($catatanTerbaru)): ?>
                    <?php foreach ($catatanTerbaru as $index => $row): ?>
                        <tr>
                            <!-- Nomor urut data. -->
                            <td>
                                <?= e($index + 1) ?>
                            </td>

                            <!-- Nama mahasiswa pemilik catatan. -->
                            <td>
                                <?= e($row['nama_mahasiswa']) ?>
                            </td>

                            <!-- Tanggal pencatatan tidur. -->
                            <td>
                                <?= e(date('d-m-Y', strtotime($row['tanggal']))) ?>
                            </td>

                            <!-- Jam mahasiswa mulai tidur. -->
                            <td>
                                <?= e(substr($row['jam_tidur'], 0, 5)) ?>
                            </td>

                            <!-- Jam mahasiswa bangun tidur. -->
                            <td>
                                <?= e(substr($row['jam_bangun'], 0, 5)) ?>
                            </td>

                            <!-- Menampilkan kualitas tidur mahasiswa. -->
                            <td>
                                <span class="quality-badge"> <?= e($row['kualitas']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Menampilkan pesan apabila belum ada catatan tidur. -->
                    <tr>
                        <td colspan="6" class="empty-state">
                            Belum ada catatan tidur yang tersimpan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>