<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once "config.php";

if (isset($_SESSION['username'])) {
    header("Location: admin/index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: admin/index.php");
        exit;
    } else {
        $error = "Username / Password tidak sesuai";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Katalog Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-shell">
            <div class="login-brand">
                <div>
                    <p class="mark">Katalog Online</p>
                    <h2>Kelola katalog produk Anda di satu tempat.</h2>
                </div>
                <div class="swatch-strip">
                    <span class="swatch swatch--baju"><span class="dot"></span>Baju</span>
                    <span class="swatch swatch--tas"><span class="dot"></span>Tas</span>
                    <span class="swatch swatch--sepatu"><span class="dot"></span>Sepatu</span>
                    <span class="swatch swatch--aksesoris"><span class="dot"></span>Aksesoris</span>
                    <span class="swatch swatch--kosmetik"><span class="dot"></span>Kosmetik</span>
                </div>
            </div>

            <div class="login-box">
                <h2>Login</h2>

                <?php if ($error): ?>
                    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="login.php">
                    <input type="text" name="username" placeholder="Username" required autofocus>
                    <input type="password" name="password" placeholder="Password" required>
                    <div class="actions">
                        <a href="index.php">&larr; Kembali</a>
                        <button type="submit" class="btn">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
