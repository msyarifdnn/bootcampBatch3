<?php
include "database/config.php";

session_start();

// Ambil data produk dari database
$products = [];
$sql = "SELECT id, nama_produk, harga FROM products";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $products[$row['id']] = [
      'nama_produk' => $row['nama_produk'],
      'harga' => $row['harga']
    ];
  }
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Tambah produk ke keranjang
if (isset($_GET['add'])) {
  $id = (int)$_GET['add'];
  if (isset($products[$id])) {
    if (isset($_SESSION['cart'][$id])) {
      $_SESSION['cart'][$id]++;
    } else {
      $_SESSION['cart'][$id] = 1;
    }
  }
  header('Location: cart.php');
  exit;
}

// Hapus produk dari keranjang
if (isset($_GET['remove'])) {
  $id = (int)$_GET['remove'];
  unset($_SESSION['cart'][$id]);
  header('Location: cart.php');
  exit;
}

// Update jumlah produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  foreach ($_POST['qty'] as $id => $qty) {
    $qty = (int)$qty;
    if ($qty > 0) {
      $_SESSION['cart'][$id] = $qty;
    } else {
      unset($_SESSION['cart'][$id]);
    }
  }
  header('Location: cart.php');
  exit;
}
?>
<!DOCTYPE html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<html lang="id">
<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>ARshop | Keranjang Belanja</title>
  <style>
    body {
      background: linear-gradient(135deg, #e0e7ff 0%, #f8fafc 100%);
      min-height: 100vh;
    }
    table { border-collapse: collapse; width: 60%; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background: #f2f2f2; }
    .actions { margin-top: 20px; }
  </style>
</head>
<body>
<!-- navbar -->
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
              <i class="bi bi-house-door-fill me-1"></i>Beranda
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active fw-semibold" href="cart.php">
              <i class="bi bi-cart-fill me-1"></i>Keranjang
            </a>
          </li>
        </ul>
        <form class="d-flex position-relative me-3" role="search" method="get" action="">
          <input 
            class="form-control me-2 rounded-pill ps-4 border-primary" 
            type="search" 
            placeholder="Cari produk..." 
            aria-label="Search" 
            style="min-width:180px;" 
            name="cari"
            value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>"
          >
          <button class="btn btn-primary rounded-pill px-4" type="submit">
            <i class="bi bi-search"></i> Cari
          </button>
        </form>
        <ul class="navbar-nav mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="admin/">
              <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<!-- keranjang-->
  <div class="container my-5">
    <h1 class="mb-4 text-primary"><i class="bi bi-cart-fill me-2"></i>Keranjang Belanja</h1>
    <form method="post">
      <div class="table-responsive">
        <div style="min-width:400px; overflow-x:auto;">
        <table class="table table-bordered align-middle mb-0" style="min-width:600px;">
          <thead class="table-light">
            <tr>
              <th style="min-width:70px;">Gambar</th>
              <th style="min-width:120px;">Produk</th>
              <th style="min-width:90px;">Harga</th>
              <th style="min-width:90px;">Jumlah</th>
              <th style="min-width:100px;">Subtotal</th>
              <th style="min-width:80px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $total = 0;
          if (!empty($_SESSION['cart'])):
            foreach ($_SESSION['cart'] as $id => $qty):
              if (!isset($products[$id])) continue;
              // Ambil gambar produk dari database
              $imgSql = "SELECT gambar FROM products WHERE id = $id";
              $imgResult = $conn->query($imgSql);
              $imgRow = $imgResult && $imgResult->num_rows > 0 ? $imgResult->fetch_assoc() : null;
              $gambar = $imgRow && !empty($imgRow['gambar']) ? $imgRow['gambar'] : 'https://via.placeholder.com/60x60?text=No+Image';
              $product = $products[$id];
              $subtotal = $product['harga'] * $qty;
              $total += $subtotal;
          ?>
            <tr>
              <td>
          <img src="uploads/<?= htmlspecialchars($gambar) ?>" alt="Gambar Produk" class="img-fluid" style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
              </td>
              <td><?= htmlspecialchars($product['nama_produk']) ?></td>
              <td>Rp<?= number_format($product['harga'], 0, ',', '.') ?></td>
              <td style="width:110px;">
          <input type="number" name="qty[<?= $id ?>]" value="<?= $qty ?>" min="1" class="form-control text-center" style="width:80px; margin:auto;">
              </td>
              <td>Rp<?= number_format($subtotal, 0, ',', '.') ?></td>
              <td>
          <a href="?remove=<?= $id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus produk ini dari keranjang?')">
            <i class="bi bi-trash"></i> <span class="d-none d-md-inline">Hapus</span>
          </a>
              </td>
            </tr>
          <?php
            endforeach;
          else:
          ?>
            <tr>
              <td colspan="6" class="text-center text-muted">Keranjang kosong.</td>
            </tr>
          <?php endif; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="4" class="text-end">Total</th>
              <th colspan="2" class="text-primary fs-5">Rp<?= number_format($total, 0, ',', '.') ?></th>
            </tr>
          </tfoot>
        </table>
        </div>
      </div>
      <div class="d-flex flex-column flex-md-row gap-2 mt-3">
        <button type="submit" name="update" class="btn btn-outline-primary">
          <i class="bi bi-arrow-repeat"></i> <span class="d-none d-sm-inline">Update Keranjang</span>
        </button>
        <a href="index.php" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Lanjut Belanja</span>
        </a>
        <?php if ($total > 0): ?>
          <a href="checkout.php" class="btn btn-success ms-md-auto">
            <i class="bi bi-credit-card"></i> <span class="d-none d-sm-inline">Checkout</span>
          </a>
        <?php endif; ?>
      </div>
    </form>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  
</body>
</html>