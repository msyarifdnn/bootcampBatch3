<?php
include '../database/config.php';

$error = '';
$success = '';
$id = $_GET['id'] ?? '';



// Ambil data produk lama
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama'] ?? '');
  $deskripsi = trim($_POST['deskripsi'] ?? '');
  $kategori = trim($_POST['kategori'] ?? '');
  $harga = $_POST['harga'] ?? '';
  $stok = $_POST['stok'] ?? '';
  $gambarBaru = $product['gambar'];

  // Validasi data
  if ($nama === '' || $deskripsi === '' || $kategori === '' || $harga === '' || $stok === '') {
    $error = "Semua field harus diisi.";
  } elseif (!is_numeric($harga) || $harga < 0) {
    $error = "Harga harus berupa angka positif.";
  } elseif (!is_numeric($stok) || $stok < 0) {
    $error = "Stok harus berupa angka positif.";
  } else {
    // Cek jika ada upload gambar baru
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
      $gambar = $_FILES['gambar'];
      $ext = strtolower(pathinfo($gambar['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
      if (!in_array($ext, $allowed)) {
        $error = "Format gambar tidak didukung.";
      } else {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
          mkdir($uploadDir, 0777, true);
        }
        $newName = uniqid('img_', true) . '.' . $ext;
        $uploadPath = $uploadDir . $newName;
        if (move_uploaded_file($gambar['tmp_name'], $uploadPath)) {
          // Hapus gambar lama jika ada
          if ($product['gambar'] && file_exists($uploadDir . $product['gambar'])) {
            unlink($uploadDir . $product['gambar']);
          }
          $gambarBaru = $newName;
        } else {
          $error = "Gagal mengunggah gambar.";
        }
      }
    }
    // Jika tidak ada error, update data
    if (!$error) {
      $stmt = $conn->prepare("UPDATE products SET nama_produk=?, harga=?, deskripsi=?, stok=?, kategori=?, gambar=? WHERE id=?");
      $stmt->bind_param("sdsissi", $nama, $harga, $deskripsi, $stok, $kategori, $gambarBaru, $id);
      if ($stmt->execute()) {
        $success = "Produk berhasil diupdate.";
        // Refresh data produk
        $product['nama_produk'] = $nama;
        $product['deskripsi'] = $deskripsi;
        $product['kategori'] = $kategori;
        $product['harga'] = $harga;
        $product['stok'] = $stok;
        $product['gambar'] = $gambarBaru;
      } else {
        $error = "Gagal mengupdate produk: " . $conn->error;
      }
      $stmt->close();
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Admin | Tambah Produk</title>
</head>

<style>
  body {
    background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
    min-height: 100vh;
  }
  .card {
    border-radius: 1.5rem;
  }
  .card-header {
    border-top-left-radius: 1.5rem !important;
    border-top-right-radius: 1.5rem !important;
    background: linear-gradient(90deg, #6366f1 0%, #2563eb 100%);
  }
  .form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(99,102,241,.25);
    border-color: #6366f1;
  }
  .btn-primary {
    background: linear-gradient(90deg, #6366f1 0%, #2563eb 100%);
    border: none;
  }
  .btn-primary:hover {
    background: linear-gradient(90deg, #2563eb 0%, #6366f1 100%);
  }
</style>

<body>
<!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center" href="/" style="letter-spacing:2px;">
        <img src="https://cdn-icons-png.flaticon.com/512/1170/1170678.png" alt="Logo" width="36" height="36" class="me-2">
        <span style="font-size:1.5rem; color:#0d6efd;">
          ARshop
        </span>
      </a>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link fw-semibold" aria-current="page" href="../admin/">
              <i class="bi bi-house-door-fill me-1"></i>Dashboard
            </a>
          </li>
      </ul>
    </div>
  </nav>

<!-- notif produk tidak ditemukan -->

<?php if (!$id || !is_numeric($id)) : ?>
  <div class="alert alert-danger text-center"><?= "ID produk tidak valid."; ?></div>
<?php elseif (!$product) : ?>
  <div class="alert alert-danger text-center"><?= "Produk tidak ditemukan." ?></div>
<?php endif ?>

<!-- notif sukses dan error -->
<?php if ($error): ?>
  <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
  <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php
if ($error || $success) {
  header("Refresh: 2; url=index.php");
}
?>

<!-- form edit -->
<?php if ($product) : ?>
<div class="container col-md-6 col-lg-5">
  <div class="card mt-5 shadow-lg border-0">
    <div class="card-header bg-gradient bg-primary">
      <h2 class="text-white text-center mb-0">Edit Produk</h2>
    </div>
    <div class="card-body bg-light">
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="nama" class="form-label fw-semibold">Nama Produk</label>
          <input type="text" class="form-control rounded-pill" id="nama" name="nama" required placeholder="Masukkan nama produk"
            value="<?= htmlspecialchars($product['nama_produk']) ?>">
        </div>
        <div class="mb-3">
          <label for="deskripsi" class="form-label fw-semibold">Deskripsi Produk</label>
          <textarea class="form-control rounded-4" id="deskripsi" name="deskripsi" rows="3" required placeholder="Deskripsi singkat produk"><?= htmlspecialchars($product['deskripsi']) ?></textarea>
        </div>
        <div class="mb-3">
          <label for="kategori" class="form-label fw-semibold">Kategori</label>
          <select class="form-select rounded-pill" id="kategori" name="kategori" required>
            <option value="" disabled>Pilih kategori produk</option>
            <?php
              $kategoriList = [
                "Elektronik", "Fashion", "Kesehatan", "Kecantikan", "Olahraga",
                "Rumah Tangga", "Makanan & Minuman", "Buku", "Mainan", "Otomotif",
                "Perlengkapan Bayi", "Lainnya"
              ];
              foreach ($kategoriList as $kat) {
                $selected = ($product['kategori'] === $kat) ? 'selected' : '';
                echo "<option value=\"$kat\" $selected>$kat</option>";
              }
            ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="gambar" class="form-label fw-semibold">Gambar Produk</label>
          <?php if ($product['gambar']): ?>
            <div class="mb-2">
              <img src="../uploads/<?= htmlspecialchars($product['gambar']) ?>" alt="Gambar Produk" style="max-width:120px;max-height:120px;border-radius:10px;">
            </div>
          <?php endif; ?>
          <input type="file" class="form-control rounded-pill" id="gambar" name="gambar" accept="image/*">
          <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar.</small>
        </div>
        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="harga" class="form-label fw-semibold">Harga Produk</label>
            <input type="number" class="form-control rounded-pill" id="harga" name="harga" required placeholder="Harga"
              value="<?= htmlspecialchars($product['harga']) ?>">
          </div>
          <div class="col-md-6 mb-4">
            <label for="stok" class="form-label fw-semibold">Stok Produk</label>
            <input type="number" class="form-control rounded-pill" id="stok" name="stok" required placeholder="Stok"
              value="<?= htmlspecialchars($product['stok']) ?>">
          </div>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
            <i class="bi bi-save me-2"></i>Update Produk
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif ?>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


</body>
</html>