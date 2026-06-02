<?php
session_start();

if (!isset($_SESSION['nota'])) {

    header("Location: index.php");
    exit;
}

$nota = $_SESSION['nota'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Nota Pembayaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light py-5">

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow border-success">

                <div class="card-header bg-success text-white text-center">

                    <h3>
                        Transaksi Berhasil
                    </h3>

                </div>

                <div class="card-body">

                    <table class="table">

                        <tr>
                            <th>ID Pesanan</th>
                            <td><?= $nota['id_pesanan'] ?></td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td><?= htmlspecialchars($nota['email']) ?></td>
                        </tr>

                        <tr>
                            <th>Alamat</th>
                            <td><?= htmlspecialchars($nota['alamat']) ?></td>
                        </tr>

                        <tr>
                            <th>Status</th>
                            <td><?= $nota['status'] ?></td>
                        </tr>

                    </table>

                    <div class="text-center bg-light p-4 rounded">

                        <h5>Total Pembayaran</h5>

                        <h1 class="text-success">

                            Rp <?= number_format(
                                $nota['total_bayar'],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </h1>

                    </div>

                    <div class="text-center mt-4">

                        <a href="index.php"
                           class="btn btn-primary">

                            Kembali ke Katalog

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>