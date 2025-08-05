<?php
// app/controllers/DashboardController.php

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Ambil data user dari session
$username = $_SESSION['username'];
$role     = $_SESSION['role'];

require_once __DIR__ . '/../views/layout/header.php';
?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Dashboard TAMARA</h1>
        <div>
            <span class="me-3">Halo, <strong><?= htmlspecialchars($username) ?></strong> (<?= htmlspecialchars($role) ?>)</span>
            <a href="index.php?page=logout" class="btn btn-sm btn-danger">Logout</a>
        </div>
    </div>

    <div class="row">

        <!-- Card Master STO -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Master STO</h5>
                    <p class="card-text">Kelola, edit, hapus, dan filter data STO.</p>
                    <a href="index.php?page=master_sto" class="btn btn-primary">Buka</a>
                </div>
            </div>
        </div>

        <!-- Card Scan -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Scan Tagihan</h5>
                    <p class="card-text">Scan QR untuk approval tagihan.</p>
                    <a href="index.php?page=scan" class="btn btn-primary">Buka</a>
                </div>
            </div>
        </div>

        <!-- Card Laporan -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Laporan</h5>
                    <p class="card-text">Lihat laporan tagihan dan status approval.</p>
                    <a href="index.php?page=report" class="btn btn-primary">Buka</a>
                </div>
            </div>
        </div>

        <!-- Card Master Gudang (Hanya Superadmin) -->
        <?php if ($role === 'SUPERADMIN'): ?>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title">Master Gudang</h5>
                    <p class="card-text">
                        Kelola daftar nama gudang serta tarif.
                    </p>
                    <a href="index.php?page=gudang" class="btn btn-primary">Buka</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/../views/layout/footer.php';
