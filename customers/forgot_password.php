<?php
session_start();
// Sertakan koneksi database
include '../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_pass = $_POST['password'];

    // Menggunakan password_hash agar sinkron dengan login.php
    $password_secure = hash('sha256', $new_pass);

    // Cek apakah email ada
    $cek = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        // Update password ke tabel user
        $update = mysqli_query($conn, "UPDATE user SET password_hash='$password_secure' WHERE email='$email'");

        if ($update) {
            $message = "<script>alert('Password berhasil diubah! Silakan login kembali.'); window.location='login.php';</script>";
        }
    } else {
        $message = "<script>alert('Email tidak ditemukan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Squashy</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@800&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent-yellow: #FFD700;
            --button-orange: #FF852D;
            --input-border: #E0E0E0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }

        body {
            background-image: url('../assets/images/bg.png');
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-nav {
            padding: 20px 50px;
            font-weight: bold;
        }

        .top-nav a {
            text-decoration: none;
            color: #333;
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 400px;
            padding: 60px 35px 40px;
            border-radius: 30px;
            border: 5px solid var(--accent-yellow);
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .logo-top {
            position: absolute;
            top: -65px;
            left: 50%;
            transform: translateX(-50%);
            width: 160px;
        }

        .logo-top img {
            width: 100%;
            filter: drop-shadow(0 4px 4px rgba(0, 0, 0, 0.1));
        }

        h2 {
            font-family: 'Nunito', sans-serif;
            margin-bottom: 20px;
        }

        .input-wrapper {
            margin-bottom: 15px;
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 15px 14px 45px;
            border: 2px solid var(--input-border);
            border-radius: 50px;
            outline: none;
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background-color: var(--button-orange);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 0 #d66a1e;
        }
    </style>
</head>

<body>
    <?php echo $message; ?>

    <!-- <div class="top-nav"><a href="login.php">Kembali ke Login</a></div> -->
    <?= require_once __DIR__ . "/../components/layout/header.php" ?>

    <main>
        <div class="card">
            <div class="logo-top">
                <img src="../assets/images/logo.png" alt="Logo">
            </div>

            <h2>RESET PASSWORD</h2>

            <form method="POST">
                <div class="input-wrapper">
                    <i>✉️</i>
                    <input type="email" name="email" placeholder="Masukkan Email Anda" required>
                </div>
                <div class="input-wrapper">
                    <i>🔒</i>
                    <input type="password" name="password" placeholder="Password Baru" required>
                </div>
                <button type="submit" class="btn-submit">RESET PASSWORD</button>
            </form>
        </div>
    </main>
</body>

</html>