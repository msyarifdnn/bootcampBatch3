<?php
  include 'database/config.php';

  $sql = "SELECT * FROM products";
  $query = mysqli_query($conn, $sql);

  if (isset($_GET['cari']) && !empty(trim($_GET['cari']))) {
    $cari = mysqli_real_escape_string($conn, $_GET['cari']);
    $sql = "SELECT * FROM products WHERE nama_produk LIKE '%$cari%' OR deskripsi LIKE '%$cari%'";
    $query = mysqli_query($conn, $sql);
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>ARshop | Task 8</title>

  <style>
  body {
    background: #f3f6fa !important;
  }
  .card {
    transition: box-shadow 0.2s, transform 0.2s;
  }
  .card:hover {
    box-shadow: 0 12px 40px rgba(0,0,0,0.18), 0 3px 8px rgba(0,0,0,0.13);
    transform: translateY(-4px) scale(1.02);
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
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active fw-semibold" aria-current="page" href="/">
              <i class="bi bi-house-door-fill me-1"></i>Beranda
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-semibold" href="#">
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
            <a class="nav-link fw-semibold" href="login.php">
              <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

<!-- Search by category -->
<?php
  // Ambil kategori dari GET jika ada
  $selectedCategory = isset($_GET['kategori']) && is_numeric($_GET['kategori']) ? (int)$_GET['kategori'] : '';

  // Query kategori dari database (misal tabel categories)
  $categories = [];
  $catResult = mysqli_query($conn, "SELECT id, kategori FROM products");
  if ($catResult) {
    while ($cat = mysqli_fetch_assoc($catResult)) {
      $categories[] = $cat;
    }
  }

  // Modifikasi query produk jika filter kategori dipilih
  if ($selectedCategory) {
    $sql = "SELECT * FROM products WHERE id = $selectedCategory";
    // Jika juga ada pencarian
    if (isset($_GET['cari']) && !empty(trim($_GET['cari']))) {
      $cari = mysqli_real_escape_string($conn, $_GET['cari']);
      $sql .= " AND (nama_produk LIKE '%$cari%' OR deskripsi LIKE '%$cari%')";
    }
    $query = mysqli_query($conn, $sql);
  }
?>
<div class="container mt-4">
  <div class="row justify-content-between align-items-center">
    <div class="col-md-4">
      <form action="" method="get" class="d-flex">
        <select class="form-select me-2" aria-label="Pilih kategori" name="kategori" onchange="this.form.submit()">
          <option value="">Semua Kategori</option>
          <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $selectedCategory == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['kategori']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($_GET['cari'])): ?>
          <input type="hidden" name="cari" value="<?= htmlspecialchars($_GET['cari']) ?>">
        <?php endif; ?>
        <button class="btn btn-outline-primary" type="submit">Filter</button>
      </form>
    </div>
  </div>
</div>

<!-- Produk -->
<div class="container mt-5">
  <div class="row g-4">
    <?php while($row = mysqli_fetch_assoc($query)): ?>
      <div class="col-md-4 col-sm-6">
        <div class="card h-100 shadow-lg border-0" style="background: #fff; border-radius: 18px; box-shadow: 0 6px 32px rgba(0,0,0,0.12), 0 1.5px 4px rgba(0,0,0,0.10);">
          <img src="uploads/<?= htmlspecialchars($row['gambar'] ?? 'https://via.placeholder.com/300x200?text=No+Image') ?>" class="card-img-top" alt="<?= htmlspecialchars($row['nama_produk']) ?>" style="height:200px;object-fit:cover; border-top-left-radius: 18px; border-top-right-radius: 18px;">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title"><?= htmlspecialchars($row['nama_produk']) ?></h5>
            <p class="card-text text-muted" style="min-height:48px;"><?= htmlspecialchars($row['deskripsi']) ?></p>
            <div class="mt-auto">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-primary">Rp<?= number_format($row['harga'],0,',','.') ?></span>
                <small class="text-secondary">Stok: <?= (int)$row['stok'] ?> pcs</small>
              </div>
              <a href="#" class="btn btn-success w-100"><i class="bi bi-cart-plus me-1"></i>Keranjang</a>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>