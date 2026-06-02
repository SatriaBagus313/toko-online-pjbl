<?php
session_start();

require_once 'src/Catalog.php';

$katalog = new App\Catalog(__DIR__ . '/data/products.json');

// Fitur pencarian
$keyword = $_GET['cari'] ?? '';
$produkList = $katalog->searchProduct($keyword);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Online</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">

            <a class="navbar-brand" href="#">Toko Online</a>

            <div class="d-flex align-items-center text-white">

                <?php if (isset($_SESSION['user'])): ?>

                    Halo, <?= $_SESSION['user']['nama'] ?>!

                    <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                        <a href="admin.php" class="btn btn-warning btn-sm ms-3">
                            Panel Admin
                        </a>
                    <?php endif; ?>

                    <a href="logout.php" class="btn btn-outline-danger btn-sm ms-2">
                        Logout
                    </a>

                <?php else: ?>

                    <a href="login.php" class="btn btn-primary btn-sm">
                        Login
                    </a>

                <?php endif; ?>

            </div>
        </div>
    </nav>

    <div class="container">

        <!-- Form Pencarian -->
        <div class="row mb-3">
            <div class="col-md-6">

                <form method="GET" class="d-flex">

                    <input
                        type="text"
                        name="cari"
                        class="form-control me-2"
                        placeholder="Cari nama produk..."
                        value="<?= htmlspecialchars($keyword) ?>">

                    <button class="btn btn-outline-primary">
                        Cari
                    </button>

                </form>

            </div>
        </div>

        <!-- Form Checkout -->
        <form action="proses.php" method="POST">

            <div class="row">

                <!-- Tabel Produk -->
                <div class="col-md-8">

                    <table class="table table-bordered bg-white shadow-sm">

                        <tr class="table-dark">
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Qty</th>
                        </tr>

                        <?php foreach ($produkList as $kode => $item): ?>

                            <tr>

                                <td><?= $kode ?></td>

                                <td><?= $item['nama'] ?></td>

                                <td>
                                    Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                                </td>

                                <td><?= $item['stok'] ?></td>

                                <td>

                                    <?php if ($item['stok'] > 0): ?>

                                        <input
                                            type="number"
                                            name="qty[<?= $kode ?>]"
                                            min="0"
                                            max="<?= $item['stok'] ?>"
                                            value="0"
                                            class="form-control form-control-sm">

                                    <?php else: ?>

                                        <button class="btn btn-danger btn-sm" disabled>
                                            Stok Habis
                                        </button>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </table>

                </div>

                <!-- Checkout -->
                <div class="col-md-4">

                    <div class="card shadow-sm">

                        <div class="card-body">

                            <h5 class="mb-3">Checkout</h5>

                            <input
                                type="email"
                                name="email"
                                class="form-control mb-2"
                                placeholder="Email Anda"
                                value="<?= $_SESSION['user']['email'] ?? '' ?>"
                                required>

                            <textarea
                                name="alamat"
                                class="form-control mb-3"
                                placeholder="Alamat Pengiriman Lengkap"
                                required></textarea>

                            <button class="btn btn-success w-100">
                                Bayar Sekarang
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</body>

</html>