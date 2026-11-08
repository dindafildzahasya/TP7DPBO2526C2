<?php
// Memeriksa apakah pengguna sedang melakukan proses edit.
$isEditing = is_array($editData);
// Menentukan action berdasarkan kondisi form.
$formAction = $isEditing ? 'jadwal_update' : 'jadwal_create';
?>

<section class="page-heading">
    <div>
        <!-- Judul halaman pengelolaan jadwal tidur. -->
        <h2>Jadwal Tidur</h2>
        <!-- Penjelasan singkat mengenai fungsi halaman. -->
        <p>
            Atur target waktu tidur dan bangun mahasiswa
            agar pola tidur menjadi lebih teratur.
        </p>
    </div>
</section>

<section class="section-card">
    <!-- Judul form berubah sesuai kondisi tambah atau edit. -->
    <div class="section-header">
        <div>
            <h3>
                <?= $isEditing ? 'Edit Jadwal Tidur' : 'Tambah Jadwal Tidur' ?>
            </h3>

            <p>
                <?= $isEditing
                    ? 'Perbarui target tidur dan bangun mahasiswa.'
                    : 'Masukkan jadwal tidur yang ingin ditargetkan mahasiswa.' ?>
            </p>
        </div>
    </div>

    <form action="index.php?page=jadwal"
        method="POST" class="form-card"
    >

        <!-- Token digunakan untuk memvalidasi request dari form. -->
        <input type="hidden" name="csrf_token"
            value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
        >

        <!-- Menentukan operasi CRUD yang akan dijalankan. -->
        <input type="hidden" name="action" value="<?= e($formAction) ?>"
        >

        <?php if ($isEditing): ?>
            <input type="hidden" name="id"
                value="<?= e($editData['id_jadwal'] ?? '') ?>"
            >
        <?php endif; ?>

        <div class="form-group">
            <label for="mahasiswa_id">Mahasiswa</label>
            <select id="mahasiswa_id" name="mahasiswa_id" required>
                <!-- Pilihan awal ketika belum ada mahasiswa dipilih. -->
                <option value="">-- Pilih Mahasiswa --</option>

                <?php foreach ($mahasiswaOptions as $option): ?>
                    <option
                        value="<?= e($option['id_mahasiswa']) ?>"
                        <?= ($isEditing &&
                            (int) $editData['mahasiswa_id'] ===
                            (int) $option['id_mahasiswa']
                        ) ? 'selected' : '' ?>
                    >
                        <?= e($option['nim']) ?> -  <?= e($option['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="tanggal">Tanggal Jadwal</label>
            <!--Input date digunakan untuk memilih tanggal jadwal tidur. -->
            <input type="date" id="tanggal" name="tanggal"
                value="<?= e($editData['tanggal'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="target_tidur">Target Jam Tidur</label>
            <!--Input time digunakan untuk menentukan target waktu mahasiswa mulai tidur. -->
            <input type="time" id="target_tidur" name="target_tidur"
                value="<?= e(substr((string) ($editData['target_tidur'] ?? ''), 0, 5)) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="target_bangun">Target Jam Bangun</label>

            <!--Input time digunakan untuk menentukan target waktu mahasiswa bangun tidur.-->
            <input type="time" id="target_bangun" name="target_bangun"
                value="<?= e(substr((string) ($editData['target_bangun'] ?? ''), 0, 5)) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="catatan">Catatan (Opsional)</label>

            <!--Textarea digunakan untuk menambahkan keterangan tambahan pada jadwal tidur.-->
            <textarea id="catatan" name="catatan" rows="3"
                placeholder="Tambahkan catatan mengenai jadwal tidur..."
            ><?= e($editData['catatan'] ?? '') ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $isEditing ? 'Simpan Perubahan' : 'Tambah Jadwal' ?>
            </button>

            <?php if ($isEditing): ?>
                <a
                    href="index.php?page=jadwal"
                    class="btn btn-secondary"
                >
                    Batal
                </a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="section-card">
    <!-- Judul tabel dan jumlah jadwal yang tersimpan. -->
    <div class="section-header">
        <div>
            <h3>Daftar Jadwal Tidur</h3>
            <p>
                Seluruh target waktu tidur dan bangun mahasiswa.
            </p>
        </div>

        <!-- Menampilkan jumlah jadwal tidur. -->
        <span class="data-count">
            Total: <?= e(count($jadwalRows)) ?> jadwal
        </span>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <!-- Bagian kepala tabel. -->
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Tanggal</th>
                    <th>Target Tidur</th>
                    <th>Target Bangun</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <!-- Bagian isi tabel yang menampilkan data jadwal tidur. -->
            <tbody>
                <?php if (!empty($jadwalRows)): ?>
                    <?php foreach ($jadwalRows as $index => $row): ?>
                        <tr>
                            <!-- Menampilkan nomor urut data. -->
                            <td>
                                <?= e($index + 1) ?>
                            </td>
                            <!-- Menampilkan nama mahasiswa pemilik jadwal. -->
                            <td>
                                <?= e($row['nama_mahasiswa']) ?>
                            </td>
                            <!-- Menampilkan NIM mahasiswa. -->
                            <td>
                                <?= e($row['nim']) ?>
                            </td>
                            <!-- Menampilkan tanggal jadwal tidur. -->
                            <td>
                                <?= e(date('d-m-Y', strtotime($row['tanggal']))) ?>
                            </td>
                            <!-- Menampilkan target waktu mulai tidur. -->
                            <td>
                                <?= e(substr($row['target_tidur'], 0, 5)) ?>
                            </td>
                            <!-- Menampilkan target waktu bangun. -->
                            <td>
                                <?= e(substr($row['target_bangun'], 0, 5)) ?>
                            </td>
                            <!-- Menampilkan catatan tambahan. -->
                            <td>
                                <?= e($row['catatan'] ?: '-') ?>
                            </td>

                            <td>
                                <div class="table-actions">
                                    <!-- Tombol Edit mengirim ID jadwal melalui URL. -->
                                    <a
                                        href="index.php?page=jadwal&edit=<?= e($row['id_jadwal']) ?>"
                                        class="btn btn-secondary btn-small"
                                    >
                                        Edit
                                    </a>

                                    <!--Form penghapusan menggunakan metode POST. -->
                                    <form action="index.php?page=jadwal" method="POST"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus jadwal tidur ini?');"
                                    >
                                        <!-- Token keamanan CSRF. -->
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
                                        >
                                        <!-- Action untuk proses Delete. -->
                                        <input
                                            type="hidden"
                                            name="action"
                                            value="jadwal_delete"
                                        >
                                        <!-- ID jadwal yang akan dihapus. -->
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= e($row['id_jadwal']) ?>"
                                        >
                                        <!-- Tombol untuk menghapus jadwal. -->
                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-small"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!--Jika belum terdapat data jadwal tidur-->
                    <tr>
                        <td colspan="8" class="empty-state">
                            Belum ada jadwal tidur yang tersimpan.
                            Silakan tambahkan jadwal melalui form di atas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>