<?php
// app/controllers/ReportController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$months   = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$types    = ['BONGKAR','MUAT'];
$gudangs  = $conn
  ->query("SELECT id, nama_gudang FROM gudang ORDER BY nama_gudang")
  ->fetchAll(PDO::FETCH_ASSOC);

$stoList = $conn
  ->query("
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
  ")
  ->fetchAll(PDO::FETCH_ASSOC);

$invoices = $conn
  ->query("
    SELECT i.*, g.nama_gudang
    FROM invoice i
    JOIN gudang g ON i.gudang_id = g.id
    ORDER BY i.created_at DESC
  ")
  ->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../views/layout/header.php';
require_once __DIR__ . '/../views/report/index.php';
require_once __DIR__ . '/../views/layout/footer.php';
