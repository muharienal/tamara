<?php
// app/controllers/ReportUpdateController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

// Pastikan login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Ambil ID dari query string
$invoiceId = (int)($_GET['id'] ?? 0);
if (!$invoiceId) {
    $_SESSION['error'] = 'Invoice ID tidak valid.';
    header('Location: index.php?page=report');
    exit;
}

// Validasi & ambil data POST
$bulan            = $_POST['bulan']            ?? '';
$jenisPupuk       = $_POST['jenis_pupuk']      ?? '';
$gudangId         = (int)($_POST['gudang_id']  ?? 0);
$jenisTransaksi   = $_POST['jenis_transaksi']  ?? '';
$uraian           = $_POST['uraian_pekerjaan'] ?? '';
$tarifNormal      = (float)($_POST['tarif_normal'] ?? 0);
$tarifLembur      = (float)($_POST['tarif_lembur'] ?? 0);
$stoIds           = $_POST['sto_ids']          ?? [];

// Mulai transaksi
$conn->beginTransaction();

try {
    // 1) Update header invoice
    $upd = $conn->prepare("
        UPDATE invoice
        SET bulan            = ?,
            jenis_pupuk      = ?,
            gudang_id        = ?,
            jenis_transaksi  = ?,
            uraian_pekerjaan = ?,
            tarif_normal     = ?,
            tarif_lembur     = ?
        WHERE id = ?
    ");
    $upd->execute([
        $bulan,
        $jenisPupuk,
        $gudangId,
        $jenisTransaksi,
        $uraian,
        $tarifNormal,
        $tarifLembur,
        $invoiceId
    ]);

    // 2) Hapus semua baris lama
    $del = $conn->prepare("DELETE FROM invoice_line WHERE invoice_id = ?");
    $del->execute([$invoiceId]);

    // 3) Insert ulang baris yang dipilih
    $ins = $conn->prepare("
        INSERT INTO invoice_line (invoice_id, sto_id)
        VALUES (?, ?)
    ");
    foreach ($stoIds as $stoId) {
        $ins->execute([$invoiceId, (int)$stoId]);
    }

    $conn->commit();

    $_SESSION['success'] = "Invoice #{$invoiceId} berhasil di‐update.";
} catch (\Exception $e) {
    $conn->rollBack();
    $_SESSION['error'] = "Gagal update invoice: " . $e->getMessage();
}

// Redirect kembali ke list
header('Location: index.php?page=report');
exit;
