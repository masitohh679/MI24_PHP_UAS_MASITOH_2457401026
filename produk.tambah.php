<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once __DIR__ . '/../includes/auth.php';

$error = "";

$kategori_list = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_produk = trim($_POST['kode_produk']);
    $nama_produk = trim($_POST['nama_produk']);
    $kategori_id = (int) $_POST['kategori_id'];
    $deskripsi   = trim($_POST['deskripsi']);
    $gambar_name = null;

    if ($kode_produk === '' || $nama_produk === '' || $kategori_id === 0) {
        $error = "Kode produk, nama produk, dan kategori wajib diisi";
    } elseif (!empty($_FILES['gambar']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = "Format gambar harus JPG, PNG, atau WEBP";
        } elseif ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            $error = "Ukuran gambar maksimal 2MB";
        } else {
            $gambar_name = uniqid('produk_') . '.' . $ext;
            $target = __DIR__ . '/../assets/uploads/' . $gambar_name;
            if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                $error = "Gagal mengunggah gambar";
            }
        }
    }

    if ($error === '') {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO produk (kode_produk, nama_produk, kategori_id, deskripsi, gambar)
            VALUES (?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, "ssiss", $kode_produk, $nama_produk, $kategori_id, $deskripsi, $gambar_name);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: produk.php?added=1");
            exit;
        } else {
            $error = mysqli_error($conn) === "Duplicate entry '$kode_produk' for key 'kode_produk'"
                ? "Kode produk sudah digunakan"
                : "Gagal menyimpan produk";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk — Katalog Online</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <p class="eyebrow">Kelola Katalog</p>
                    <h1>Tambah Produk</h1>
                    <p>Masukkan detail produk baru untuk katalog.</p>
                </div>
            </div>

            <div class="form-panel">
                <?php if ($error): ?>
                    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="produk_tambah.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="field-group">
                            <label>Kode Produk</label>
                            <input type="text" name="kode_produk" placeholder="Contoh: P06"
                                   value="<?= isset($_POST['kode_produk']) ? htmlspecialchars($_POST['kode_produk']) : '' ?>" required>
                        </div>
                        <div class="field-group">
                            <label>Kategori</label>
                            <select name="kategori_id" required>
                                <option value="">Pilih kategori</option>
                                <?php while ($k = mysqli_fetch_assoc($kategori_list)): ?>
                                    <option value="<?= $k['id'] ?>"
                                        <?= (isset($_POST['kategori_id']) && $_POST['kategori_id'] == $k['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($k['nama_kategori']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" placeholder="Contoh: Blouse Wanita Katun"
                               value="<?= isset($_POST['nama_produk']) ? htmlspecialchars($_POST['nama_produk']) : '' ?>" required>
                    </div>

                    <div class="field-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" placeholder="Deskripsi singkat produk"><?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?></textarea>
                    </div>

                    <div class="field-group">
                        <label>Gambar Produk</label>
                        <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp">
                        <p class="hint">Opsional. Format JPG/PNG/WEBP, maksimal 2MB.</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Simpan Produk</button>
                        <a href="produk.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
