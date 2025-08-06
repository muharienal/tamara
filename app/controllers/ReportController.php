<?php
// app/controllers/ReportController.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: index.php?page=login');
  exit;
}

// Master data
$months   = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$types    = ['BONGKAR','MUAT'];
$gudangs  = $conn->query("SELECT id, nama_gudang FROM gudang ORDER BY nama_gudang")
                ->fetchAll(PDO::FETCH_ASSOC);
$stoList = $conn->query(<<<SQL
  SELECT 
    s.id,
    s.nomor_sto,
    s.tanggal_terbit,
    s.keterangan,
    s.transportir,
    s.tonase_normal,
    s.tonase_lembur,
    g.nama_gudang
  FROM sto s
  JOIN gudang g ON s.gudang_id = g.id
  ORDER BY s.tanggal_terbit DESC
SQL
)->fetchAll(PDO::FETCH_ASSOC);

// Invoice header list
$invoices = $conn->query(<<<SQL
  SELECT i.*, g.nama_gudang
  FROM invoice i
  JOIN gudang g ON i.gudang_id = g.id
  ORDER BY i.created_at DESC
SQL
)->fetchAll(PDO::FETCH_ASSOC);

// Prepare per‐invoice data for JS
$invoiceData  = [];
$invoiceLines = [];
foreach ($invoices as $inv) {
  $id = $inv['id'];
  // header fields
  $invoiceData[$id] = [
    'bulan'            => $inv['bulan'],
    'jenis_pupuk'      => $inv['jenis_pupuk'],
    'gudang_id'        => $inv['gudang_id'],
    'jenis_transaksi'  => $inv['jenis_transaksi'],
    'uraian_pekerjaan' => $inv['uraian_pekerjaan'],
    'tarif_normal'     => $inv['tarif_normal'],
    'tarif_lembur'     => $inv['tarif_lembur']
  ];
  // lines
  $stmt = $conn->prepare("SELECT sto_id FROM invoice_line WHERE invoice_id=? ORDER BY id");
  $stmt->execute([$id]);
  $invoiceLines[$id] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

require_once __DIR__ . '/../views/layout/header.php';
require_once __DIR__ . '/../views/report/index.php';
require_once __DIR__ . '/../views/layout/footer.php';
