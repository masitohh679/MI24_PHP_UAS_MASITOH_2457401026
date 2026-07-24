<?php
/**
 * NIM   : 2457401026
 * Nama  : Masitoh
 * Kelas : MI24
 */

require_once __DIR__ . '/../includes/auth.php';

if (!isset($_GET['id'])) {
    header("Location: produk.php");
    exit;
}

$id = (int) $_GET['id'];
$error = "";

$stmt = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$produk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$produk) {
    header("Location: produk.php");
    exit;
}

$kategori_list = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_produk = trim($_POST['kode_produk']);
    $nama_produk = trim($_POST['nama_produk']);
    $kategori_id = (int) $_POST['kategori_id'];
    $deskripsi   = trim($_POST['deskripsi']);
    $gambar_name = $produk['gambar'];

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
            $new_name = uniqid('produk_') . '.' . $ext;
            $target = __DIR__ . '/../assets/uploads/' . $new_name;
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                if ($gambar_name) {
                    $old = __DIR__ . '/../assets/uploads/' . $gambar_name;
                    if (file_exists($old)) unlink($old);
                }
                $gambar_name = $new_name;
            } else {
                $error = "Gagal mengunggah gambar";
            }
        }
    }

    if ($error === '') {
        $stmt = mysqli_prepare($conn, "
            UPDATE produk
            SET kode_produk = ?, nama_produk = ?, kategori_id = ?, deskripsi = ?, gambar = ?
            WHERE id = ?
        ");
        mysqli_stmt_bind_param($stmt, "ssissi", $kode_produk, $nama_produk, $kategori_id, $deskripsi, $gambar_name, $id);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: produk.php?updated=1");
            exit;
        } else {
            $error = "Gagal menyimpan perubahan. Pastikan kode produk unik.";
        }
    }

    // keep values on screen if there was an error
    $produk = array_merge($produk, [
        'kode_produk' => $kode_produk,
        'nama_produk' => $nama_produk,
        'kategori_id' => $kategori_id,
        'deskripsi'   => $deskripsi,
    ]);
    mysqli_data_seek($kategori_list, 0);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk — Katalog Online</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-shell">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-header">
                <div>
                    <p class="eyebrow">Kelola Katalog</p>
                    <h1>Edit Produk</h1>
                    <p>Perbarui detail untuk <?= htmlspecialchars($produk['nama_produk']) ?>.</p>
                </div>
            </div>

            <div class="form-panel">
                <?php if ($error): ?>
                    <div class="alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($produk['gambar']): ?>
                    <div class="field-group">
                        <label>Gambar Saat Ini</label>
                        <img class="thumb" style="width:80px; height:80px;"
                             src="../assets/uploads/<?= htmlspecialchars($produk['gambar']) ?>" alt="">
                    </div>
                <?php endif; ?>

                <form method="post" action="produk_edit.php?id=<?= $produk['id'] ?>" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="field-group">
                            <label>Kode Produk</label>
                            <input type="text" name="kode_produk" value="<?= htmlspecialchars($produk['kode_produk']) ?>" required>
                        </div>
                        <div class="field-group">
                            <label>Kategori</label>
                            <select name="kategori_id" required>
                                <?php while ($k = mysqli_fetch_assoc($kategori_list)): ?>
                                    <option value="<?= $k['id'] ?>" <?= $k['id'] == $produk['kategori_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($k['nama_kategori']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="field-group">
                        <label>Nama Produk</label>
                        <input type="text" name="nama_produk" value="<?= htmlspecialchars($produk['nama_produk']) ?>" required>
                    </div>

                    <div class="field-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi"><?= htmlspecialchars($produk['deskripsi']) ?></textarea>
                    </div>

                    <div class="field-group">
                        <label>Ganti Gambar</label>
                        <input type="file" name="gambar" accept=".jpg,.jpeg,.png,.webp">
                        <p class="hint">Opsional. Biarkan kosong untuk mempertahankan gambar saat ini.</p>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn">Simpan Perubahan</button>
                        <a href="produk.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
