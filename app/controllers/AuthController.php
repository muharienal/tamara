<?php
// Jika form login dikirim (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil user dari database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Cek password
    if ($user && password_verify($password, $user['password'])) {
        // Simpan session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect ke dashboard
        header('Location: index.php?page=dashboard');
        exit;
    } else {
        // Simpan error
        $_SESSION['error'] = 'Username atau password salah!';
        header('Location: index.php?page=login');
        exit;
    }
}

// Tampilkan form login
require_once __DIR__ . '/../views/layout/header.php';
?>
<div class="container mt-5" style="max-width:400px;">
    <h2 class="text-center mb-4">Login TAMARA</h2>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="index.php?page=login">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
<?php
require_once __DIR__ . '/../views/layout/footer.php';
