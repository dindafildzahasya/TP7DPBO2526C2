<?php
// Memeriksa apakah pengguna sedang melakukan proses edit.
$isEditing = is_array($editData);
// Jika sedang edit, gunakan mahasiswa_update.
// Jika tidak, gunakan mahasiswa_create.
$formAction = $isEditing ? 'mahasiswa_update' : 'mahasiswa_create';
?>

<section class="page-heading">
    <div>
        <!-- Judul halaman pengelolaan mahasiswa. -->
        <h2>Data Mahasiswa</h2>
        <!-- Penjelasan singkat mengenai halaman. -->
        <p>
            Kelola data mahasiswa yang menggunakan aplikasi RehatSehat.
        </p>
    </div>
</section>

<section class="section-card">
    <!--Judul form berubah sesuai kondisi.-->
    <div class="section-header">
        <div>
            <h3>
                <?= $isEditing ? 'Edit Data Mahasiswa' : 'Tambah Mahasiswa' ?>
            </h3>
            <p>
                <?= $isEditing
                    ? 'Perbarui informasi mahasiswa yang dipilih.'
                    : 'Masukkan informasi mahasiswa untuk menambahkan data baru.' ?>
            </p>
        </div>
    </div>

    <form action="index.php?page=mahasiswa"
        method="POST" class="form-card"
    >
        <!-- Token CSRF digunakan untuk memvalidasi -->
        <input
            type="hidden"
            name="csrf_token"
            value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
        >
        <!-- Menentukan operasi CRUD yang akan dijalankan index.php. -->
        <input
            type="hidden"
            name="action"
            value="<?= e($formAction) ?>"
        >

        <?php if ($isEditing): ?>
            <input type="hidden" name="id"
                value="<?= e($editData['id_mahasiswa'] ?? '') ?>"
            >
        <?php endif; ?>

        <div class="form-group">
            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim"
                placeholder="Masukkan NIM mahasiswa"
                maxlength="30"
                value="<?= e($editData['nim'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="nama">Nama Mahasiswa</label>
            <!-- Input nama mahasiswa. -->
            <input type="text" id="nama" name="nama"
                placeholder="Masukkan nama mahasiswa"
                maxlength="100"
                value="<?= e($editData['nama'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <!--Menggunakan type email agar browser memeriksa format email sebelum form dikirim.-->
            <input type="email" id="email" name="email"
                placeholder="Masukkan email mahasiswa"
                maxlength="120"
                value="<?= e($editData['email'] ?? '') ?>"
                required
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $isEditing ? 'Simpan Perubahan' : 'Tambah Mahasiswa' ?>
            </button>

            <?php if ($isEditing): ?>
                <!--Tombol Batal hanya muncul ketika pengguna sedang melakukan proses edit.-->
                <a
                    href="index.php?page=mahasiswa" class="btn btn-secondary"
                >
                    Batal
                </a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="section-card">
    <!-- Bagian judul tabel dan jumlah data mahasiswa. -->
    <div class="section-header">
        <div>
            <h3>Daftar Mahasiswa</h3>
            <p>
                Berikut merupakan seluruh mahasiswa yang terdaftar
                dalam aplikasi RehatSehat.
            </p>

        </div>
        <!--Menampilkan jumlah seluruh mahasiswa berdasarkan data yang telah diambil oleh index.php.-->
        <span class="data-count">
            Total: <?= e(count($mahasiswaRows)) ?> mahasiswa
        </span>
    </div>

    <div class="table-wrapper">
        <table class="data-table">
            <!-- Kepala tabel berisi nama setiap kolom. -->
            <thead>
                <tr>
                    <th>No.</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <!-- Bagian isi tabel yang berisi data mahasiswa. -->
            <tbody>
                <?php if (!empty($mahasiswaRows)): ?>
                    <!--Melakukan perulangan untuk menampilkan seluruh data mahasiswa yang tersedia.-->
                    <?php foreach ($mahasiswaRows as $index => $row): ?>
                        <tr>
                            <!-- Menampilkan nomor urut mahasiswa. -->
                            <td>
                                <?= e($index + 1) ?>
                            </td>
                            <!-- Menampilkan NIM mahasiswa. -->
                            <td>
                                <?= e($row['nim']) ?>
                            </td>
                            <!-- Menampilkan nama mahasiswa. -->
                            <td>
                                <?= e($row['nama']) ?>
                            </td>
                            <!-- Menampilkan email mahasiswa. -->
                            <td>
                                <?= e($row['email']) ?>
                            </td>

                            <td>
                                <div class="table-actions">
                                    <!--Tombol Edit mengirimkan ID mahasiswa melalui parameter GET.-->
                                    <a
                                        href="index.php?page=mahasiswa&edit=<?= e($row['id_mahasiswa']) ?>"
                                        class="btn btn-secondary btn-small"
                                    >
                                        Edit
                                    </a>
                                    <form
                                        action="index.php?page=mahasiswa"
                                        method="POST"
                                        onsubmit="return confirm('Apakah kamu yakin ingin menghapus data mahasiswa ini? Data jadwal dan catatan tidurnya juga akan terhapus.');"
                                    >
                                        <!-- Token keamanan CSRF. -->
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e($_SESSION['csrf_token'] ?? '') ?>"
                                        >
                                        <!-- Action untuk menentukan operasi Delete. -->
                                        <input
                                            type="hidden"
                                            name="action"
                                            value="mahasiswa_delete"
                                        >
                                        <!-- ID mahasiswa yang akan dihapus. -->
                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= e($row['id_mahasiswa']) ?>"
                                        >
                                        <!-- Tombol untuk menghapus data mahasiswa. -->
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
                    <tr>
                        <td colspan="5" class="empty-state">
                            Belum ada data mahasiswa.
                            Silakan tambahkan data melalui form di atas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>