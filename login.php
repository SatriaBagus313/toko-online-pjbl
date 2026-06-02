<?php
session_start();

require_once 'src/Auth.php';

use App\Auth;

$auth = new Auth(
    __DIR__ . '/data/users.json'
);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    try {

        // Proses login
        $user = $auth->login(
            $_POST['email'],
            $_POST['password']
        );

        // Simpan session user
        $_SESSION['user'] = $user;

        // Redirect berdasarkan role
        if ($user['role'] == 'admin') {

            header("Location: admin.php");

        } else {

            header("Location: index.php");
        }

        exit;

    } catch (Exception $e) {

        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Login</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light py-5">

<div class="container">

    <div class="row justify-content-center">

        <div class="col-md-4">

            <div class="card shadow">

                <div class="card-body">

                    <h4 class="text-center mb-4">
                        Login
                    </h4>

                    <!-- Error -->
                    <?php if (!empty($error)): ?>

                        <div class="alert alert-danger">

                            <?= htmlspecialchars($error) ?>

                        </div>

                    <?php endif; ?>

                    <!-- Pesan sukses -->
                    <?php if (isset($_GET['msg'])): ?>

                        <div class="alert alert-success">

                            <?= htmlspecialchars($_GET['msg']) ?>

                        </div>

                    <?php endif; ?>

                    <!-- Form Login -->
                    <form method="POST">

                        <input
                            type="email"
                            name="email"
                            class="form-control mb-2"
                            placeholder="Email"
                            required>

                        <input
                            type="password"
                            name="password"
                            class="form-control mb-3"
                            placeholder="Password"
                            required>

                        <button class="btn btn-success w-100">

                            Login

                        </button>

                    </form>

                    <p class="text-center mt-3">

                        Belum punya akun?

                        <a href="register.php">

                            Daftar

                        </a>

                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>