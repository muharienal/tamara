<?php
// app/controllers/ReportGenerateController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// 1) Collect & validate POST
$bulan            = $_POST['bulan']            ?? '';
$jenis_pupuk      = $_POST['jenis_pupuk']      ?? '';
$gudang_id        = $_POST['gudang_id']        ?? null;
$jenis_transaksi  = $_POST['jenis_transaksi']  ?? '';
$uraian_pekerjaan = $_POST['uraian_pekerjaan'] ?? '';
$tarif_normal     = (float)($_POST['tarif_normal'] ?? 0);
$tarif_lembur     = (float)($_POST['tarif_lembur'] ?? 0);
$sto_ids          = $_POST['sto_ids']          ?? [];

if (
    !$bulan || !$jenis_pupuk || !$gudang_id
  || !$jenis_transaksi || !$uraian_pekerjaan
  || !$tarif_normal || !$tarif_lembur
  || !is_array($sto_ids) || count($sto_ids) === 0
) {
    $_SESSION['error'] = "Semua field wajib diisi, dan pilih minimal 1 STO.";
    header('Location: index.php?page=report');
    exit;
}

// 2) Compute grand totals based on tonase × tarif
$totalBN  = 0.0;
$totalBL  = 0.0;

$stmtSto = $conn->prepare("
  SELECT tonase_normal, tonase_lembur 
  FROM sto 
  WHERE id = :sto_id
");

foreach ($sto_ids as $sid) {
    $stmtSto->execute(['sto_id' => $sid]);
    $row = $stmtSto->fetch(PDO::FETCH_ASSOC);
    if (!$row) continue;
    $totalBN += ((float)$row['tonase_normal']) * $tarif_normal;
    $totalBL += ((float)$row['tonase_lembur']) * $tarif_lembur;
}
$grandTotal = $totalBN + $totalBL;

// 3) Insert into invoice
$insInv = $conn->prepare("
  INSERT INTO invoice
    (bulan, jenis_pupuk, gudang_id, jenis_transaksi, uraian_pekerjaan,
     tarif_normal, tarif_lembur,
     total_bongkar_normal, total_bongkar_lembur, total)
  VALUES
    (:bulan, :jpupuk, :gid, :jtrans, :uraian,
     :tnorm, :tlemb,
     :tbn, :tbl, :tot)
");
$insInv->execute([
  'bulan'   => $bulan,
  'jpupuk'  => $jenis_pupuk,
  'gid'     => $gudang_id,
  'jtrans'  => $jenis_transaksi,
  'uraian'  => $uraian_pekerjaan,
  'tnorm'   => $tarif_normal,
  'tlemb'   => $tarif_lembur,
  'tbn'     => $totalBN,
  'tbl'     => $totalBL,
  'tot'     => $grandTotal,
]);

$invoiceId = $conn->lastInsertId();

// 4) Link each STO row in invoice_line
$insLine = $conn->prepare("
  INSERT INTO invoice_line (invoice_id, sto_id)
  VALUES (:iid, :sid)
");
foreach ($sto_ids as $sid) {
    $insLine->execute([
      'iid' => $invoiceId,
      'sid' => $sid,
    ]);
}

// 5) (Optional) generate QR here if you have a library…

$_SESSION['success'] = "Invoice #{$invoiceId} berhasil dibuat!";
header('Location: index.php?page=report');
exit;
