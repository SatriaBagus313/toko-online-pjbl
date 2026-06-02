<?php
session_start();

// =========================================
// PROTEKSI ADMIN
// =========================================
if (
    !isset($_SESSION['user']) ||
    $_SESSION['user']['role'] !== 'admin'
) {
    die("Akses Ditolak! Anda bukan admin.");
}

require_once 'src/AdminManager.php';
require_once 'src/Catalog.php';

// =========================================
// INISIALISASI CLASS
// =========================================
$adminManager = new App\AdminManager(
    __DIR__ . '/data/orders.json'
);

$katalog = new App\Catalog(
    __DIR__ . '/data/products.json'
);

// =========================================
// CRUD PRODUK
// =========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi_produk'])) {

    if ($_POST['aksi_produk'] == 'simpan') {

        $katalog->saveProduct(
            $_POST['kode'],
            $_POST['nama'],
            $_POST['harga'],
            $_POST['stok']
        );

        $success = "Produk berhasil ditambahkan.";

    } elseif ($_POST['aksi_produk'] == 'hapus') {

        $katalog->deleteProduct($_POST['kode']);

        $success = "Produk berhasil dihapus.";
    }
}

// =========================================
// UPDATE STATUS PESANAN
// =========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_pesanan'])) {

    // Status yang diperbolehkan
    $statusValid = [
        'Menunggu Pembayaran',
        'Diproses',
        'Dikirim',
        'Selesai',
        'Dibatalkan'
    ];

    // Validasi status
    if (!in_array($_POST['status_baru'], $statusValid)) {

        $error = "Status tidak valid!";

    } else {

        try {

            $adminManager->updateStatusPesanan(
                $_POST['id_pesanan'],
                $_POST['status_baru']
            );

            $success = "Status pesanan berhasil diperbarui.";

        } catch (Exception $e) {

            $error = $e->getMessage();
        }
    }
}

// =========================================
// AMBIL DATA
// =========================================
$produkList = $katalog->getAllProducts();

$pesananList = array_reverse(
    $adminManager->getAllOrders()
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Panel Admin</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark mb-4">

    <div class="container">

        <span class="navbar-brand">
            Panel Admin
        </span>

        <div>

            <a href="index.php" class="btn btn-outline-light btn-sm">
                Ke Website
            </a>

            <a href="logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>

        </div>

    </div>

</nav>

<div class="container">

    <!-- ALERT -->
    <?php if(isset($success)): ?>

        <div class="alert alert-success">
            <?= $success ?>
        </div>

    <?php endif; ?>

    <?php if(isset($error)): ?>

        <div class="alert alert-danger">
            <?= $error ?>
        </div>

    <?php endif; ?>

    <div class="row">

        <!-- CRUD PRODUK -->
        <div class="col-md-4">

            <div class="card shadow-sm mb-4">

                <div class="card-header bg-primary text-white">
                    Tambah Produk
                </div>

                <div class="card-body">

                    <form method="POST">

                        <input
                            type="hidden"
                            name="aksi_produk"
                            value="simpan">

                        <input
                            type="text"
                            name="kode"
                            class="form-control mb-2"
                            placeholder="Kode Produk"
                            required>

                        <input
                            type="text"
                            name="nama"
                            class="form-control mb-2"
                            placeholder="Nama Produk"
                            required>

                        <input
                            type="number"
                            name="harga"
                            class="form-control mb-2"
                            placeholder="Harga"
                            required>

                        <input
                            type="number"
                            name="stok"
                            class="form-control mb-3"
                            placeholder="Stok"
                            required>

                        <button class="btn btn-primary w-100">
                            Simpan Produk
                        </button>

                    </form>

                </div>

            </div>

            <!-- LIST PRODUK -->
            <ul class="list-group shadow-sm">

                <?php foreach ($produkList as $kode => $item): ?>

                    <li class="list-group-item d-flex justify-content-between align-items-center">

                        <div>

                            <strong>
                                <?= $item['nama'] ?>
                            </strong>

                            <br>

                            <small>
                                Stok: <?= $item['stok'] ?>
                            </small>

                        </div>

                        <form method="POST">

                            <input
                                type="hidden"
                                name="aksi_produk"
                                value="hapus">

                            <input
                                type="hidden"
                                name="kode"
                                value="<?= $kode ?>">

                            <button class="btn btn-danger btn-sm">
                                Hapus
                            </button>

                        </form>

                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

        <!-- DAFTAR PESANAN -->
        <div class="col-md-8">

            <div class="card shadow-sm">

                <div class="card-header bg-dark text-white">
                    Daftar Pesanan Masuk
                </div>

                <div class="card-body p-0">

                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>ID</th>
                                <th>Alamat</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Ubah</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if (!empty($pesananList)): ?>

                            <?php foreach ($pesananList as $order): ?>

                                <tr>

                                    <td>
                                        <?= $order['id_pesanan'] ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($order['alamat']) ?>
                                    </td>

                                    <td>
                                        Rp <?= number_format($order['total_bayar'], 0, ',', '.') ?>
                                    </td>

                                    <td>

                                        <span class="badge bg-secondary">

                                            <?= $order['status'] ?>

                                        </span>

                                    </td>

                                    <td>

                                        <form method="POST" class="d-flex gap-1">

                                            <input
                                                type="hidden"
                                                name="id_pesanan"
                                                value="<?= $order['id_pesanan'] ?>">

                                            <select
                                                name="status_baru"
                                                class="form-select form-select-sm">

                                                <option value="Menunggu Pembayaran">
                                                    Menunggu
                                                </option>

                                                <option value="Diproses">
                                                    Diproses
                                                </option>

                                                <option value="Dikirim">
                                                    Dikirim
                                                </option>

                                                <option value="Selesai">
                                                    Selesai
                                                </option>

                                                <option value="Dibatalkan">
                                                    Dibatalkan
                                                </option>

                                            </select>

                                            <button class="btn btn-success btn-sm">
                                                OK
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="5" class="text-center p-3">
                                    Belum ada pesanan.
                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>