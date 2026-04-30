<?php
// Memulai session untuk menyimpan status login
session_start();

// Sertakan koneksi database
include '../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan email
    $query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password yang di-hash
        if (password_verify($password, $row['password_hash'])) {
            // Set session data
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap'];

            echo "<script>
                    alert('Login Berhasil! Selamat datang " . $row['nama_lengkap'] . "');
                    window.location='../index.php';
                  </script>";
            exit;
        } else {
            $message = "<script>alert('Kata sandi salah!');</script>";
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
    <title>Halaman Login Bunda</title>
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
            background-image: url('/squashy/assets/images/tbg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .top-nav {
            padding: 20px 50px;
            font-weight: bold;
            font-size: 1.2rem;
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

        .login-card {
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
            z-index: 10;
        }

        .logo-top img {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 4px 4px rgba(0, 0, 0, 0.1));
        }

        .login-card h2 {
            font-family: 'Nunito', sans-serif;
            font-size: 26px;
            margin-bottom: 5px;
            color: #000;
        }

        .subtitle {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.4;
            font-weight: 700;
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
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-wrapper input:focus {
            border-color: var(--button-orange);
            box-shadow: 0 0 8px rgba(255, 133, 45, 0.2);
        }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-style: normal;
        }

        .btn-masuk {
            width: 100%;
            padding: 16px;
            background-color: var(--button-orange);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 800;
            font-size: 15px;
            cursor: pointer;
            margin-top: 5px;
            box-shadow: 0 4px 0 #d66a1e;
            transition: transform 0.1s;
        }

        .btn-masuk:active {
            transform: translateY(3px);
            box-shadow: 0 1px 0 #d66a1e;
        }

        .forgot-link {
            display: block;
            margin-top: 15px;
            font-size: 12px;
            color: #777;
            text-decoration: none;
        }

        .register-text {
            margin-top: 30px;
            font-size: 13px;
            color: #444;
        }

        .register-text a {
            color: #00A3FF;
            text-decoration: none;
            font-weight: bold;
        }

        footer {
            background: rgba(255, 255, 255, 0.8);
            padding: 15px 50px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .info-icon {
            border: 2px solid #000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin-right: 10px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <?php echo $message; ?>

    <!-- <div class="top-nav"><a href="../index.php">Kembali</a></div> -->

    <?= require_once __DIR__ . "/../components/layout/header.php" ?>
    <main>
        <div class="login-card">
            <div class="logo-top">
                <img src="../assets/images/logo.png" alt="Squashy Logo">
            </div>

            <h2>HALAMAN LOGIN</h2>
            <p class="subtitle">Halo, Bunda!<br>Silahkan Masuk</p>

            <form action="" method="POST">
                <div class="input-wrapper">
                    <i>✉️</i>
                    <input type="email" name="email" placeholder="Dummy123@gmail.com" required>
                </div>

                <div class="input-wrapper">
                    <i>🔒</i>
                    <input type="password" name="password" placeholder="Masukkan Kata Sandi" required>
                </div>

                <button type="submit" class="btn-masuk">MASUK SEKARANG</button>

                <a href="#" class="forgot-link">Lupa Kata Sandi?</a>

                <p class="register-text">
                    Belum Punya Akun? <a href="register.php">Daftar di sini</a>
                </p>
            </form>
        </div>
    </main>



</body>

</html>