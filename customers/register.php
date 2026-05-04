<?php
session_start();
// Sertakan koneksi database
include '../config/database.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $konfirmasi_pass = $_POST['konfirmasi_password'];

    // Validasi sederhana: Cek kecocokan password
    if ($pass !== $konfirmasi_pass) {
        $message = "<script>alert('Kata sandi tidak cocok!');</script>";
    } else {
        // Cek apakah email sudah terdaftar
        $cek_email = mysqli_query($conn, "SELECT email FROM user WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            $message = "<script>alert('Email sudah terdaftar!');</script>";
        } else {
            // Hash password untuk keamanan
            $password_secure = hash('sha256', $pass);

            // Insert ke tabel user (sesuai struktur squashy_db.sql)
            $query = "INSERT INTO user (nama_lengkap, email, password_hash) VALUES ('$nama', '$email', '$password_secure')";

            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location='login.php';</script>";
            } else {
                $message = "<script>alert('Gagal mendaftar: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - Squashy</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&family=Nunito:wght@800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-green: #62C974;
            --bg-light-green: #E8F5E9;
            --text-dark: #333;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }

        body {
            background-image: url('../assets/images/tbg.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }


        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 450px;
            padding: 60px 30px 40px;
            border-radius: 30px;
            border: 6px solid var(--primary-green);
            text-align: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .logo-top {
            position: absolute;
            top: -65px;
            left: 50%;
            transform: translateX(-50%);
            width: 180px;
            z-index: 20;
        }

        .logo-top img {
            width: 100%;
            height: auto;
            display: block;
            filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.1));
        }

        h2 {
            font-family: 'Nunito', sans-serif;
            font-size: 28px;
            margin: 10px 0 5px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .input-group {
            margin-bottom: 12px;
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 10px 15px 10px 55px;
            border: 2px solid #ccc;
            border-radius: 25px;
            outline: none;
            font-size: 14px;
        }

        .input-group .icon-box {
            position: absolute;
            left: -1px;
            top: -1px;
            bottom: -1px;
            width: 45px;
            background: var(--bg-light-green);
            border: 2px solid var(--primary-green);
            border-radius: 25px 0 0 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            z-index: 2;
        }

        .terms {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 11px;
            margin: 10px 0;
            font-weight: bold;
        }

        .btn-daftar {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-green);
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 0 #4a9e57;
            text-transform: uppercase;
            transition: 0.2s;
        }

        .btn-daftar:active {
            transform: translateY(2px);
            box-shadow: 0 2px 0 #4a9e57;
        }

        .divider {
            margin: 20px 0;
            position: relative;
            font-size: 12px;
            color: #777;
            font-weight: bold;
        }

        .divider::before,
        .divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #ccc;
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            cursor: pointer;
            transition: 0.2s;
        }

        footer {
            background: white;
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
        }
    </style>
</head>

<body>
    <?php echo $message; ?>

    <!-- <header><a href="login.php">Login</a></header> -->

    <?= require_once __DIR__ . "/../components/layout/header.php" ?>

    <main>
        <div class="register-card">
            <div class="logo-top">
                <img src="../assets/images/logo.png" alt="Squashy Logo">
            </div>

            <h2>DAFTAR AKUN BARU</h2>
            <p class="subtitle">Selamat Datang di SQUASHY!<br>Ayo Bergabung!</p>

            <form action="" method="POST">
                <div class="input-group">
                    <div class="icon-box">👤</div>
                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
                </div>
                <div class="input-group">
                    <div class="icon-box">✉️</div>
                    <input type="email" name="email" placeholder="Dummy123@gmail.com" required>
                </div>
                <div class="input-group">
                    <div class="icon-box">🔒</div>
                    <input type="password" name="password" placeholder="Buat Kata Sandi" required>
                </div>
                <div class="input-group">
                    <div class="icon-box">🔒</div>
                    <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Kata Sandi" required>
                </div>
                <div class="input-group">
                    <div class="icon-box">📅</div>
                    <input type="text" onfocus="(this.type='date')" onblur="(this.type='text')" placeholder="Tanggal Lahir" required>
                </div>

                <div class="terms">
                    <input type="checkbox" id="agree" required>
                    <label for="agree">Saya menyetujui Syarat dan Ketentuan</label>
                </div>

                <button type="submit" class="btn-daftar">DAFTAR SEKARANG</button>
            </form>

            <div class="divider">Atau Daftar dengan</div>

            <!-- <div class="social-login">
                <img src="https://cdn-icons-png.flaticon.com/512/300/300221.png" class="social-icon" alt="Google">
                <img src="https://cdn-icons-png.flaticon.com/512/124/124010.png" class="social-icon" alt="Facebook">
            </div> -->
        </div>
    </main>


</body>

</html>