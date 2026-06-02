<?php
session_start();

require_once 'src/Checkout.php';

use App\Checkout;

$fileProduk = __DIR__ . '/data/products.json';
$filePesanan = __DIR__ . '/data/orders.json';

// Jika bukan POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: index.php");
    exit;
}

$email = $_POST['email'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$inputQty = $_POST['qty'] ?? [];

// Ambil hanya qty > 0
$keranjang = array_filter($inputQty, function($qty) {

    return (int)$qty > 0;
});

try {

    // Inisialisasi checkout
    $checkoutManager = new Checkout(
        $fileProduk,
        $filePesanan
    );

    // Proses checkout
    $nota = $checkoutManager->prosesCheckout(
        $email,
        $alamat,
        $keranjang
    );

    // Simpan nota ke session
    $_SESSION['nota'] = $nota;

    // Redirect ke halaman nota
    header("Location: nota.php");
    exit;

} catch (Exception $e) {

    $_SESSION['error'] = $e->getMessage();

    header("Location: index.php");
    exit;
}
?>