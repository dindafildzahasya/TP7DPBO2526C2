<?php

function e(mixed $value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* Menyimpan pesan notifikasi sementara ke dalam session. */
function setFlash(string $type, string $message): void {
    // Memastikan session sudah dimulai sebelum digunakan.
    if (session_status() !== PHP_SESSION_ACTIVE) {
        throw new LogicException('Session belum dimulai. Panggil session_start() sebelum setFlash().');
    }
    // Menyimpan jenis dan isi pesan dalam session.
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function redirectTo(string $page): never {
    // Mengirim header Location agar browser membuka halaman tujuan.
    header('Location: index.php?page=' . urlencode($page));
    // Menghentikan script agar kode setelah redirect tidak dijalankan.
    exit;
}