<?php
// variabel
$nama       = $_POST["namaProduk"];
$harga      = $_POST["harga"];
$deskripsi  = $_POST["deskripsi"];

$gambar = $_FILES['gambar']['name'];
$tmp_name = $_FILES['gambar']['tmp_name'];

// Ambil ekstensi file secara aman
$format = strtolower(pathinfo($gambar, PATHINFO_EXTENSION));

// Nama baru yang unik
$newname = 'Produk' . time() . '.' . $format;

// Format yang diizinkan
$f_diizinkan = array('jpg','jpeg','png','webp');

// Cek format
if (!in_array($format, $f_diizinkan)) {
    echo '<script>alert("Format tidak diizinkan")</script>';
    echo '<script>window.location="index.html"</script>';
} else {
    // Pindahkan file
    if (move_uploaded_file($tmp_name, 'image/' . $newname)) {
        echo "Gambar berhasil diunggah!";
    } else {
        echo "Gagal mengunggah gambar.";
    }
}

// validasi
$errors = [];

if (empty($nama)) {
  $errors[] = "Nama produk harus diisi.";
}
if (!is_numeric($harga) || $harga <= 0) {
  $errors[] = "Harga harus berupa angka lebih dari 0.";
}
if (empty($deskripsi)) {
  $errors[] = "Deskripsi harus diisi.";
}
if (empty($gambar)) {
  $errors[] = "URL gambar harus diisi.";
}

if (!empty($errors)) {
  foreach ($errors as $error) {
    echo "<div class='alert alert-danger'>$error</div>";
  }
  exit;
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task 7 Bootcamp Batch 3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
      <div class="card mt-5" style="width: 18rem;">
        <img src="image/<?= $newname ?>" class="card-img-top" alt="...">
        <div class="card-body">
          <h5 class="card-title"><?= $nama ?></h5>
          <p class="card-text"><?= $deskripsi ?></p>
          <div class="d-flex flex-column">
            <small class="text-body-secondary"><?= "Rp " . number_format($harga, 0, ',', '.'); ?></small>
            <button class="btn btn-success mt-3">Beli dah...</button>
          </div>
        </div>
      </div>
      <div class="mt-5">
        <a href="index.html"><button class="btn btn-secondary">Kembali</button></a>
      </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>