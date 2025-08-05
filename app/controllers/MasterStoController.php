<?php
// app/controllers/MasterStoController.php

// 1) bootstrap session & connect
if (session_status()===PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../config/database.php';

// 2) protect
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// 3) AJAX: fetch one STO row as JSON
if (isset($_GET['action']) && $_GET['action']==='get' && !empty($_GET['id'])) {
    $stmt = $conn->prepare("
      SELECT s.*, g.nama_gudang
      FROM sto s
      JOIN gudang g ON s.gudang_id = g.id
      WHERE s.id = :id
    ");
    $stmt->execute(['id'=>$_GET['id']]);
    header('Content-Type: application/json');
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    exit;
}

// 4) AJAX: save edits
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action'] ?? '')==='update') {
    $upd = $conn->prepare("
      UPDATE sto SET
        nomor_sto       = :nomor_sto,
        tanggal_terbit  = :tanggal_terbit,
        gudang_id       = :gudang_id,
        jenis_transaksi = :jenis_transaksi,
        tonase_normal   = :tonase_normal,
        tonase_lembur   = :tonase_lembur,
        transportir     = :transportir,
        keterangan      = :keterangan,
        status          = :status
      WHERE id = :id
    ");
    $upd->execute([
      'nomor_sto'       => $_POST['nomor_sto'],
      'tanggal_terbit'  => $_POST['tanggal_terbit'],
      'gudang_id'       => $_POST['gudang_id'],
      'jenis_transaksi' => $_POST['jenis_transaksi'],
      'tonase_normal'   => $_POST['tonase_normal'],
      'tonase_lembur'   => $_POST['tonase_lembur'],
      'transportir'     => $_POST['transportir'],
      'keterangan'      => $_POST['keterangan'],
      'status'          => $_POST['status'],
      'id'              => $_POST['id'],
    ]);
    echo json_encode(['success'=>true]);
    exit;
}

// 5) Delete
if (isset($_GET['delete'])) {
  $del = $conn->prepare("DELETE FROM sto WHERE id=?");
  $del->execute([$_GET['delete']]);
  $_SESSION['success']="STO terhapus.";
  header('Location: index.php?page=master_sto');
  exit;
}

// 6) Insert new STO (regular POST, no "action")
if ($_SERVER['REQUEST_METHOD']==='POST' && !isset($_POST['action'])) {
  // duplicate check
  $chk = $conn->prepare("SELECT id FROM sto WHERE nomor_sto=?");
  $chk->execute([$_POST['nomor_sto']]);
  if ($chk->rowCount()) {
    $_SESSION['error']="Nomor STO sudah terdaftar!";
    header('Location: index.php?page=master_sto');
    exit;
  }
  $ins = $conn->prepare("
    INSERT INTO sto (
      nomor_sto,tanggal_terbit,keterangan,
      gudang_id,jenis_transaksi,transportir,
      tonase_normal,tonase_lembur,status
    ) VALUES (
      :nomor_sto,:tanggal_terbit,:keterangan,
      :gudang_id,:jenis_transaksi,:transportir,
      :tonase_normal,:tonase_lembur,'NOT_USED'
    )
  ");
  $ins->execute([
    'nomor_sto'=>$_POST['nomor_sto'],
    'tanggal_terbit'=>$_POST['tanggal_terbit'],
    'keterangan'=>$_POST['keterangan']?:null,
    'gudang_id'=>$_POST['gudang_id'],
    'jenis_transaksi'=>$_POST['jenis_transaksi'],
    'transportir'=>$_POST['transportir'],
    'tonase_normal'=>$_POST['tonase_normal'],
    'tonase_lembur'=>$_POST['tonase_lembur'],
  ]);
  $_SESSION['success']="STO berhasil didaftarkan!";
  header('Location: index.php?page=master_sto');
  exit;
}

// 7) Load data for page
$gudangs = $conn
  ->query("SELECT id,nama_gudang FROM gudang ORDER BY nama_gudang")
  ->fetchAll(PDO::FETCH_ASSOC);

$stoList = $conn
  ->query("
    SELECT s.*, g.nama_gudang,
           s.tonase_normal + s.tonase_lembur AS jumlah
      FROM sto s
      JOIN gudang g ON s.gudang_id=g.id
     ORDER BY s.created_at DESC
  ")
  ->fetchAll(PDO::FETCH_ASSOC);

// 8) Render
require_once __DIR__.'/../views/layout/header.php';
require_once __DIR__.'/../views/sto/master.php';
require_once __DIR__.'/../views/layout/footer.php';
