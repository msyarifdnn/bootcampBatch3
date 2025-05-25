<?php include '../database/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Ambil data dari form
  $nama = trim($_POST['nama'] ?? '');
  $deskripsi = trim($_POST['deskripsi'] ?? '');
  $kategori = trim($_POST['kategori'] ?? '');
  $harga = $_POST['harga'] ?? '';
  $stok = $_POST['stok'] ?? '';

  // Validasi data
  if ($nama === '' || $deskripsi === '' || $kategori === '' || $harga === '' || $stok === '') {
    $error = "Semua field harus diisi.";
  } elseif (!is_numeric($harga) || $harga < 0) {
    $error = "Harga harus berupa angka positif.";
  } elseif (!is_numeric($stok) || $stok < 0) {
    $error = "Stok harus berupa angka positif.";
  } elseif (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
    $error = "Gambar produk wajib diunggah.";
  } else {
    // Proses upload gambar
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
        // Simpan ke database
        $stmt = $conn->prepare("INSERT INTO products (nama_produk, harga, deskripsi, stok, kategori, gambar) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsiss", $nama, $harga, $deskripsi, $stok, $kategori, $newName);
        // "s"=string, "d"=double (for harga), "s"=string, "i"=integer (for stok), "s"=string (kategori), "s"=string (gambar)
        if ($stmt->execute()) {
          $success = "Produk berhasil ditambahkan.";
        } else {
          $error = "Gagal menambahkan produk: " . $conn->error;
        }
        $stmt->close();
      } else {
        $error = "Gagal mengunggah gambar.";
      }
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
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link fw-semibold" aria-current="page" href="/">
              <i class="bi bi-house-door-fill me-1"></i>Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="#">
              <i class="bi bi-plus-square-fill me-1"></i>Tambah Produk
            </a>
          </li>
        </ul>
        <form class="d-flex position-relative me-3" role="search">
          <input class="form-control me-2 rounded-pill ps-4 border-primary" type="search" placeholder="Cari produk..." aria-label="Search" style="min-width:180px;">
          <button class="btn btn-primary rounded-pill px-4" type="submit">
            <i class="bi bi-search"></i> Cari
          </button>
        </form>
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="login.php">
              <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<!-- notif sukses dan error -->
<?php if ($error): ?>
  <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
  <div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php
if ($error || $success) {
  header("Refresh: 4; url=create.php");
}
?>

<!-- form tambah -->
<div class="container col-md-6 col-lg-5">
  <div class="card mt-5 shadow-lg border-0">
    <div class="card-header bg-gradient bg-primary">
      <h2 class="text-white text-center mb-0">Tambah Produk</h2>
    </div>
    <div class="card-body bg-light">
      <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label for="nama" class="form-label fw-semibold">Nama Produk</label>
          <input type="text" class="form-control rounded-pill" id="nama" name="nama" required placeholder="Masukkan nama produk">
        </div>
        <div class="mb-3">
          <label for="deskripsi" class="form-label fw-semibold">Deskripsi Produk</label>
          <textarea class="form-control rounded-4" id="deskripsi" name="deskripsi" rows="3" required placeholder="Deskripsi singkat produk"></textarea>
        </div>
        <div class="mb-3">
          <label for="kategori" class="form-label fw-semibold">Kategori</label>
          <select class="form-select rounded-pill" id="kategori" name="kategori" required>
            <option value="" disabled selected>Pilih kategori produk</option>
            <option value="Elektronik">Elektronik</option>
            <option value="Fashion">Fashion</option>
            <option value="Kesehatan">Kesehatan</option>
            <option value="Kecantikan">Kecantikan</option>
            <option value="Olahraga">Olahraga</option>
            <option value="Rumah Tangga">Rumah Tangga</option>
            <option value="Makanan & Minuman">Makanan & Minuman</option>
            <option value="Buku">Buku</option>
            <option value="Mainan">Mainan</option>
            <option value="Otomotif">Otomotif</option>
            <option value="Perlengkapan Bayi">Perlengkapan Bayi</option>
            <option value="Lainnya">Lainnya</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="gambar" class="form-label fw-semibold">Gambar Produk</label>
          <input type="file" class="form-control rounded-pill" id="gambar" name="gambar" accept="image/*" required>
        </div>
        <div class="row">
          <div class="col-md-6 mb-4">
            <label for="harga" class="form-label fw-semibold">Harga Produk</label>
            <input type="number" class="form-control rounded-pill" id="harga" name="harga" required placeholder="Harga">
          </div>
          <div class="col-md-6 mb-4">
            <label for="stok" class="form-label fw-semibold">Stok Produk</label>
            <input type="number" class="form-control rounded-pill" id="stok" name="stok" required placeholder="Stok">
          </div>
        </div>
        <div class="d-grid">
          <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Tambah Produk
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

</body>
</html>