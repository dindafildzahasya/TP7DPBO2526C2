<?php
// Memeriksa apakah pengguna sedang melakukan proses edit.
$isEditing = is_array($editData);
// Menentukan action berdasarkan kondisi form.
$formAction = $isEditing ? 'catatan_update' : 'catatan_create';
?>

<section class="page-heading">
    <div>
        <!-- Judul halaman pengelolaan catatan tidur. -->
        <h2>Catatan Tidur</h2>
        <!-- Penjelasan singkat mengenai fungsi halaman. -->
        <p>
            Catat waktu tidur dan bangun mahasiswa untuk memantau
            kualitas pola tidur setiap hari.
        </p>
    </div>
</section>

<section class="section-card">
    <!-- Judul form berubah sesuai kondisi tambah atau edit. -->
    <div class="section-header">
        <div>
            <h3>
                <?= $isEditing ? 'Edit Catatan Tidur' : 'Tambah Catatan Tidur' ?>
            </h3>
            <p>
                <?= $isEditing
                    ? 'Perbarui informasi riwayat tidur mahasiswa.'
                    : 'Masukkan riwayat waktu tidur dan bangun mahasiswa.' ?>
            </p>
        </div>
    </div>

    <form action="index.php?page=catatan" method="POST" class="form-card">
        <!-- Token digunakan untuk memastikan request berasal dari form aplikasi yang sah. -->
        <input type="hidden" name="csrf_token"
            value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
        >

        <!-- Menentukan operasi CRUD yang akan dijalankan index.php. -->
        <input type="hidden" name="action" value="<?= e($formAction) ?>">

        <?php if ($isEditing): ?>
            <input type="hidden" name="id"
                value="<?= e($editData['id_catatan'] ?? '') ?>"
            >
        <?php endif; ?>

        <div class="form-group">
            <label for="mahasiswa_id">Mahasiswa</label>

            <select id="mahasiswa_id" name="mahasiswa_id" required>
                <!-- Pilihan awal sebelum mahasiswa dipilih. -->
                <option value="">-- Pilih Mahasiswa --</option>
                <?php foreach ($mahasiswaOptions as $option): ?>
                    <option
                        value="<?= e($option['id_mahasiswa']) ?>"
                        <?= ($isEditing &&
                            (int) $editData['mahasiswa_id'] ===
                            (int) $option['id_mahasiswa']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e($option['nim']) ?> - <?= e($option['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="tanggal">Tanggal Tidur</label>

            <input type="date" id="tanggal" name="tanggal"
                value="<?= e($editData['tanggal'] ?? '') ?>" required
            >
        </div>

        <div class="form-group">
            <label for="jam_tidur">Jam Mulai Tidur</label>

            <input type="time" id="jam_tidur" name="jam_tidur"
                value="<?= e(substr((string) ($editData['jam_tidur'] ?? ''), 0, 5)
                ) ?>" required
            >
        </div>

        <div class="form-group">
            <label for="jam_bangun">Jam Bangun</label>

            <input type="time" id="jam_bangun" name="jam_bangun"
                value="<?= e(substr((string) ($editData['jam_bangun'] ?? ''), 0, 5)
                ) ?>" required
            >
        </div>

        <div class="form-group">
            <label for="kualitas">Kualitas Tidur</label>

            <select id="kualitas" name="kualitas" required>
                <!-- Pilihan awal sebelum kualitas ditentukan. -->
                <option value="">-- Pilih Kualitas Tidur --</option>

                <!-- Pilihan kualitas tidur Baik. -->
                <option value="Baik" <?= ($isEditing &&
                        ($editData['kualitas'] ?? '') === 'Baik'
                    ) ? 'selected' : '' ?>
                >
                    Baik
                </option>

                <!-- Pilihan kualitas tidur Cukup. -->
                <option value="Cukup" <?= ($isEditing &&
                        ($editData['kualitas'] ?? '') === 'Cukup'
                    ) ? 'selected' : '' ?>
                >
                    Cukup
                </option>

                <!-- Pilihan kualitas tidur Kurang. -->
                <option value="Kurang" <?= ($isEditing &&
                        ($editData['kualitas'] ?? '') === 'Kurang'
                    ) ? 'selected' : '' ?>
                >
                    Kurang
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="catatan">Catatan Tambahan (Opsional)</label>
            <!--Textarea digunakan untuk menyimpan keterangan tambahan -->
            <textarea id="catatan" name="catatan" rows="3"
                placeholder="Tuliskan keterangan tambahan mengenai tidur..."
            ><?= e($editData['catatan'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $isEditing ? 'Simpan Perubahan' : 'Tambah Catatan' ?>
            </button>

            <?php if ($isEditing): ?>
                <a
                    href="index.php?page=catatan"
                    class="btn btn-secondary"
                >
                    Batal
                </a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="section-card">
    <!-- Judul tabel dan jumlah catatan tidur yang tersimpan. -->
    <div class="section-header">
        <div>
            <h3>Daftar Catatan Tidur</h3>
            <p>
                Seluruh riwayat tidur dan bangun mahasiswa.
            </p>
        </div>
        <!-- Menampilkan jumlah seluruh catatan tidur. -->
        <span class="data-count">
            Total: <?= e(count($catatanRows)) ?> catatan
        </span>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <!-- Bagian kepala tabel yang berisi nama setiap kolom. -->
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Tanggal</th>
                    <th>Jam Tidur</th>
                    <th>Jam Bangun</th>
                    <th>Kualitas</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <!-- Bagian isi tabel untuk menampilkan data catatan tidur. -->
            <tbody>
                <?php if (!empty($catatanRows)): ?>
                    <?php foreach ($catatanRows as $index => $row): ?>
                        <tr>
                            <!-- Menampilkan nomor urut data. -->
                            <td>
                                <?= e($index + 1) ?>
                            </td>
                            <!-- Menampilkan nama mahasiswa pemilik catatan. -->
                            <td>
                                <?= e($row['nama_mahasiswa']) ?>
                            </td>
                            <!-- Menampilkan NIM mahasiswa. -->
                            <td>
                                <?= e($row['nim']) ?>
                            </td>
                            <!-- Menampilkan tanggal pencatatan tidur. -->
                            <td>
                                <?= e(date('d-m-Y', strtotime($row['tanggal']))) ?>
                            </td>
                            <!-- Menampilkan jam mahasiswa mulai tidur. -->
                            <td>
                                <?= e(substr($row['jam_tidur'], 0, 5)) ?>
                            </td>
                            <!-- Menampilkan jam mahasiswa bangun tidur. -->
                            <td>
                                <?= e(substr($row['jam_bangun'], 0, 5)) ?>
                            </td>
                            <!-- Menampilkan kualitas tidur mahasiswa. -->
                            <td>
                                <span class="quality-badge">
                                    <?= e($row['kualitas']) ?>
                                </span>
                            </td>
                            <!-- Menampilkan catatan tambahan. -->
                            <td>
                                <?= e($row['catatan'] ?: '-') ?>
                            </td>

                            <td>
                                <div class="table-actions">
                                    <!--Tombol Edit mengirimkan ID catatan melalui parameter GET. -->
                                    <a
                                        href="index.php?page=catatan&edit=<?= e($row['id_catatan']) ?>"
                                        class="btn btn-secondary btn-small"
                                    >
                                        Edit
                                    </a>

                                    <!-- Penghapusan tidak dilakukan melalui URL secara langsung. -->
                                    <form action="index.php?page=catatan" method="POST"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus catatan tidur ini?');"
                                    >
                                        <!-- Token keamanan CSRF. -->
                                        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                                        <!-- Action untuk operasi Delete. -->
                                        <input type="hidden" name="action" value="catatan_delete">
                                        <!-- ID catatan yang akan dihapus. -->
                                        <input type="hidden" name="id" value="<?= e($row['id_catatan']) ?>">
                                        <!-- Tombol untuk menghapus catatan tidur. -->
                                        <button type="submit" class="btn btn-danger btn-small">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <!--Jika belum terdapat catatan tidur,tampilkan pesan bahwa tabel masih kosong.-->
                    <tr>
                        <td colspan="9" class="empty-state">
                            Belum ada catatan tidur yang tersimpan.
                            Silakan tambahkan catatan melalui form di atas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>