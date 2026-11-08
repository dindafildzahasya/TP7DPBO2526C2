<section class="page-heading">
    <div>
        <!-- Judul halaman ketika koneksi database bermasalah. -->
        <h2>Koneksi Database Bermasalah</h2>
        <!-- Penjelasan singkat mengenai kondisi aplikasi. -->
        <p>
            Aplikasi SleepMate belum dapat mengakses database.
        </p>
    </div>
</section>

<section class="section-card">
    <div class="database-error">

        <!-- Simbol peringatan ketika terjadi kesalahan. -->
        <div class="error-icon">
            !
        </div>
        <!-- Judul informasi kesalahan. -->
        <h3>Database Tidak Dapat Terhubung</h3>

        <p class="error-message">
            <?= e($databaseError ?? 'Terjadi kesalahan saat menghubungkan database.') ?>
        </p>

        <div class="error-instructions">
            <h4>Hal yang perlu diperiksa:</h4>

            <ul>
                <!-- Memastikan service MySQL sudah aktif. -->
                <li>
                    Pastikan service MySQL pada XAMPP atau Laragon
                    sudah dijalankan.
                </li>

                <!-- Memastikan database sudah dibuat dan diimpor. -->
                <li>
                    Pastikan database <strong>sleepmate_db</strong>
                    sudah dibuat dan file SQL sudah diimpor melalui phpMyAdmin.
                </li>

                <!-- Memeriksa konfigurasi koneksi database. -->
                <li>
                    Periksa host, username, dan password pada file
                    <strong>config/Database.php</strong>.
                </li>

                <!-- Memastikan driver PDO MySQL tersedia. -->
                <li>
                    Pastikan ekstensi PHP <strong>pdo_mysql</strong>
                    sudah aktif.
                </li>
            </ul>
        </div>

        <div class="form-actions">
            <a href="index.php?page=dashboard" class="btn btn-primary" >
                Coba Kembali
            </a>
        </div>
    </div>
</section>