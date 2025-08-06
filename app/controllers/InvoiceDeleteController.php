<?php
// app/controllers/InvoiceDeleteController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

// Pastikan login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$invoiceId = (int)($_GET['id'] ?? 0);
if (!$invoiceId) {
    $_SESSION['error'] = 'Invoice ID tidak valid.';
    header('Location: index.php?page=report');
    exit;
}

try {
    // Hapus baris‐baris dulu
    $delLines = $conn->prepare("DELETE FROM invoice_line WHERE invoice_id = ?");
    $delLines->execute([$invoiceId]);

    // Baru hapus header invoice
    $delInv = $conn->prepare("DELETE FROM invoice WHERE id = ?");
    $delInv->execute([$invoiceId]);

    $_SESSION['success'] = "Invoice #{$invoiceId} berhasil dihapus.";
} catch (\Exception $e) {
    $_SESSION['error'] = "Gagal menghapus invoice: " . $e->getMessage();
}

// Redirect kembali ke daftar
header('Location: index.php?page=report');
exit;
